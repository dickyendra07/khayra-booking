<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\BillingItem;
use App\Models\InformedConsent;
use App\Models\Patient;
use App\Models\Promo;
use App\Models\Room;
use App\Models\Therapist;
use App\Models\Visit;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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

// ======================= REQ 6: BIAYA ADMINISTRASI PASIEN BARU =======================

it('biaya admin pasien baru NONAKTIF secara default: tidak disarankan dan ditolak bila dipaksa', function () {
    expect(config('clinic.new_patient_admin_fee.enabled'))->toBeFalse()->and(config('clinic.new_patient_admin_fee.amount'))->toBe(35000);

    $this->get('/admin/cashier')->assertViewHas('adminFee', fn ($fee) => $fee['enabled'] === false);

    crCheckout($this, [['description' => 'Biaya Administrasi Awal', 'price' => 35000, 'ref_type' => 'patient_admin_fee', 'ref_id' => $this->patient->id]])
        ->assertSessionHasErrors('checkout');

    expect(Billing::count())->toBe(0);
});

it('tidak ada penagihan otomatis: membuat pasien atau booking tidak membuat biaya admin walau fitur aktif', function () {
    config(['clinic.new_patient_admin_fee.enabled' => true]);

    $this->post('/admin/patients', ['full_name' => 'Pasien Baru', 'whatsapp' => '0899']);

    expect(Billing::count())->toBe(0)->and(BillingItem::count())->toBe(0);
});

it('biaya admin (bila diaktifkan): baris tersendiri Rp35.000, kategori admin_fee, tidak didiskon, terauditkan', function () {
    config(['clinic.new_patient_admin_fee.enabled' => true]);
    Promo::create(['code' => 'D10', 'name' => '10%', 'discount_type' => 'percent', 'discount_value' => 10, 'status' => 'active']);

    crCheckout($this, [
        ['description' => 'Fisioterapi', 'price' => 300000, 'category' => 'program'],
        ['description' => 'Biaya Administrasi Awal', 'price' => 35000, 'ref_type' => 'patient_admin_fee', 'ref_id' => $this->patient->id],
    ], ['promo_id' => Promo::first()->id])->assertSessionHasNoErrors();

    $billing = Billing::with('items')->firstOrFail();
    $fee = $billing->items->firstWhere('item_category', 'admin_fee');

    expect((float) $fee->line_total)->toBe(35000.0)->and((float) $fee->discount_amount)->toBe(0.0)
        ->and($fee->reference_type)->toBe('patient_admin_fee')->and($fee->reference_id)->toBe($this->patient->id)
        ->and((float) $billing->amount)->toBe(305000.0)                          // 270.000 + 35.000
        ->and(BillingAuditLog::where('billing_id', $billing->id)->where('action', 'created')->firstOrFail()->after['items'][1]['category'])->toBe('admin_fee');
});

it('biaya admin tidak ditagih ganda per pasien (invoice lain, kirim ulang, dalam satu invoice) dan bebas lagi setelah void', function () {
    config(['clinic.new_patient_admin_fee.enabled' => true]);
    $line = [['description' => 'Biaya Administrasi Awal', 'price' => 35000, 'ref_type' => 'patient_admin_fee', 'ref_id' => $this->patient->id]];

    crCheckout($this, $line)->assertSessionHasNoErrors();
    crCheckout($this, $line)->assertSessionHasErrors('checkout');

    $other = Patient::create(['full_name' => 'Pasien Lain', 'whatsapp' => '08555']);
    crCheckout($this, [['description' => 'Biaya Administrasi Awal', 'price' => 35000, 'ref_type' => 'patient_admin_fee', 'ref_id' => $other->id]], ['patient_id' => $other->id])->assertSessionHasNoErrors();

    $both = Patient::create(['full_name' => 'Pasien Ketiga', 'whatsapp' => '08777']);
    crCheckout($this, [
        ['description' => 'Admin 1', 'price' => 35000, 'ref_type' => 'patient_admin_fee', 'ref_id' => $both->id],
        ['description' => 'Admin 2', 'price' => 35000, 'ref_type' => 'patient_admin_fee', 'ref_id' => $both->id],
    ], ['patient_id' => $both->id])->assertSessionHasErrors('checkout');

    expect(BillingItem::where('reference_type', 'patient_admin_fee')->count())->toBe(2);

    $first = Billing::orderBy('id')->first();
    $this->post("/admin/billings/{$first->id}/void", ['void_reason' => 'Salah input'])->assertRedirect();
    crCheckout($this, $line)->assertSessionHasNoErrors();

    expect(BillingItem::where('reference_type', 'patient_admin_fee')->count())->toBe(3);
});

it('biaya admin harus untuk pasien pada invoice dan bernilai sesuai konfigurasi (selain itu wajib alasan)', function () {
    config(['clinic.new_patient_admin_fee.enabled' => true]);
    $other = Patient::create(['full_name' => 'Pasien Lain', 'whatsapp' => '08555']);

    crCheckout($this, [['description' => 'Admin', 'price' => 35000, 'ref_type' => 'patient_admin_fee', 'ref_id' => $other->id]])->assertSessionHasErrors('checkout');
    crCheckout($this, [['description' => 'Admin', 'price' => 20000, 'ref_type' => 'patient_admin_fee', 'ref_id' => $this->patient->id]])->assertSessionHasErrors('price_reason');
    crCheckout($this, [['description' => 'Admin', 'price' => 20000, 'reason' => 'Keringanan disetujui', 'ref_type' => 'patient_admin_fee', 'ref_id' => $this->patient->id]])->assertSessionHasNoErrors();

    expect(Billing::count())->toBe(1);
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
