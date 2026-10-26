<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\BillingItem;
use App\Models\BillingPayment;
use App\Models\Booking;
use App\Models\FinanceMonthlyClosing;
use App\Models\Patient;
use App\Models\PendingRegistrationFee;
use App\Models\Promo;
use App\Models\Therapist;
use App\Models\TherapistAvailability;
use App\Models\Visit;
use App\Services\FinanceReport;
use App\Services\NewPatientAdminFee;
use App\Services\PatientRegistration;
use App\Services\PendingRegistrationFees;
use App\Support\ClinicTime;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

/*
| Aturan FINAL: Rp35.000 dikenakan SATU KALI saat pasien pertama kali didaftarkan; pasien lama tidak ditagih
| retroaktif; kunjungan/booking/reschedule/batal tidak membuat tagihan; kategori admin_fee tanpa diskon;
| dibayar lewat PaymentLedger; registrasi ganda tidak menggandakan tagihan.
*/

beforeEach(function () {
    $this->withSession(adminSession(['admin_email' => 'admin@uji.test']));
    $this->therapist = Therapist::create(['full_name' => 'Terapis', 'email' => 't@uji.test', 'password' => Hash::make('x-uji'), 'status' => 'active']);
});

function regPayload(array $overrides = []): array
{
    return array_merge(['full_name' => 'Pasien Baru', 'whatsapp' => '081211112222', 'registration_token' => (string) Str::uuid()], $overrides);
}

function regPatient($test, array $overrides = []): Patient
{
    $test->post('/admin/patients', regPayload($overrides))->assertSessionHasNoErrors();

    return Patient::latest('id')->firstOrFail();
}

function regFee(Patient $patient): ?Billing
{
    return NewPatientAdminFee::registrationInvoice($patient->id);
}

function regLegacyPatient(string $name = 'Pasien Lama'): Patient
{
    // Pasien yang sudah ada sebelum aturan ini: dibuat langsung (tanpa registrasi), tanpa tagihan administrasi.
    return Patient::create(['full_name' => $name, 'whatsapp' => '08'.random_int(100000000, 999999999)]);
}

// ---------------- registrasi pasien baru ----------------

it('registrasi admin pasien baru membuat satu tagihan administrasi Rp35.000: unpaid, kategori admin_fee, tanpa diskon, terauditkan', function () {
    $this->post('/admin/patients', regPayload())->assertRedirect('/admin/patients')
        ->assertSessionHas('success', fn ($m) => str_contains($m, 'Tagihan administrasi pasien baru INV-') && str_contains($m, 'Rp35.000'));

    $patient = Patient::firstOrFail();
    $billing = regFee($patient);

    expect($billing)->not->toBeNull()
        ->and(Billing::count())->toBe(1)
        ->and((int) $billing->patient_id)->toBe($patient->id)
        ->and((float) $billing->amount)->toBe(35000.0)
        ->and((float) $billing->subtotal_amount)->toBe(35000.0)
        ->and((float) $billing->discount_amount)->toBe(0.0)
        ->and($billing->payment_status)->toBe('unpaid')
        ->and($billing->payment_model)->toBe('ledger')
        ->and((float) $billing->paid_amount)->toBe(0.0)
        ->and((float) $billing->remaining_amount)->toBe(35000.0)
        ->and($billing->invoice_date->toDateString())->toBe(ClinicTime::today())
        ->and($billing->invoice_number)->toStartWith('INV-'.str_replace('-', '', ClinicTime::monthKey()))
        ->and($billing->checkout_token)->toBe('registration-fee:'.$patient->id)
        ->and(BillingPayment::count())->toBe(0);

    $item = $billing->items()->firstOrFail();

    expect(BillingItem::count())->toBe(1)
        ->and($item->item_category)->toBe('admin_fee')
        ->and($item->is_discount_eligible)->toBeFalse()
        ->and((float) $item->discount_amount)->toBe(0.0)
        ->and((float) $item->line_total)->toBe(35000.0)
        ->and($item->reference_type)->toBe('patient_admin_fee')->and($item->reference_id)->toBe($patient->id);

    $audit = BillingAuditLog::where('billing_id', $billing->id)->where('action', 'created')->firstOrFail();

    expect($audit->after['source'])->toBe('patient_registration')->and($audit->actor)->toBe('admin@uji.test')->and($audit->after['items'][0]['category'])->toBe('admin_fee');
});

it('Create Patient dari booking membuat satu tagihan administrasi dan menautkan booking; klik ulang tidak menggandakan', function () {
    $booking = Booking::create(['full_name' => 'Calon Pasien', 'whatsapp' => '081233334444', 'service' => 'Uji', 'booking_date' => ClinicTime::today(), 'booking_time' => '10:00', 'status' => 'confirmed']);

    $this->post("/admin/bookings/{$booking->id}/create-patient")->assertRedirect("/admin/bookings/{$booking->id}")
        ->assertSessionHas('success', fn ($m) => str_contains($m, 'Tagihan administrasi pasien baru'));
    $this->post("/admin/bookings/{$booking->id}/create-patient")->assertRedirect("/admin/bookings/{$booking->id}");
    $this->post("/admin/bookings/{$booking->id}/create-patient")->assertRedirect("/admin/bookings/{$booking->id}");

    $patient = Patient::firstOrFail();

    expect(Patient::count())->toBe(1)->and(Billing::count())->toBe(1)->and(BillingItem::where('reference_type', 'patient_admin_fee')->count())->toBe(1)
        ->and($booking->fresh()->patient_id)->toBe($patient->id)
        ->and($patient->registration_token)->toBe('booking:'.$booking->id)
        ->and((float) regFee($patient)->amount)->toBe(35000.0);
});

it('booking yang sudah tertaut ke pasien tidak membuat pasien atau tagihan lagi', function () {
    $patient = regLegacyPatient();
    $booking = Booking::create(['patient_id' => $patient->id, 'full_name' => $patient->full_name, 'whatsapp' => $patient->whatsapp, 'service' => 'Uji', 'booking_date' => ClinicTime::today(), 'booking_time' => '10:00', 'status' => 'confirmed']);

    $this->post("/admin/bookings/{$booking->id}/create-patient")->assertRedirect();

    expect(Patient::count())->toBe(1)->and(Billing::count())->toBe(0);
});

// ---------------- idempotensi dan perlindungan database ----------------

it('registrasi ganda dengan token yang sama hanya membuat satu pasien dan satu tagihan administrasi', function () {
    $payload = regPayload();

    $this->post('/admin/patients', $payload)->assertRedirect('/admin/patients');
    $this->post('/admin/patients', $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tersimpan sebelumnya'));
    $this->post('/admin/patients', $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tersimpan sebelumnya'));

    expect(Patient::count())->toBe(1)->and(Billing::count())->toBe(1)->and(BillingItem::count())->toBe(1)
        ->and(BillingAuditLog::where('action', 'created')->count())->toBe(1);
});

it('registrasi tanpa token ditolak (formulir kedaluwarsa) dan tidak membuat pasien maupun tagihan', function () {
    $payload = regPayload();
    unset($payload['registration_token']);

    $this->post('/admin/patients', $payload)->assertSessionHasErrors('registration_token');

    expect(Patient::count())->toBe(0)->and(Billing::count())->toBe(0);
});

it('form registrasi menyertakan token baru di setiap tampilan', function () {
    $tokenOf = function () {
        preg_match('/name="registration_token" value="([0-9a-f-]{36})"/', $this->get('/admin/patients/create')->assertOk()->getContent(), $m);

        return $m[1] ?? null;
    };

    expect($tokenOf())->not->toBeNull()->and($tokenOf())->not->toBe($tokenOf());
});

it('service: token yang sama mengembalikan pasien yang sama (created=false); database menolak token kembar', function () {
    $first = PatientRegistration::register(new Patient(['full_name' => 'Satu', 'whatsapp' => '0811']), 'token-service-1');
    $second = PatientRegistration::register(new Patient(['full_name' => 'Dua', 'whatsapp' => '0822']), 'token-service-1');

    expect($first['created'])->toBeTrue()->and($second['created'])->toBeFalse()
        ->and($second['patient']->id)->toBe($first['patient']->id)->and($second['fee']->id)->toBe($first['fee']->id)
        ->and(Patient::count())->toBe(1)->and(Billing::count())->toBe(1);

    expect(fn () => DB::table('patients')->insert(['full_name' => 'Kembar', 'registration_token' => 'token-service-1', 'created_at' => now(), 'updated_at' => now()]))
        ->toThrow(QueryException::class);

    // Pasien lama (tanpa token) boleh banyak.
    regLegacyPatient('A');
    regLegacyPatient('B');
    expect(Patient::whereNull('registration_token')->count())->toBe(2);
});

it('database menolak dua tagihan administrasi untuk pasien yang sama (checkout_token unik)', function () {
    $patient = regPatient($this);
    $row = fn () => ['patient_id' => $patient->id, 'invoice_number' => 'INV-X-'.Str::random(6), 'invoice_date' => ClinicTime::today(), 'amount' => 35000, 'checkout_token' => 'registration-fee:'.$patient->id, 'created_at' => now(), 'updated_at' => now()];

    expect(fn () => DB::table('billings')->insert($row()))->toThrow(QueryException::class);
    expect(Billing::count())->toBe(1);
});

it('pasien dan tagihannya atomik: bila tagihan gagal dibuat, pasien juga tidak tersimpan', function () {
    Billing::creating(fn () => throw new RuntimeException('simulasi kegagalan tagihan'));

    expect(fn () => PatientRegistration::register(new Patient(['full_name' => 'Gagal', 'whatsapp' => '0833']), 'token-gagal'))->toThrow(RuntimeException::class);

    expect(Patient::count())->toBe(0)->and(Billing::count())->toBe(0);
});

// ---------------- pasien lama, kunjungan berikutnya, reschedule/batal ----------------

it('pasien lama tidak ditagih retroaktif: aktivitas apa pun tidak membuat biaya administrasi dan kasir menolak menambahkannya', function () {
    $legacy = regLegacyPatient();
    $line = fn ($patientId) => [
        'patient_id' => $patientId, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0, 'item_type' => ['service'], 'description' => ['Biaya Administrasi'],
        'quantity' => [1], 'unit_price' => [35000], 'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['admin_fee'],
        'reference_type' => ['patient_admin_fee'], 'reference_id' => [$patientId],
    ];

    // Update data, booking, visit, dan invoice biasa tidak membuat biaya administrasi.
    $this->post("/admin/patients/{$legacy->id}/update", ['full_name' => 'Pasien Lama Diubah', 'whatsapp' => $legacy->whatsapp])->assertRedirect();
    Booking::create(['patient_id' => $legacy->id, 'full_name' => $legacy->full_name, 'whatsapp' => $legacy->whatsapp, 'service' => 'Uji', 'booking_date' => ClinicTime::today(), 'booking_time' => '10:00', 'status' => 'confirmed']);
    $this->post('/admin/visits', ['patient_id' => $legacy->id, 'therapist_id' => $this->therapist->id, 'visit_date' => ClinicTime::today(), 'status' => 'scheduled'])->assertRedirect('/admin/visits');

    expect(Billing::count())->toBe(0);

    // Baris administrasi tidak dapat ditambahkan manual di kasir untuk pasien lama.
    $this->post('/admin/cashier/checkout', $line($legacy->id))->assertSessionHasErrors('checkout');

    expect(Billing::count())->toBe(0)->and(regFee($legacy))->toBeNull()
        ->and(NewPatientAdminFee::stateFor($legacy->id)['state'])->toBe('not_applicable');

    // Kasir menampilkan statusnya sebagai informasi.
    $this->get('/admin/cashier')->assertViewHas('adminFee', fn ($fee) => $fee['patients'][$legacy->id]['state'] === 'not_applicable');
});

it('kunjungan, booking, dan invoice berikutnya tidak membuat tagihan administrasi kedua; kasir menolak penambahan manual', function () {
    $patient = regPatient($this);
    $fee = regFee($patient);

    $booking = Booking::create(['patient_id' => $patient->id, 'full_name' => $patient->full_name, 'whatsapp' => $patient->whatsapp, 'service' => 'Uji', 'booking_date' => ClinicTime::today(), 'booking_time' => '10:00', 'status' => 'confirmed']);
    $this->post('/admin/visits', ['patient_id' => $patient->id, 'therapist_id' => $this->therapist->id, 'visit_date' => ClinicTime::today(), 'status' => 'scheduled'])->assertRedirect('/admin/visits');
    $visit = Visit::firstOrFail();
    $this->post("/admin/visits/{$visit->id}/update", ['patient_id' => $patient->id, 'therapist_id' => $this->therapist->id, 'visit_date' => ClinicTime::today(), 'status' => 'completed'])->assertRedirect();

    // Invoice treatment berikutnya.
    $this->post('/admin/cashier/checkout', [
        'patient_id' => $patient->id, 'visit_id' => $visit->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0, 'item_type' => ['service'], 'description' => ['Fisioterapi'],
        'quantity' => [1], 'unit_price' => [300000], 'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['program'],
    ])->assertSessionHasNoErrors();

    // Penambahan manual selagi tagihan administrasi masih aktif ditolak (tidak ganda).
    $this->post('/admin/cashier/checkout', [
        'patient_id' => $patient->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0, 'item_type' => ['service'], 'description' => ['Admin lagi'],
        'quantity' => [1], 'unit_price' => [35000], 'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['admin_fee'],
        'reference_type' => ['patient_admin_fee'], 'reference_id' => [$patient->id],
    ])->assertSessionHasErrors('checkout');

    expect(BillingItem::where('item_category', 'admin_fee')->count())->toBe(1)
        ->and(BillingItem::where('reference_type', 'patient_admin_fee')->count())->toBe(1)
        ->and(Billing::count())->toBe(2)
        ->and(regFee($patient)->id)->toBe($fee->id)
        ->and($booking->fresh()->patient_id)->toBe($patient->id);
});

it('reschedule dan pembatalan booking tidak membuat, memindahkan, atau melepas tagihan administrasi', function () {
    $patient = regPatient($this);
    $fee = regFee($patient);
    $booking = Booking::create(['patient_id' => $patient->id, 'therapist_id' => $this->therapist->id, 'full_name' => $patient->full_name, 'whatsapp' => $patient->whatsapp, 'service' => 'Uji', 'booking_date' => ClinicTime::today(), 'booking_time' => '10:00', 'status' => 'confirmed']);
    $payload = ['full_name' => $patient->full_name, 'whatsapp' => $patient->whatsapp, 'service' => 'Uji', 'booking_date' => ClinicTime::today(), 'booking_time' => '11:30', 'therapist_id' => $this->therapist->id, 'status' => 'confirmed'];

    $this->post("/admin/bookings/{$booking->id}/update", $payload)->assertRedirect();                               // reschedule
    $this->post("/admin/bookings/{$booking->id}/update", array_merge($payload, ['booking_time' => '14:00']))->assertRedirect();
    $this->post("/admin/bookings/{$booking->id}/status", ['status' => 'cancelled'])->assertRedirect();               // batal
    $this->post("/admin/bookings/{$booking->id}/status", ['status' => 'no_show'])->assertRedirect();

    expect(Billing::count())->toBe(1)->and(BillingItem::count())->toBe(1)
        ->and(regFee($patient)->id)->toBe($fee->id)->and($fee->fresh()->payment_status)->toBe('unpaid');
});

it('booking publik tidak membuat pasien maupun tagihan (hanya menautkan ke pasien yang sudah ada)', function () {
    $date = Carbon::parse(ClinicTime::today())->addDay();
    TherapistAvailability::create(['therapist_id' => $this->therapist->id, 'day_of_week' => $date->isoWeekday(), 'start_time' => '08:00', 'end_time' => '20:00', 'slot_duration_minutes' => 30, 'capacity' => 3, 'status' => 'active']);

    $this->post('/booking', ['full_name' => 'Calon Publik', 'whatsapp' => '081299990000', 'service' => 'Uji', 'booking_date' => $date->toDateString(), 'booking_time' => '10:00'])->assertSessionHasNoErrors();

    expect(Booking::count())->toBe(1)->and(Booking::first()->patient_id)->toBeNull()->and(Patient::count())->toBe(0)->and(Billing::count())->toBe(0);
});

// ---------------- pembayaran lewat PaymentLedger, status unpaid, pelaporan ----------------

it('tagihan administrasi tetap unpaid sampai dibayar lewat alur pembayaran existing (PaymentLedger), termasuk cicilan', function () {
    $patient = regPatient($this);
    $fee = regFee($patient);

    expect($fee->fresh()->payment_status)->toBe('unpaid');
    $this->get("/admin/billings/{$fee->id}")->assertOk()->assertSee('Catat Pembayaran')->assertSee('Biaya Administrasi Pasien Baru')->assertSee('Biaya Admin');

    $this->post("/admin/billings/{$fee->id}/payments", ['tendered_amount' => 10000, 'method' => 'cash'] + idem())->assertSessionHas('success');
    expect($fee->fresh()->payment_status)->toBe('partial')->and((float) $fee->fresh()->remaining_amount)->toBe(25000.0);

    $this->post("/admin/billings/{$fee->id}/payments", ['tendered_amount' => 50000, 'method' => 'qr'] + idem())->assertSessionHas('success');

    $fee->refresh();
    $summary = $this->get('/admin/owner-finance?month='.ClinicTime::monthKey())->viewData('summary');

    expect($fee->payment_status)->toBe('paid')->and((float) $fee->change_amount)->toBe(25000.0)
        ->and(BillingPayment::where('billing_id', $fee->id)->count())->toBe(2)
        ->and((float) $summary['billing_income'])->toBe(35000.0)                    // kembalian bukan pendapatan
        ->and((float) $summary['invoice_revenue'])->toBe(35000.0);
});

it('biaya administrasi masuk laporan per kategori sebagai admin_fee dan tidak terpengaruh diskon invoice lain', function () {
    Promo::create(['code' => 'D10', 'name' => '10%', 'discount_type' => 'percent', 'discount_value' => 10, 'status' => 'active']);
    $patient = regPatient($this);

    $this->post('/admin/cashier/checkout', [
        'patient_id' => $patient->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0, 'promo_id' => Promo::first()->id, 'item_type' => ['service'], 'description' => ['Fisioterapi'],
        'quantity' => [1], 'unit_price' => [300000], 'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['program'],
    ])->assertSessionHasNoErrors();

    $byCategory = collect(FinanceReport::forMonth(ClinicTime::monthKey())['summary']['revenue_by_category']['rows'])->pluck('amount', 'key');

    expect($byCategory['admin_fee'])->toBe(35000.0)->and($byCategory['program'])->toBe(270000.0)
        ->and((float) regFee($patient)->discount_amount)->toBe(0.0)->and(regFee($patient)->promo_code)->toBeNull();
});

// ---------------- koreksi / void ----------------

it('void invoice administrasi: audit tersimpan, tidak ditagih otomatis lagi, dan dapat dikoreksi lewat Terbitkan Ulang', function () {
    $patient = regPatient($this);
    $fee = regFee($patient);

    $this->post("/admin/billings/{$fee->id}/void", ['void_reason' => 'Salah registrasi pasien'])->assertRedirect("/admin/billings/{$fee->id}");

    expect($fee->fresh()->payment_status)->toBe('void')
        ->and(BillingAuditLog::where('billing_id', $fee->id)->where('action', 'voided')->exists())->toBeTrue()
        ->and(Billing::count())->toBe(1)                                                    // tidak otomatis menerbitkan tagihan baru
        ->and(NewPatientAdminFee::stateFor($patient->id)['state'])->toBe('voided')
        ->and(regFee($patient)->id)->toBe($fee->id);                                        // token tetap: tidak diregistrasi/ditagih ulang

    // Registrasi yang sama dikirim ulang setelah void: tetap tidak membuat tagihan baru.
    $this->post('/admin/patients', regPayload(['registration_token' => $patient->registration_token]))->assertSessionHas('success');
    expect(Billing::count())->toBe(1)->and(Patient::count())->toBe(1);

    // Mekanisme koreksi existing: kasir terisi dari invoice void; penerbitan ulang membawa referensi biaya administrasi.
    $this->get("/admin/cashier?replaces={$fee->id}")->assertOk()->assertViewHas('prefillItems', fn ($items) => $items[0]['reference_type'] === 'patient_admin_fee' && $items[0]['category'] === 'admin_fee');

    $this->post('/admin/cashier/checkout', [
        'patient_id' => $patient->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0, 'replaces_billing_id' => $fee->id, 'item_type' => ['service'],
        'description' => ['Biaya Administrasi Pasien Baru'], 'quantity' => [1], 'unit_price' => [35000], 'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['admin_fee'],
        'reference_type' => ['patient_admin_fee'], 'reference_id' => [$patient->id],
    ])->assertSessionHasNoErrors();

    $replacement = Billing::where('replaces_billing_id', $fee->id)->firstOrFail();

    expect((float) $replacement->amount)->toBe(35000.0)->and($replacement->items()->first()->item_category)->toBe('admin_fee')
        ->and(NewPatientAdminFee::stateFor($patient->id)['state'])->toBe('voided')      // token registrasi tetap menunjuk invoice asli
        ->and(BillingItem::where('reference_type', 'patient_admin_fee')->whereHas('billing', fn ($q) => $q->where('payment_status', '!=', 'void'))->count())->toBe(1);
});

it('void invoice administrasi yang sudah dibayar mewajibkan keputusan refund atau kredit (alur existing)', function () {
    $patient = regPatient($this);
    $fee = regFee($patient);
    $this->post("/admin/billings/{$fee->id}/payments", ['tendered_amount' => 35000, 'method' => 'cash'] + idem())->assertSessionHas('success');

    $this->post("/admin/billings/{$fee->id}/void", ['void_reason' => 'Salah registrasi pasien'])->assertSessionHasErrors('money_disposition');
    $this->post("/admin/billings/{$fee->id}/void", ['void_reason' => 'Salah registrasi pasien', 'money_disposition' => 'refund', 'refund_method' => 'cash'])->assertRedirect();

    expect(BillingPayment::where('type', 'refund')->count())->toBe(1)->and($fee->fresh()->payment_status)->toBe('void');
});

// ---------------- saklar dan tepi ----------------

it('saklar clinic.new_patient_admin_fee.enabled=false: pasien tetap terdaftar tetapi tidak ada tagihan otomatis', function () {
    config(['clinic.new_patient_admin_fee.enabled' => false]);

    regPatient($this);

    expect(Patient::count())->toBe(1)->and(Billing::count())->toBe(0);
});

it('nominal mengikuti konfigurasi clinic.new_patient_admin_fee.amount', function () {
    expect(config('clinic.new_patient_admin_fee.amount'))->toBe(35000)->and(config('clinic.new_patient_admin_fee.enabled'))->toBeTrue();

    config(['clinic.new_patient_admin_fee.amount' => 40000]);
    $patient = regPatient($this);

    expect((float) regFee($patient)->amount)->toBe(40000.0);
});

it('registrasi baru tidak mengubah data pasien maupun invoice lama', function () {
    $legacy = regLegacyPatient();
    $old = Billing::create(['patient_id' => $legacy->id, 'invoice_number' => 'INV-LAMA-9', 'invoice_date' => '2026-01-10', 'amount' => 123000, 'paid_amount' => 123000, 'remaining_amount' => 0, 'payment_status' => 'paid']);
    $before = [Patient::find($legacy->id)->toArray(), $old->fresh()->toArray()];

    regPatient($this);

    expect([Patient::find($legacy->id)->toArray(), $old->fresh()->toArray()])->toBe($before)
        ->and(Billing::where('patient_id', $legacy->id)->count())->toBe(1)
        ->and(Patient::whereNull('registration_token')->count())->toBe(1);
});

it('kasir menampilkan status biaya administrasi pasien baru dan tidak lagi menyarankan penambahan manual', function () {
    $patient = regPatient($this);
    $fee = regFee($patient);

    $this->get('/admin/cashier')->assertOk()
        ->assertViewHas('adminFee', fn ($data) => $data['patients'][$patient->id]['state'] === 'charged' && $data['patients'][$patient->id]['invoice_number'] === $fee->invoice_number)
        ->assertDontSee('+ Biaya administrasi pasien baru');
});

// ---------------- pagar: semua jalur pembuatan pasien wajib lewat PatientRegistration ----------------

it('PAGAR: setiap kode yang membuat baris pasien memakai PatientRegistration::register (tidak ada jalur lain)', function () {
    $routes = file_get_contents(base_path('routes/web.php'));

    // Pecah per rute; rute yang menyimpan `new Patient` wajib memanggil PatientRegistration::register.
    $blocks = preg_split('/\n(?=Route::)/', $routes);
    $creators = array_filter($blocks, fn ($block) => str_contains($block, 'new Patient') && ! str_contains($block, 'template-preview'));

    expect($creators)->toHaveCount(2);                   // form admin + Create Patient dari booking

    foreach ($creators as $block) {
        expect($block)->toContain('PatientRegistration::register(')->not->toContain('$patient->save()');
    }

    // Tidak ada pembuatan pasien lewat jalur lain di kode aplikasi.
    $files = array_merge(
        [base_path('routes/web.php'), base_path('routes/console.php')],
        glob(base_path('app/*/*.php')),
        glob(base_path('app/*.php')),
        glob(base_path('database/seeders/*.php')),
    );

    foreach ($files as $file) {
        if (basename($file) === 'PatientRegistration.php') {
            continue;
        }

        $code = file_get_contents($file);

        expect($code)->not->toMatch('/Patient::(create|firstOrCreate|updateOrCreate|insert|forceCreate)\s*\(/', $file)
            ->and($code)->not->toMatch('/DB::table\(\s*[\'"]patients[\'"]\s*\)\s*->\s*(insert|insertOrIgnore|upsert)/', $file);
    }
});

// ======================= KEWAJIBAN TERTUNDA (PERIODE TUTUP BUKU) =======================

afterEach(fn () => Carbon::setTestNow());

function regCloseCurrentMonth(): FinanceMonthlyClosing
{
    return FinanceMonthlyClosing::create([
        'month' => ClinicTime::monthKey(), 'period_start' => ClinicTime::monthKey().'-01', 'period_end' => ClinicTime::today(),
        'billing_income' => 111000, 'total_income' => 111000, 'closed_at' => now(), 'closed_by' => 'uji',
    ]);
}

it('registrasi saat periode tutup: pasien terdaftar, kewajiban Rp35.000 dicatat PENDING (persisten, unik, beraudit), tanpa invoice dan tanpa pembayaran', function () {
    $closing = regCloseCurrentMonth();
    $closingBefore = $closing->fresh()->toArray();

    $this->post('/admin/patients', regPayload())->assertRedirect('/admin/patients')
        ->assertSessionHas('success', fn ($m) => str_contains($m, 'TERTUNDA') && str_contains($m, '/admin/registration-fees'));

    $patient = Patient::firstOrFail();
    $pending = PendingRegistrationFee::firstOrFail();

    expect(Billing::count())->toBe(0)->and(BillingItem::count())->toBe(0)->and(BillingPayment::count())->toBe(0)
        ->and($pending->patient_id)->toBe($patient->id)
        ->and((float) $pending->amount)->toBe(35000.0)
        ->and($pending->status)->toBe('pending')
        ->and($pending->closed_month)->toBe(ClinicTime::monthKey())
        ->and($pending->billing_id)->toBeNull()
        ->and($pending->created_by)->toBe('admin@uji.test')
        ->and(NewPatientAdminFee::stateFor($patient->id)['state'])->toBe('pending')
        ->and($closing->fresh()->toArray())->toBe($closingBefore);                      // closing historis tidak disentuh

    $audit = BillingAuditLog::where('action', 'registration_fee_pending')->firstOrFail();

    expect($audit->billing_id)->toBeNull()->and($audit->finance_month)->toBe(ClinicTime::monthKey())
        ->and($audit->after['patient_id'])->toBe($patient->id)->and((float) $audit->after['amount'])->toBe(35000.0);

    // Kasir tidak boleh menerbitkannya diam-diam di periode tertutup (dan tidak lewat jalur manual).
    $this->post('/admin/cashier/checkout', [
        'patient_id' => $patient->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0, 'item_type' => ['service'], 'description' => ['Admin'],
        'quantity' => [1], 'unit_price' => [35000], 'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['admin_fee'],
        'reference_type' => ['patient_admin_fee'], 'reference_id' => [$patient->id],
    ])->assertSessionHasErrors('invoice_date');

    expect(Billing::count())->toBe(0);
});

it('registrasi dari booking saat periode tutup juga mencatat kewajiban tertunda dan menautkan booking', function () {
    regCloseCurrentMonth();
    $booking = Booking::create(['full_name' => 'Calon Pasien', 'whatsapp' => '081233334444', 'service' => 'Uji', 'booking_date' => ClinicTime::today(), 'booking_time' => '10:00', 'status' => 'confirmed']);

    $this->post("/admin/bookings/{$booking->id}/create-patient")->assertSessionHas('success', fn ($m) => str_contains($m, 'TERTUNDA'));
    $this->post("/admin/bookings/{$booking->id}/create-patient")->assertRedirect();

    expect(Patient::count())->toBe(1)->and(PendingRegistrationFee::count())->toBe(1)->and(Billing::count())->toBe(0)
        ->and($booking->fresh()->patient_id)->toBe(Patient::first()->id);
});

it('registrasi ulang dengan token yang sama saat periode tutup tidak menggandakan pasien maupun kewajiban', function () {
    regCloseCurrentMonth();
    $payload = regPayload();

    $this->post('/admin/patients', $payload)->assertRedirect();
    $this->post('/admin/patients', $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tersimpan sebelumnya'));
    $this->post('/admin/patients', $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tersimpan sebelumnya'));

    expect(Patient::count())->toBe(1)->and(PendingRegistrationFee::count())->toBe(1)->and(BillingAuditLog::where('action', 'registration_fee_pending')->count())->toBe(1);
});

it('database menjamin satu kewajiban tertunda per pasien (unique patient_id)', function () {
    regCloseCurrentMonth();
    $patient = regPatient($this);
    $row = ['patient_id' => $patient->id, 'amount' => 35000, 'status' => 'pending', 'closed_month' => ClinicTime::monthKey(), 'created_at' => now(), 'updated_at' => now()];

    expect(fn () => DB::table('pending_registration_fees')->insert($row))->toThrow(QueryException::class);
    expect(PendingRegistrationFee::count())->toBe(1);
});

it('pasien dan kewajiban tertunda atomik: bila pencatatan kewajiban gagal, pasien juga tidak tersimpan', function () {
    regCloseCurrentMonth();
    PendingRegistrationFee::creating(fn () => throw new RuntimeException('simulasi kegagalan'));

    expect(fn () => PatientRegistration::register(new Patient(['full_name' => 'Gagal', 'whatsapp' => '0833']), 'token-pending-gagal'))->toThrow(RuntimeException::class);

    expect(Patient::count())->toBe(0)->and(PendingRegistrationFee::count())->toBe(0);
});

it('penerbitan ditolak selama periode hari ini masih tutup: tidak ada invoice mundur dan kewajiban tetap pending', function () {
    regCloseCurrentMonth();
    $patient = regPatient($this);
    $pending = PendingRegistrationFee::firstOrFail();

    $this->post("/admin/registration-fees/{$pending->id}/issue")->assertRedirect('/admin/registration-fees')
        ->assertSessionHas('error', fn ($m) => str_contains($m, 'tutup buku') && str_contains($m, 'tanggal mundur'));

    expect(Billing::count())->toBe(0)->and($pending->fresh()->status)->toBe('pending')->and(NewPatientAdminFee::stateFor($patient->id)['state'])->toBe('pending');
    $this->get('/admin/registration-fees')->assertOk()->assertSee('masih tutup buku');
});

it('penerbitan setelah periode baru dimulai: invoice bertanggal HARI INI (bukan periode tertutup), unpaid, tanpa pembayaran otomatis, beraudit', function () {
    Carbon::setTestNow('2026-10-20 03:00:00');
    $closing = regCloseCurrentMonth();
    $closingBefore = $closing->fresh()->toArray();
    $patient = regPatient($this);
    $pending = PendingRegistrationFee::firstOrFail();

    Carbon::setTestNow('2026-11-02 03:00:00');                                            // periode Oktober tetap tertutup; November terbuka

    $this->post("/admin/registration-fees/{$pending->id}/issue")->assertSessionHas('success', fn ($m) => str_contains($m, 'diterbitkan'));

    $billing = regFee($patient);
    $pending->refresh();

    expect($billing)->not->toBeNull()
        ->and($billing->invoice_date->toDateString())->toBe('2026-11-02')                    // tanggal penerbitan, bukan 2026-10
        ->and($billing->invoice_number)->toStartWith('INV-202611')
        ->and((float) $billing->amount)->toBe(35000.0)->and($billing->payment_status)->toBe('unpaid')->and((float) $billing->paid_amount)->toBe(0.0)
        ->and($billing->items()->first()->item_category)->toBe('admin_fee')->and($billing->items()->first()->is_discount_eligible)->toBeFalse()
        ->and(BillingPayment::count())->toBe(0)
        ->and($pending->status)->toBe('issued')->and($pending->billing_id)->toBe($billing->id)->and($pending->issued_by)->toBe('admin@uji.test')
        ->and(Billing::whereBetween('invoice_date', ['2026-10-01', '2026-10-31'])->count())->toBe(0)
        ->and($closing->fresh()->toArray())->toBe($closingBefore)
        ->and(NewPatientAdminFee::stateFor($patient->id)['state'])->toBe('charged');

    $audit = BillingAuditLog::where('billing_id', $billing->id)->where('action', 'registration_fee_issued')->firstOrFail();
    expect($audit->after['pending_fee_id'])->toBe($pending->id)->and($audit->after['closed_month'])->toBe('2026-10');
});

it('penerbitan setelah periode dibuka kembali memakai tanggal hari ini', function () {
    Carbon::setTestNow('2026-10-20 03:00:00');
    $closing = regCloseCurrentMonth();
    $patient = regPatient($this);
    $pending = PendingRegistrationFee::firstOrFail();

    $closing->update(['reopened_at' => now(), 'reopened_by' => 'uji', 'reopen_reason' => 'uji']);

    $this->post("/admin/registration-fees/{$pending->id}/issue")->assertSessionHas('success');

    expect(regFee($patient)->invoice_date->toDateString())->toBe('2026-10-20');
});

it('dua penerbitan berurutan/bersamaan untuk kewajiban yang sama hanya menghasilkan satu invoice (idempoten)', function () {
    Carbon::setTestNow('2026-10-20 03:00:00');
    regCloseCurrentMonth();
    $patient = regPatient($this);
    $pending = PendingRegistrationFee::firstOrFail();
    Carbon::setTestNow('2026-11-02 03:00:00');

    $first = PendingRegistrationFees::issue($pending->id);
    $second = PendingRegistrationFees::issue($pending->id);

    expect($first['created'])->toBeTrue()->and($second['created'])->toBeFalse()->and($second['billing']->id)->toBe($first['billing']->id)
        ->and(Billing::count())->toBe(1)->and(BillingItem::count())->toBe(1);

    $this->post("/admin/registration-fees/{$pending->id}/issue")->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah diterbitkan sebelumnya'));

    expect(Billing::count())->toBe(1)->and(BillingAuditLog::where('action', 'registration_fee_issued')->count())->toBe(1);

    // Lapis terakhir: database menolak tagihan administrasi kedua untuk pasien yang sama.
    expect(fn () => DB::table('billings')->insert(['patient_id' => $patient->id, 'invoice_number' => 'INV-X-1', 'invoice_date' => '2026-11-02', 'amount' => 35000, 'checkout_token' => 'registration-fee:'.$patient->id, 'created_at' => now(), 'updated_at' => now()]))
        ->toThrow(QueryException::class);
});

it('kewajiban yang tertunda dapat dibayar lewat PaymentLedger setelah diterbitkan; tidak ada pembayaran otomatis', function () {
    Carbon::setTestNow('2026-10-20 03:00:00');
    regCloseCurrentMonth();
    $patient = regPatient($this);
    Carbon::setTestNow('2026-11-02 03:00:00');

    $this->post('/admin/registration-fees/'.PendingRegistrationFee::firstOrFail()->id.'/issue')->assertSessionHas('success');
    $fee = regFee($patient);

    expect($fee->payment_status)->toBe('unpaid');
    $this->post("/admin/billings/{$fee->id}/payments", ['tendered_amount' => 35000, 'method' => 'cash'] + idem())->assertSessionHas('success');

    expect($fee->fresh()->payment_status)->toBe('paid')->and(BillingPayment::count())->toBe(1);
});

it('pasien lama tidak ditagih: tidak punya kewajiban tertunda, tidak muncul di daftar, dan penerbitan kewajiban orang lain tidak menyentuhnya', function () {
    $legacy = regLegacyPatient('Pasien Lama Tertunda');
    $old = Billing::create(['patient_id' => $legacy->id, 'invoice_number' => 'INV-LAMA-7', 'invoice_date' => '2026-01-10', 'amount' => 90000, 'paid_amount' => 90000, 'remaining_amount' => 0, 'payment_status' => 'paid']);
    $legacyBefore = [Patient::find($legacy->id)->toArray(), $old->fresh()->toArray()];

    Carbon::setTestNow('2026-10-20 03:00:00');
    regCloseCurrentMonth();
    regPatient($this);
    Carbon::setTestNow('2026-11-02 03:00:00');
    $this->post('/admin/registration-fees/'.PendingRegistrationFee::firstOrFail()->id.'/issue')->assertSessionHas('success');

    expect(PendingRegistrationFee::where('patient_id', $legacy->id)->exists())->toBeFalse()
        ->and(NewPatientAdminFee::stateFor($legacy->id)['state'])->toBe('not_applicable')
        ->and(Billing::where('patient_id', $legacy->id)->count())->toBe(1)
        ->and([Patient::find($legacy->id)->toArray(), $old->fresh()->toArray()])->toBe($legacyBefore);

    $this->get('/admin/registration-fees')->assertOk()->assertDontSee('Pasien Lama Tertunda');
});

it('saklar fitur nonaktif: registrasi saat periode tutup tidak membuat kewajiban maupun invoice', function () {
    config(['clinic.new_patient_admin_fee.enabled' => false]);
    regCloseCurrentMonth();

    regPatient($this);

    expect(Patient::count())->toBe(1)->and(PendingRegistrationFee::count())->toBe(0)->and(Billing::count())->toBe(0);
});

it('daftar Biaya Admin Tertunda: wajib login, menampilkan status pending/issued, dan lencana di sidebar', function () {
    Carbon::setTestNow('2026-10-20 03:00:00');
    regCloseCurrentMonth();
    $patient = regPatient($this, ['full_name' => 'Pasien Menunggu']);

    $this->get('/admin/registration-fees')->assertOk()->assertSee('Menunggu Diterbitkan (1)')->assertSee('Pasien Menunggu')->assertSee('Rp 35.000')
        ->assertSee('Biaya Admin Tertunda (1)')->assertSee('disabled', false);

    Carbon::setTestNow('2026-11-02 03:00:00');
    $this->get('/admin/registration-fees')->assertOk()->assertDontSee('disabled>Terbitkan', false);
    $this->post('/admin/registration-fees/'.PendingRegistrationFee::firstOrFail()->id.'/issue');

    $this->get('/admin/registration-fees')->assertOk()->assertSee('Menunggu Diterbitkan (0)')->assertSee('Tidak ada kewajiban administrasi yang tertunda')
        ->assertSee(regFee($patient)->invoice_number)->assertDontSee('Biaya Admin Tertunda (');

    $this->flushSession();
    $this->get('/admin/registration-fees')->assertRedirect('/admin/login');
    $this->post('/admin/registration-fees/1/issue')->assertRedirect('/admin/login');
});

it('audit integritas melaporkan kewajiban tertunda dan mendeteksi data tidak konsisten', function () {
    regCloseCurrentMonth();
    regPatient($this);

    $this->artisan('billing:audit-integrity')->expectsOutputToContain('1 kewajiban administrasi tertunda')->assertSuccessful();

    // Anomali: kewajiban berstatus issued tanpa invoice.
    PendingRegistrationFee::query()->update(['status' => 'issued', 'billing_id' => null]);

    $this->artisan('billing:audit-integrity')->expectsOutputToContain('berstatus issued tanpa invoice')->assertSuccessful();
});

// ---------------- keselamatan konfigurasi ----------------

it('KONFIGURASI: biaya administrasi hanya menyala lewat env eksplisit; default MATI di kode dan di .env.example', function () {
    $config = file_get_contents(base_path('config/clinic.php'));
    $example = file_get_contents(base_path('.env.example'));

    expect($config)->toContain("(bool) env('NEW_PATIENT_ADMIN_FEE_ENABLED', false)")
        ->and($example)->toMatch('/^NEW_PATIENT_ADMIN_FEE_ENABLED=false$/m')
        ->and(config('clinic.new_patient_admin_fee.amount'))->toBe(35000);

    // phpunit.xml menyalakannya khusus untuk tes; lingkungan tanpa variabel itu = mati.
    expect(file_get_contents(base_path('phpunit.xml')))->toContain('NEW_PATIENT_ADMIN_FEE_ENABLED');
});

it('KONFIGURASI: preflight menampilkan status biaya administrasi (aktif/nonaktif)', function () {
    $this->artisan('khayra:preflight')->expectsOutputToContain('Biaya administrasi pasien baru')->run();

    config(['clinic.new_patient_admin_fee.enabled' => false]);
    $this->artisan('khayra:preflight')->expectsOutputToContain('NONAKTIF (default')->run();
});
