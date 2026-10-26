<?php

use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\ClinicService;
use App\Models\InformedConsent;
use App\Models\Patient;
use App\Models\Promo;
use App\Models\Room;
use App\Models\Therapist;
use App\Models\Visit;
use App\Services\InvoiceWhatsappMessage;
use App\Services\NewPatientAdminFee;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);
    $this->patient = Patient::create(['full_name' => 'Pasien Revisi', 'whatsapp' => '081234567890', 'birth_date' => '1990-01-01']);
    $this->therapist = Therapist::create(['full_name' => 'Terapis', 'email' => 't@uji.test', 'password' => Hash::make('x-uji'), 'status' => 'active']);
});

function crVisit(?string $room = 'VIP 1', ?Patient $patient = null): Visit
{
    return Visit::create([
        'patient_id' => ($patient ?? Patient::firstOrFail())->id, 'therapist_id' => Therapist::firstOrFail()->id, 'therapist' => 'Terapis',
        'room_name' => $room, 'visit_date' => ClinicTime::today(), 'status' => 'scheduled',
    ]);
}

function crCheckout($test, array $lines, array $extra = [])
{
    $payload = array_merge([
        'patient_id' => Patient::firstOrFail()->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0,
        'item_type' => [], 'description' => [], 'quantity' => [], 'unit_price' => [], 'inventory_item_id' => [], 'clinic_service_id' => [],
        'item_category' => [], 'price_reason' => [], 'reference_type' => [], 'reference_id' => [],
    ], $extra);

    foreach ($lines as $line) {
        $payload['item_type'][] = 'service';
        $payload['description'][] = $line['description'];
        $payload['quantity'][] = $line['quantity'] ?? 1;
        $payload['unit_price'][] = $line['price'];
        $payload['inventory_item_id'][] = '';
        $payload['clinic_service_id'][] = '';
        $payload['item_category'][] = $line['category'] ?? 'other';
        $payload['price_reason'][] = $line['reason'] ?? '';
        $payload['reference_type'][] = $line['ref_type'] ?? '';
        $payload['reference_id'][] = $line['ref_id'] ?? '';
    }

    return $test->post('/admin/cashier/checkout', $payload);
}

function crBillableRoom(string $name = 'VIP 1', float $rate = 100000, string $unit = 'per_session'): Room
{
    $room = Room::findByName($name) ?? Room::create(['name' => $name]);
    $room->update(['is_billable' => true, 'rate' => $rate, 'rate_unit' => $unit, 'status' => 'active']);

    return $room->fresh();
}

/** Registrasi pasien baru lewat form admin (jalur nyata); tagihan administrasi dibuat otomatis. */
function crRegister($test, array $overrides = []): Patient
{
    $test->post('/admin/patients', array_merge([
        'full_name' => 'Pasien Registrasi', 'whatsapp' => '0812'.random_int(10000000, 99999999), 'registration_token' => (string) Str::uuid(),
    ], $overrides))->assertSessionHasNoErrors();

    return Patient::latest('id')->firstOrFail();
}

// ======================= REQ 3: MASTER RUANGAN & BIAYA RUANGAN =======================

it('master ruangan di-seed dari daftar lama dan semuanya NON-billable tanpa tarif', function () {
    expect(Room::count())->toBe(10)
        ->and(Room::where('is_billable', true)->count())->toBe(0)
        ->and(Room::whereNotNull('rate')->count())->toBe(0)
        ->and(Room::whereNotNull('rate_unit')->count())->toBe(0)
        ->and(Room::pluck('name')->all())->toContain('VIP 1', 'VIP 2', 'R. GYM', 'R. Studio Pilates');
});

it('dropdown ruangan di booking dan visit memakai master, nilai lama tetap tampil', function () {
    Room::create(['name' => 'Ruang Baru']);
    Room::where('name', 'R.2.5')->update(['status' => 'inactive']);

    $this->get('/admin/bookings/create')->assertOk()->assertSee('Ruang Baru')->assertDontSee('>R.2.5<', false);

    $visit = crVisit('R.2.5');                         // ruangan nonaktif tetapi masih tersimpan di visit lama
    $this->get("/admin/visits/{$visit->id}/edit")->assertOk()->assertSee('R.2.5');
});

it('halaman master ruangan: tambah default non-billable, berbayar wajib tarif dan satuan, nama tidak berubah', function () {
    $this->get('/admin/rooms')->assertOk()->assertSee('default non-billable');

    $this->post('/admin/rooms', ['name' => 'R. Baru', 'status' => 'active'])->assertRedirect('/admin/rooms');
    $room = Room::where('name', 'R. Baru')->firstOrFail();
    expect($room->is_billable)->toBeFalse()->and($room->rate)->toBeNull();

    $this->post('/admin/rooms', ['name' => 'R. Baru', 'status' => 'active'])->assertSessionHasErrors('name');   // unik

    $this->post("/admin/rooms/{$room->id}/update", ['status' => 'active', 'is_billable' => 1, 'rate' => 0])->assertSessionHasErrors('rate');
    $this->post("/admin/rooms/{$room->id}/update", ['status' => 'active', 'is_billable' => 1, 'rate' => 50000])->assertSessionHasErrors('rate');   // satuan tidak diasumsikan
    expect($room->fresh()->is_billable)->toBeFalse();

    $this->post("/admin/rooms/{$room->id}/update", ['name' => 'Nama Diubah', 'status' => 'active', 'is_billable' => 1, 'rate' => 50000, 'rate_unit' => 'per_hour'])->assertRedirect('/admin/rooms');

    $room->refresh();
    expect($room->is_billable)->toBeTrue()->and((float) $room->rate)->toBe(50000.0)->and($room->rate_unit)->toBe('per_hour')
        ->and($room->name)->toBe('R. Baru')->and($room->is_chargeable)->toBeTrue();
});

it('TIDAK ada penagihan otomatis: booking/visit di ruangan berbayar tidak membuat invoice atau baris biaya', function () {
    crBillableRoom();

    $this->post('/admin/bookings/create', [
        'patient_id' => $this->patient->id, 'therapist_id' => $this->therapist->id, 'room_name' => 'VIP 1', 'full_name' => 'Pasien Revisi',
        'whatsapp' => '081234567890', 'service' => 'Uji', 'booking_date' => ClinicTime::today(), 'booking_time' => '10:00', 'status' => 'confirmed',
    ])->assertRedirect();
    $this->post('/admin/visits', ['patient_id' => $this->patient->id, 'therapist_id' => $this->therapist->id, 'room_name' => 'VIP 1', 'visit_date' => ClinicTime::today(), 'status' => 'scheduled'])->assertRedirect('/admin/visits');

    expect(Billing::count())->toBe(0)->and(BillingItem::count())->toBe(0);
});

it('kasir hanya menyarankan biaya ruangan untuk ruangan berbayar yang tarif dan satuannya lengkap', function () {
    $this->get('/admin/cashier')->assertOk()->assertViewHas('roomRates', fn ($rates) => $rates->isEmpty());

    Room::where('name', 'VIP 1')->update(['is_billable' => true, 'rate' => 100000]);   // tanpa satuan: belum lengkap
    $this->get('/admin/cashier')->assertViewHas('roomRates', fn ($rates) => $rates->isEmpty());

    crBillableRoom('VIP 1', 100000, 'per_session');
    $this->get('/admin/cashier')->assertViewHas('roomRates', fn ($rates) => $rates->has('vip 1') && $rates['vip 1']['rate'] === 100000.0);
});

it('biaya ruangan menjadi baris tersendiri: treatment tetap, ruangan terpisah, tidak didiskon, snapshot tarif tersimpan', function () {
    crBillableRoom('VIP 1', 100000, 'per_session');
    $visit = crVisit('VIP 1');
    Promo::create(['code' => 'D10', 'name' => '10%', 'discount_type' => 'percent', 'discount_value' => 10, 'status' => 'active']);

    crCheckout($this, [
        ['description' => 'Fisioterapi', 'price' => 300000, 'category' => 'program'],
        ['description' => 'Biaya ruangan VIP 1', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $visit->id],
    ], ['promo_id' => Promo::first()->id, 'visit_id' => $visit->id]);

    $billing = Billing::with('items')->firstOrFail();
    $treatment = $billing->items->firstWhere('item_category', 'program');
    $room = $billing->items->firstWhere('item_category', 'room_charge');

    expect((float) $treatment->unit_price)->toBe(300000.0)                      // harga treatment tidak berubah
        ->and((float) $treatment->discount_amount)->toBe(30000.0)               // diskon hanya program
        ->and((float) $room->line_total)->toBe(100000.0)
        ->and($room->is_discount_eligible)->toBeFalse()
        ->and((float) $room->discount_amount)->toBe(0.0)
        ->and($room->reference_type)->toBe('visit_room')->and($room->reference_id)->toBe($visit->id)
        ->and($room->rate_unit)->toBe('per_session')
        ->and((float) $billing->amount)->toBe(370000.0);                         // 270.000 + 100.000

    // Snapshot: mengubah tarif master sesudahnya tidak mengubah invoice yang sudah terbit.
    Room::where('name', 'VIP 1')->update(['rate' => 250000]);
    expect((float) $room->fresh()->unit_price)->toBe(100000.0);
});

it('biaya ruangan ditolak untuk ruangan non-billable, tanpa tarif lengkap, atau visit pasien lain', function () {
    $visit = crVisit('VIP 1');                                                   // VIP 1 non-billable
    crCheckout($this, [['description' => 'Biaya ruangan', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $visit->id]])->assertSessionHasErrors('checkout');
    expect(Billing::count())->toBe(0);

    crBillableRoom('VIP 1');
    $other = Patient::create(['full_name' => 'Lain', 'whatsapp' => '08555']);
    $otherVisit = crVisit('VIP 1', $other);
    crCheckout($this, [['description' => 'Biaya ruangan', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $otherVisit->id]])->assertSessionHasErrors('checkout');

    expect(Billing::count())->toBe(0);
});

it('harga biaya ruangan berbeda dari tarif master wajib beralasan', function () {
    crBillableRoom('VIP 1', 100000);
    $visit = crVisit('VIP 1');

    crCheckout($this, [['description' => 'Biaya ruangan', 'price' => 80000, 'ref_type' => 'visit_room', 'ref_id' => $visit->id]])->assertSessionHasErrors('price_reason');
    crCheckout($this, [['description' => 'Biaya ruangan', 'price' => 80000, 'reason' => 'Disetujui owner', 'ref_type' => 'visit_room', 'ref_id' => $visit->id]])->assertSessionHasNoErrors();

    expect((float) Billing::firstOrFail()->amount)->toBe(80000.0);
});

it('satuan per jam: jumlah mengikuti kuantitas, tarif satuan tersimpan sebagai snapshot', function () {
    crBillableRoom('VIP 1', 75000, 'per_hour');
    $visit = crVisit('VIP 1');

    crCheckout($this, [['description' => 'Biaya ruangan (2 jam)', 'price' => 75000, 'quantity' => 2, 'ref_type' => 'visit_room', 'ref_id' => $visit->id]]);

    $item = BillingItem::firstOrFail();

    expect((float) $item->line_total)->toBe(150000.0)->and($item->rate_unit)->toBe('per_hour');
});

it('biaya ruangan untuk satu visit tidak ditagih ganda (edit, kirim ulang, invoice lain) dan bebas lagi setelah void', function () {
    crBillableRoom('VIP 1', 100000);
    $visit = crVisit('VIP 1');
    $line = [['description' => 'Biaya ruangan', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $visit->id]];

    crCheckout($this, $line)->assertSessionHasNoErrors();
    crCheckout($this, $line)->assertSessionHasErrors('checkout');                           // invoice kedua untuk visit yang sama

    // Mengubah ruangan/visit setelah invoice terbit tidak menagih ulang diam-diam.
    $this->post("/admin/visits/{$visit->id}/update", ['patient_id' => $this->patient->id, 'therapist_id' => $this->therapist->id, 'room_name' => 'VIP 1', 'visit_date' => ClinicTime::today(), 'status' => 'completed'])->assertRedirect();
    expect(BillingItem::where('reference_type', 'visit_room')->count())->toBe(1);

    // Dua baris yang sama dalam satu invoice juga ditolak.
    $other = crVisit('VIP 1');
    crCheckout($this, [
        ['description' => 'Biaya ruangan A', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $other->id],
        ['description' => 'Biaya ruangan B', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $other->id],
    ])->assertSessionHasErrors('checkout');

    // Setelah invoice pertama di-void, visit itu dapat ditagih lagi (koreksi).
    $first = Billing::firstOrFail();
    $this->post("/admin/billings/{$first->id}/void", ['void_reason' => 'Salah input'])->assertRedirect();
    crCheckout($this, $line)->assertSessionHasNoErrors();

    expect(BillingItem::where('reference_type', 'visit_room')->count())->toBe(2)->and(Billing::count())->toBe(2);
});

// ======================= REQ 7: VERSI TEMPLATE INFORMED CONSENT =======================

function crConsentPayload(array $overrides = []): array
{
    return array_merge([
        'consent_date' => ClinicTime::today(), 'status' => 'signed', 'physiotherapy_name' => 'Khayra Physio', 'treatment_location' => 'Khayra Physio Bandung',
    ], $overrides);
}

it('consent baru memakai template v1 (tidak berubah) dan mencatat staf pencatat', function () {
    expect(config('clinic.consent.active_template_version'))->toBe(1);

    $this->post("/admin/patients/{$this->patient->id}/informed-consent", crConsentPayload())->assertRedirect();

    $consent = InformedConsent::firstOrFail();

    expect($consent->template_version)->toBe(1)->and($consent->recorded_by)->toBe('admin@uji.test');

    $this->get("/admin/informed-consents/{$consent->id}/print")->assertOk()
        ->assertSee('Tanda Tangan Patient / Perwakilan')->assertSee('Tanda Tangan Physiotherapy');
});

it('v1 tetap mewajibkan nama physiotherapy', function () {
    $this->post("/admin/patients/{$this->patient->id}/informed-consent", crConsentPayload(['physiotherapy_name' => '']))->assertSessionHasErrors('physiotherapy_name');

    expect(InformedConsent::count())->toBe(0);
});

it('dokumen lama (tanpa versi / v1) tetap tercetak persis seperti semula walau template baru diaktifkan', function () {
    $legacy = InformedConsent::create(['patient_id' => $this->patient->id, 'consent_date' => '2026-01-01', 'status' => 'signed', 'physiotherapy_name' => 'Khayra Physio', 'treatment_location' => 'Khayra Physio Bandung']);
    expect($legacy->fresh()->template_version)->toBe(1);          // default kolom untuk baris lama

    config(['clinic.consent.active_template_version' => 2]);

    $this->get("/admin/informed-consents/{$legacy->id}/print")->assertOk()
        ->assertSee('Tanda Tangan Physiotherapy')->assertSee('Tanda Tangan Patient / Perwakilan')->assertDontSee('Dicatat oleh');
});

it('template v2 (hanya setelah disetujui klinik): tanpa kotak TTD fisioterapis, TTD pasien dipertahankan, jejak staf dan waktu ada', function () {
    config(['clinic.consent.active_template_version' => 2]);

    $this->post("/admin/patients/{$this->patient->id}/informed-consent", crConsentPayload(['physiotherapy_name' => '']))->assertRedirect();

    $consent = InformedConsent::firstOrFail();

    expect($consent->template_version)->toBe(2);

    $this->get("/admin/informed-consents/{$consent->id}/print")->assertOk()
        ->assertSee('Tanda Tangan Patient / Perwakilan')          // persetujuan pasien/wali tetap ada
        ->assertDontSee('Tanda Tangan Physiotherapy')
        ->assertSee('Dicatat oleh: admin@uji.test');
});

// ======================= SPRINT REVISI KLIEN: BIAYA RUANGAN & ADMIN DALAM INVOICE/LAPORAN =======================

function crSummary($test, ?string $month = null): array
{
    return $test->get('/admin/owner-finance?month='.($month ?? ClinicTime::monthKey()))->assertOk()->viewData('summary');
}

it('laporan omzet per kategori memisahkan treatment, ruangan, dan biaya admin; total sama dengan omzet; void tidak dihitung', function () {
    crBillableRoom('VIP 1', 100000, 'per_session');
    $visit = crVisit('VIP 1');
    Promo::create(['code' => 'D10', 'name' => '10%', 'discount_type' => 'percent', 'discount_value' => 10, 'status' => 'active']);

    crCheckout($this, [
        ['description' => 'Fisioterapi', 'price' => 300000, 'category' => 'program'],
        ['description' => 'Add-on', 'price' => 50000, 'category' => 'add_on'],
        ['description' => 'Biaya ruangan VIP 1', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $visit->id],
    ], ['promo_id' => Promo::first()->id, 'visit_id' => $visit->id])->assertSessionHasNoErrors();

    crRegister($this);                                                    // pasien baru: tagihan administrasi Rp35.000 otomatis

    // Invoice lama tanpa rincian item: tidak dihitung ulang, masuk "tanpa kategori".
    Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-LAMA-1', 'invoice_date' => ClinicTime::today(), 'amount' => 200000, 'paid_amount' => 200000, 'remaining_amount' => 0, 'payment_status' => 'paid']);

    // Invoice void tidak ikut.
    $void = Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-VOID-1', 'invoice_date' => ClinicTime::today(), 'amount' => 999000, 'payment_status' => 'void', 'voided_at' => now()]);
    $void->items()->create(['item_type' => 'service', 'item_category' => 'program', 'description' => 'Void', 'quantity' => 1, 'unit_price' => 999000, 'line_total' => 999000, 'discount_amount' => 0]);

    $summary = crSummary($this);
    $byCategory = collect($summary['revenue_by_category']['rows'])->pluck('amount', 'key');

    expect($byCategory['program'])->toBe(270000.0)                       // 300.000 - diskon 30.000
        ->and($byCategory['add_on'])->toBe(50000.0)
        ->and($byCategory['room_charge'])->toBe(100000.0)
        ->and($byCategory['admin_fee'])->toBe(35000.0)
        ->and($summary['revenue_by_category']['uncategorised'])->toBe(200000.0)
        ->and($summary['revenue_by_category']['total'])->toBe((float) $summary['invoice_revenue'])
        ->and(array_sum($byCategory->all()) + $summary['revenue_by_category']['uncategorised'])->toBe((float) $summary['invoice_revenue']);

    $this->get('/admin/owner-finance?month='.ClinicTime::monthKey())->assertSee('Omzet per Kategori')->assertSee('Biaya Ruangan')->assertSee('Biaya Admin');
    $this->get('/admin/owner-finance/print?month='.ClinicTime::monthKey())->assertOk()->assertSee('Omzet per Kategori');
});

it('laporan per kategori tidak mengubah arus kas (billing income) maupun closing yang sudah ada', function () {
    crBillableRoom('VIP 1', 100000);
    $visit = crVisit('VIP 1');

    crCheckout($this, [['description' => 'Biaya ruangan', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $visit->id]], ['paid_amount' => 100000, 'payment_method' => 'cash']);

    $summary = crSummary($this);

    expect((float) $summary['billing_income'])->toBe(100000.0)->and((float) $summary['invoice_revenue'])->toBe(100000.0);
});

it('invoice menampilkan biaya ruangan sebagai baris tersendiri lengkap dengan satuan tarif (admin detail dan cetak)', function () {
    crBillableRoom('VIP 1', 75000, 'per_hour');
    $visit = crVisit('VIP 1');

    crCheckout($this, [
        ['description' => 'Fisioterapi', 'price' => 300000, 'category' => 'program'],
        ['description' => 'Biaya ruangan VIP 1 (per jam)', 'price' => 75000, 'quantity' => 2, 'ref_type' => 'visit_room', 'ref_id' => $visit->id],
    ]);

    $billing = Billing::firstOrFail();

    foreach (["/admin/billings/{$billing->id}", "/admin/billings/{$billing->id}/print"] as $url) {
        $this->get($url)->assertOk()->assertSee('Biaya ruangan VIP 1 (per jam)')->assertSee('Biaya Ruangan')->assertSee('Tarif per jam')->assertSee('Fisioterapi');
    }
});

it('mengubah ruangan visit setelah biayanya ditagih hanya memberi peringatan: invoice tidak berubah dan tidak menagih ulang', function () {
    crBillableRoom('VIP 1', 100000);
    crBillableRoom('VIP 2', 200000);
    $visit = crVisit('VIP 1');

    crCheckout($this, [['description' => 'Biaya ruangan', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $visit->id]])->assertSessionHasNoErrors();
    $billing = Billing::firstOrFail();

    $payload = ['patient_id' => $this->patient->id, 'therapist_id' => $this->therapist->id, 'room_name' => 'VIP 2', 'visit_date' => ClinicTime::today(), 'status' => 'scheduled'];
    $this->post("/admin/visits/{$visit->id}/update", $payload)->assertRedirect('/admin/visits')
        ->assertSessionHas('warning', fn ($message) => str_contains($message, $billing->invoice_number) && str_contains($message, 'Void'));

    expect($visit->fresh()->room_name)->toBe('VIP 2')
        ->and((float) $billing->fresh()->amount)->toBe(100000.0)
        ->and(BillingItem::count())->toBe(1)
        ->and(Billing::count())->toBe(1);

    // Visit yang biayanya belum ditagih: tanpa peringatan.
    $free = crVisit('VIP 1');
    $this->post("/admin/visits/{$free->id}/update", array_merge($payload, ['room_name' => 'VIP 2']))->assertRedirect()->assertSessionMissing('warning');

    // Menyimpan tanpa mengubah ruangan: tanpa peringatan walau sudah ditagih.
    $this->post("/admin/visits/{$visit->id}/update", $payload)->assertRedirect()->assertSessionMissing('warning');
});

it('halaman master ruangan menjelaskan status tarif dan bahwa invoice lama memakai snapshot', function () {
    $this->get('/admin/rooms')->assertOk()->assertSee('Belum ada ruangan berbayar');

    crBillableRoom('VIP 1', 100000, 'per_session');
    $this->get('/admin/rooms')->assertOk()->assertSee('1 ruangan berbayar aktif')->assertSee('snapshot');
});

// ======================= SPRINT REVISI KLIEN: PREVIEW TEMPLATE CONSENT V2 =======================

it('preview template v2 untuk persetujuan klinik: tanpa kotak TTD fisioterapis, TTD pasien dan identitas pelaksana tetap, ditandai PREVIEW', function () {
    $this->get('/admin/informed-consents/template-preview/2')->assertOk()
        ->assertSee('PREVIEW TEMPLATE v2')->assertSee('BELUM AKTIF')
        ->assertSee('Tanda Tangan Patient / Perwakilan')
        ->assertDontSee('Tanda Tangan Physiotherapy')
        ->assertSee('Pelaksana: Nama Fisioterapis Contoh')                    // identitas fisioterapis dipertahankan
        ->assertSee('Dicatat oleh: admin@uji.test (contoh)');                 // jejak staf dan waktu dipertahankan

    // Hanya baca: tidak menyimpan consent, tidak mengaktifkan template.
    expect(InformedConsent::count())->toBe(0)->and(config('clinic.consent.active_template_version'))->toBe(1);
});

it('preview template v1 sebagai pembanding menampilkan kotak TTD fisioterapis; versi tidak dikenal 404; wajib login', function () {
    $this->get('/admin/informed-consents/template-preview/1')->assertOk()->assertSee('PREVIEW TEMPLATE v1')->assertSee('Tanda Tangan Physiotherapy');
    $this->get('/admin/informed-consents/template-preview/3')->assertNotFound();

    $this->flushSession();
    $this->get('/admin/informed-consents/template-preview/2')->assertRedirect('/admin/login');
});

it('dokumen consent tersimpan tidak pernah memuat penanda preview (v1 dan v2) dan arsip menaut ke preview', function () {
    $v1 = InformedConsent::create(['patient_id' => $this->patient->id, 'consent_date' => '2026-01-01', 'status' => 'signed', 'physiotherapy_name' => 'Khayra Physio', 'treatment_location' => 'Khayra Physio Bandung']);
    $v2 = InformedConsent::create(['patient_id' => $this->patient->id, 'consent_date' => '2026-01-02', 'status' => 'signed', 'physiotherapy_name' => 'Fisio', 'treatment_location' => 'Khayra Physio Bandung', 'template_version' => 2, 'recorded_by' => 'admin@uji.test']);

    foreach ([$v1, $v2] as $consent) {
        $this->get("/admin/informed-consents/{$consent->id}/print")->assertOk()->assertDontSee('PREVIEW')->assertDontSee('CONTOH')->assertSee("#{$consent->id}")->assertSee('Kembali ke Detail Patient');
    }

    $this->get('/admin/consent-archive')->assertOk()->assertSee('/admin/informed-consents/template-preview/2', false);
});

it('cetak consent v1 identik byte-per-byte dengan keluaran sebelum template v2 dibuat (golden file), walau v2 diaktifkan', function () {
    Carbon::setTestNow('2026-10-12 03:00:00');
    $this->patient->update(['full_name' => 'Pasien Baseline']);          // id 1 dan data sama dengan saat golden file direkam
    $consent = InformedConsent::create(['patient_id' => $this->patient->id, 'consent_date' => '2026-01-01', 'status' => 'signed', 'physiotherapy_name' => 'Khayra Physio', 'treatment_location' => 'Khayra Physio Bandung', 'agreement_text' => 'Teks uji']);
    $golden = file_get_contents(base_path('tests/Fixtures/consent-v1-print.html'));

    // id auto-increment berbeda antar database (MySQL tidak mengulang dari 1 setelah rollback): normalkan ke 1.
    $render = fn () => str_replace(
        ["/admin/patients/{$this->patient->id}\"", "#{$consent->id}<"],
        ['/admin/patients/1"', '#1<'],
        $this->get("/admin/informed-consents/{$consent->id}/print")->getContent()
    );

    expect($render())->toBe($golden);

    config(['clinic.consent.active_template_version' => 2]);
    expect($render())->toBe($golden);

    Carbon::setTestNow();
});

// ======================= SPRINT REVISI KLIEN: QA MATRIKS DISKON (REQ 5) =======================

it('matriks diskon: hanya Program didiskon; Add-on, Specialist, biaya ruangan, biaya admin tetap harga penuh', function () {
    crBillableRoom('VIP 1', 100000, 'per_session');
    $visit = crVisit('VIP 1');
    Promo::create(['code' => 'D20', 'name' => '20%', 'discount_type' => 'percent', 'discount_value' => 20, 'status' => 'active']);

    $program = ClinicService::create(['name' => 'Program Uji', 'price_per_visit' => 500000, 'category' => 'Program', 'status' => 'active']);
    $specialist = ClinicService::create(['name' => 'Specialist Uji', 'price_per_visit' => 425000, 'category' => 'Specialist', 'status' => 'active']);
    $addOn = ClinicService::create(['name' => '+ Add-on Uji', 'price_per_visit' => 70000, 'category' => 'Add-on', 'status' => 'active']);

    // Pasien baru: invoice administrasi otomatis. Dikoreksi (void) lalu diterbitkan ulang bersama treatment + promo.
    $newPatient = crRegister($this);
    $feeInvoice = NewPatientAdminFee::registrationInvoice($newPatient->id);
    $this->post("/admin/billings/{$feeInvoice->id}/void", ['void_reason' => 'Koreksi gabung invoice'])->assertRedirect();

    $this->post('/admin/cashier/checkout', [
        'patient_id' => $newPatient->id, 'visit_id' => '', 'invoice_date' => ClinicTime::today(), 'promo_id' => Promo::first()->id,
        'paid_amount' => 0, 'payment_method' => 'cash', 'replaces_billing_id' => $feeInvoice->id,
        'item_type' => ['service', 'service', 'service', 'service', 'service'],
        'description' => ['Program Uji', 'Specialist Uji', '+ Add-on Uji', 'Biaya ruangan VIP 1', 'Biaya Administrasi Pasien Baru'],
        'quantity' => [1, 1, 1, 1, 1], 'unit_price' => [500000, 425000, 70000, 100000, 35000],
        'inventory_item_id' => ['', '', '', '', ''], 'clinic_service_id' => [$program->id, $specialist->id, $addOn->id, '', ''],
        'item_category' => ['', '', '', '', ''], 'price_reason' => ['', '', '', '', ''],
        'reference_type' => ['', '', '', 'visit_room', 'patient_admin_fee'], 'reference_id' => ['', '', '', $visit->id, $newPatient->id],
    ])->assertSessionHasErrors('checkout');

    // Visit milik pasien lain ditolak untuk biaya ruangan (di atas); ulangi dengan visit pasien ini.
    $ownVisit = crVisit('VIP 1', $newPatient);
    $this->post('/admin/cashier/checkout', [
        'patient_id' => $newPatient->id, 'visit_id' => '', 'invoice_date' => ClinicTime::today(), 'promo_id' => Promo::first()->id,
        'paid_amount' => 0, 'payment_method' => 'cash', 'replaces_billing_id' => $feeInvoice->id,
        'item_type' => ['service', 'service', 'service', 'service', 'service'],
        'description' => ['Program Uji', 'Specialist Uji', '+ Add-on Uji', 'Biaya ruangan VIP 1', 'Biaya Administrasi Pasien Baru'],
        'quantity' => [1, 1, 1, 1, 1], 'unit_price' => [500000, 425000, 70000, 100000, 35000],
        'inventory_item_id' => ['', '', '', '', ''], 'clinic_service_id' => [$program->id, $specialist->id, $addOn->id, '', ''],
        'item_category' => ['', '', '', '', ''], 'price_reason' => ['', '', '', '', ''],
        'reference_type' => ['', '', '', 'visit_room', 'patient_admin_fee'], 'reference_id' => ['', '', '', $ownVisit->id, $newPatient->id],
    ])->assertSessionHasNoErrors();

    $billing = Billing::with('items')->where('replaces_billing_id', $feeInvoice->id)->firstOrFail();
    $byCategory = $billing->items->keyBy('item_category');

    expect($byCategory->keys()->sort()->values()->all())->toBe(['add_on', 'admin_fee', 'program', 'room_charge', 'specialist'])
        ->and((float) $byCategory['program']->discount_amount)->toBe(100000.0)       // 20% x 500.000
        ->and($byCategory['program']->is_discount_eligible)->toBeTrue();

    foreach (['add_on', 'specialist', 'room_charge', 'admin_fee'] as $category) {
        expect($byCategory[$category]->is_discount_eligible)->toBeFalse()->and((float) $byCategory[$category]->discount_amount)->toBe(0.0);
    }

    expect((float) $billing->subtotal_amount)->toBe(1130000.0)
        ->and((float) $billing->discount_amount)->toBe(100000.0)
        ->and((float) $billing->amount)->toBe(1030000.0);

    // Halaman invoice menandai baris yang tidak termasuk diskon.
    $this->get("/admin/billings/{$billing->id}")->assertOk()->assertSee('Tidak termasuk diskon')->assertSee('Diskon Rp 100.000');
});

it('WhatsApp invoice tidak membocorkan baris biaya ruangan/admin dan tetap minimal; pasien legacy (data bebas) tidak error', function () {
    crBillableRoom('VIP 1', 100000, 'per_session');
    $visit = crVisit('VIP 1');
    $this->patient->update(['religion' => 'Aliran Lama Teks Bebas', 'education' => 'Pendidikan Lama Teks Bebas']);       // data legacy di luar dropdown

    crCheckout($this, [
        ['description' => 'Biaya ruangan VIP 1', 'price' => 100000, 'ref_type' => 'visit_room', 'ref_id' => $visit->id],
    ])->assertSessionHasNoErrors();

    $billing = Billing::firstOrFail();
    $message = (new InvoiceWhatsappMessage)->build($billing);

    expect($message)->not->toContain('ruangan')->not->toContain('VIP')->not->toContain('Aliran Lama')->and($message)->toContain('Rp100.000');

    // Invoice administrasi registrasi: pesan hanya total dan status, tanpa nama biaya.
    $fee = NewPatientAdminFee::registrationInvoice(crRegister($this)->id);
    expect((new InvoiceWhatsappMessage)->build($fee))->not->toContain('Administrasi')->toContain('Rp35.000')->toContain('Belum dibayar');

    $this->get("/admin/patients/{$this->patient->id}/edit")->assertOk()->assertSee('Aliran Lama Teks Bebas');
    $this->get("/admin/billings/{$billing->id}/whatsapp")->assertOk();
});

it('perubahan master ruangan (tarif, satuan, billable, status) tercatat di jejak audit dan tidak menagih apa pun', function () {
    $this->post('/admin/rooms', ['name' => 'R. Audit', 'status' => 'active'])->assertRedirect('/admin/rooms');
    $room = Room::where('name', 'R. Audit')->firstOrFail();

    expect(\App\Models\BillingAuditLog::where('action', 'room_created')->count())->toBe(1);

    // Simpan tanpa perubahan nilai: tidak ada audit baru.
    $this->post("/admin/rooms/{$room->id}/update", ['status' => 'active'])->assertRedirect('/admin/rooms');
    expect(\App\Models\BillingAuditLog::where('action', 'room_updated')->count())->toBe(0);

    $this->post("/admin/rooms/{$room->id}/update", ['status' => 'active', 'is_billable' => 1, 'rate' => 75000, 'rate_unit' => 'per_session'])->assertRedirect('/admin/rooms');

    $log = \App\Models\BillingAuditLog::where('action', 'room_updated')->firstOrFail();

    expect($log->billing_id)->toBeNull()
        ->and($log->before)->toMatchArray(['room' => 'R. Audit', 'is_billable' => '0', 'rate' => ''])
        ->and($log->after)->toMatchArray(['room' => 'R. Audit', 'is_billable' => '1', 'rate' => '75000.00', 'rate_unit' => 'per_session'])
        ->and(\App\Models\Billing::count())->toBe(0);
});
