<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\ClinicService;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Services\PaymentLedger;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withSession(adminSession(['admin_email' => 'admin@uji.test']));
    $this->patient = Patient::create(['full_name' => 'Pasien Koreksi', 'whatsapp' => '081234567890']);
    $this->program = ClinicService::create(['name' => 'Prog', 'price_per_visit' => 500000, 'category' => 'Program', 'status' => 'active']);
    $this->product = InventoryItem::create(['sku' => 'K-1', 'name' => 'Tape', 'stock' => 10, 'selling_price' => 40000, 'status' => 'active']);
});

function originalInvoice($test): Billing
{
    $test->post('/admin/cashier/checkout', [
        'patient_id' => $test->patient->id, 'invoice_date' => now()->toDateString(), 'paid_amount' => 580000, 'payment_method' => 'cash',
        'item_type' => ['service', 'inventory'], 'description' => ['Prog', 'Tape'], 'quantity' => [1, 2], 'unit_price' => [500000, 40000],
        'inventory_item_id' => ['', $test->product->id], 'clinic_service_id' => [$test->program->id, ''], 'item_category' => ['', ''], 'price_reason' => ['', ''],
    ]);

    return Billing::firstOrFail();
}

it('alur koreksi: void invoice lunas lalu terbitkan ulang menghasilkan invoice baru tertaut, histori utuh', function () {
    $original = originalInvoice($this);

    expect($original->payment_status)->toBe('paid')->and($this->product->fresh()->stock)->toBe(8);

    // Invoice lunas tidak bisa diubah langsung.
    $this->post("/admin/billings/{$original->id}/update", ['patient_id' => $this->patient->id, 'invoice_date' => now()->toDateString(), 'amount' => 1, 'payment_status' => 'paid'])->assertRedirect();
    expect((float) $original->fresh()->amount)->toBe(580000.0);

    // 1) Void dengan alasan; uang Rp580.000 yang sudah diterima ditahan sebagai kredit untuk invoice pengganti.
    $this->post("/admin/billings/{$original->id}/void", ['void_reason' => 'Jumlah tape salah'])->assertSessionHasErrors('money_disposition');
    $this->post("/admin/billings/{$original->id}/void", ['void_reason' => 'Jumlah tape salah', 'money_disposition' => 'credit'])->assertRedirect();
    expect($this->product->fresh()->stock)->toBe(10);

    // 2) Form kasir terisi dari invoice lama.
    $this->get("/admin/cashier?replaces={$original->id}")->assertOk()
        ->assertSee('Invoice pengganti (koreksi)')
        ->assertSee('name="replaces_billing_id"', false)
        ->assertSee('Tape');

    // 3) Simpan invoice pengganti (qty tape dikoreksi); tidak ada uang tunai baru, kredit yang dipakai.
    $this->post('/admin/cashier/checkout', [
        'patient_id' => $this->patient->id, 'invoice_date' => now()->toDateString(), 'paid_amount' => 0, 'payment_method' => 'cash',
        'replaces_billing_id' => $original->id,
        'item_type' => ['service', 'inventory'], 'description' => ['Prog', 'Tape'], 'quantity' => [1, 1], 'unit_price' => [500000, 40000],
        'inventory_item_id' => ['', $this->product->id], 'clinic_service_id' => [$this->program->id, ''], 'item_category' => ['', ''], 'price_reason' => ['', ''],
    ])->assertSessionHasNoErrors();

    $replacement = Billing::where('replaces_billing_id', $original->id)->firstOrFail();
    $original->refresh();

    expect((float) $replacement->amount)->toBe(540000.0)
        ->and($replacement->invoice_number)->not->toBe($original->invoice_number)
        ->and($this->product->fresh()->stock)->toBe(9)
        ->and($original->payment_status)->toBe('void')
        ->and($replacement->payment_status)->toBe('paid')
        ->and((float) $replacement->paid_amount)->toBe(540000.0)            // dibayar dari kredit
        ->and((float) $original->amount)->toBe(580000.0)                    // nominal asli tetap tersimpan
        ->and($original->items()->count())->toBe(2)                         // item asli tetap ada
        ->and(Billing::count())->toBe(2)
        ->and(BillingAuditLog::where('billing_id', $original->id)->pluck('action')->all())->toContain('created', 'voided', 'replaced_by')
        ->and(BillingAuditLog::where('billing_id', $replacement->id)->where('action', 'created')->exists())->toBeTrue();

    // Sisa kredit Rp40.000 (580.000 - 540.000) tertahan di invoice lama sampai direfund; belum ada uang yang kembali.
    expect(PaymentLedger::heldOnVoid($original))->toBe(40000.0);

    $this->post("/admin/billings/{$original->id}/refunds", idem() + ['amount' => 40000, 'method' => 'cash', 'reason' => 'Selisih koreksi harga'])->assertSessionHas('success');

    // Arus kas bulan ini: uang diterima 580.000 dikurangi refund 40.000 = 540.000 = nilai invoice pengganti
    // (pemindahan kredit tidak dihitung sebagai kas baru). Omzet hanya invoice pengganti.
    $this->get('/admin/owner-finance?month='.ClinicTime::monthKey())
        ->assertViewHas('summary', fn ($s) => (float) $s['billing_income'] === 540000.0 && (float) $s['invoice_revenue'] === 540000.0);
    expect(PaymentLedger::heldOnVoid($original->fresh()))->toBe(0.0);

    // Halaman detail menampilkan tautan kedua arah.
    $this->get("/admin/billings/{$original->id}")->assertOk()->assertSee('digantikan oleh')->assertSee($replacement->invoice_number);
    $this->get("/admin/billings/{$replacement->id}")->assertOk()->assertSee('Invoice pengganti');
});

it('hanya invoice void yang bisa diganti dan tidak bisa diganti dua kali', function () {
    $original = originalInvoice($this);

    $this->get("/admin/cashier?replaces={$original->id}")->assertRedirect('/admin/billings');

    $this->post('/admin/cashier/checkout', [
        'patient_id' => $this->patient->id, 'invoice_date' => now()->toDateString(), 'replaces_billing_id' => $original->id,
        'item_type' => ['service'], 'description' => ['Prog'], 'quantity' => [1], 'unit_price' => [500000],
        'inventory_item_id' => [''], 'clinic_service_id' => [$this->program->id], 'item_category' => [''],
    ])->assertSessionHasErrors('replaces_billing_id');

    $this->post("/admin/billings/{$original->id}/void", ['void_reason' => 'koreksi', 'money_disposition' => 'credit']);

    $payload = [
        'patient_id' => $this->patient->id, 'invoice_date' => now()->toDateString(), 'replaces_billing_id' => $original->id,
        'item_type' => ['service'], 'description' => ['Prog'], 'quantity' => [1], 'unit_price' => [500000],
        'inventory_item_id' => [''], 'clinic_service_id' => [$this->program->id], 'item_category' => [''],
    ];

    $this->post('/admin/cashier/checkout', $payload)->assertSessionHasNoErrors();
    $this->post('/admin/cashier/checkout', $payload)->assertSessionHasErrors('replaces_billing_id');

    expect(Billing::where('replaces_billing_id', $original->id)->count())->toBe(1);
});

it('invoice pengganti harus untuk pasien yang sama', function () {
    $original = originalInvoice($this);
    $this->post("/admin/billings/{$original->id}/void", ['void_reason' => 'koreksi']);
    $other = Patient::create(['full_name' => 'Pasien Lain', 'whatsapp' => '08555']);

    $this->post('/admin/cashier/checkout', [
        'patient_id' => $other->id, 'invoice_date' => now()->toDateString(), 'replaces_billing_id' => $original->id,
        'item_type' => ['service'], 'description' => ['Prog'], 'quantity' => [1], 'unit_price' => [500000],
        'inventory_item_id' => [''], 'clinic_service_id' => [$this->program->id], 'item_category' => [''],
    ])->assertSessionHasErrors('replaces_billing_id');
});

it('command audit integritas hanya membaca dan melaporkan temuan data lama', function () {
    $patient = $this->patient;
    // Data lama yang tidak konsisten (dibuat langsung, seperti kondisi sebelum proteksi).
    Billing::create(['patient_id' => $patient->id, 'invoice_number' => 'INV-X', 'invoice_date' => now()->toDateString(), 'subtotal_amount' => 100000, 'amount' => 100000, 'paid_amount' => 150000, 'payment_status' => 'paid']);
    Billing::create(['patient_id' => $patient->id, 'invoice_number' => 'INV-Y', 'invoice_date' => now()->toDateString(), 'amount' => 200000, 'paid_amount' => 0, 'payment_status' => 'partial']);

    $before = Billing::orderBy('id')->get()->toArray();

    $this->artisan('billing:audit-integrity')
        ->expectsOutputToContain('paid_amount > amount')
        ->expectsOutputToContain('Partial dengan paid_amount tidak valid')
        ->assertSuccessful();

    expect(Billing::orderBy('id')->get()->toArray())->toEqual($before);

    $this->artisan('billing:audit-integrity --strict')->assertFailed();
});

it('command audit melaporkan bersih pada data yang konsisten', function () {
    originalInvoice($this);

    $this->artisan('billing:audit-integrity --strict')->expectsOutputToContain('Tidak ada temuan.')->assertSuccessful();
});

it('command audit melaporkan dampak zona waktu UTC vs WIB tanpa mengubah data', function () {
    // Dibuat 20:30 UTC tanggal 31 Okt = 03:30 WIB tanggal 1 Nov: melintasi batas bulan.
    $billing = Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-TZ', 'invoice_date' => '2026-10-31', 'amount' => 100000, 'payment_status' => 'unpaid']);
    Billing::where('id', $billing->id)->update(['created_at' => '2026-10-31 20:30:00']);

    $before = Billing::find($billing->id)->only(['invoice_date', 'amount']);

    $this->artisan('billing:audit-integrity')
        ->expectsOutputToContain('Dampak zona waktu UTC vs WIB')
        ->expectsOutputToContain('melintasi batas BULAN (berisiko pada closing): 1')
        ->assertSuccessful();

    expect(Billing::find($billing->id)->only(['invoice_date', 'amount']))->toEqual($before);
});
