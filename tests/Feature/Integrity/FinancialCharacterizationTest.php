<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\Patient;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * TEST KARAKTERISASI INVOICE LAMA (payment_model = legacy).
 *
 * Invoice yang dibuat sebelum buku pembayaran TIDAK dihitung ulang: semantik historisnya dijaga
 * persis seperti sebelumnya (angka laporan lama harus tetap dapat direproduksi). Invoice baru
 * memakai buku pembayaran dan perilakunya diuji di PaymentLedgerTest.
 */
beforeEach(function () {
    $this->withSession(adminSession(['admin_email' => 'admin@uji.test']));
    $this->patient = Patient::create(['full_name' => 'Pasien Karakter', 'whatsapp' => '081234567890']);
});

function legacyInvoice(array $attrs = []): Billing
{
    // Billing::create tanpa payment_model => default database 'legacy' (seperti semua invoice lama).
    return Billing::create(array_merge([
        'patient_id' => Patient::firstOrFail()->id, 'invoice_number' => 'INV-OLD-'.uniqid(), 'invoice_date' => ClinicTime::today(),
        'subtotal_amount' => 550000, 'amount' => 550000, 'paid_amount' => 550000, 'remaining_amount' => 0, 'payment_status' => 'paid',
    ], $attrs));
}

function financeOf($test, ?string $month = null)
{
    return $test->get('/admin/owner-finance?month='.($month ?? ClinicTime::monthKey()))->viewData('summary');
}

it('invoice lama memakai model legacy secara default', function () {
    expect(legacyInvoice()->fresh()->payment_model)->toBe('legacy');
});

it('[LEGACY] invoice lama dengan paid_amount lebih besar dari amount tetap dihitung apa adanya (tidak dihitung ulang)', function () {
    legacyInvoice(['paid_amount' => 600000, 'change_amount' => 50000]);

    expect((float) financeOf($this)['billing_income'])->toBe(600000.0);
});

it('[LEGACY] void invoice lama mengeluarkan paid dari laporan seperti semula', function () {
    $billing = legacyInvoice();
    expect((float) financeOf($this)['billing_income'])->toBe(550000.0);

    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Salah input']);

    $audit = BillingAuditLog::where('billing_id', $billing->id)->where('action', 'voided')->firstOrFail();

    expect((float) financeOf($this)['billing_income'])->toBe(0.0)
        ->and((float) $audit->before['paid_amount'])->toBe(550000.0);
});

it('[LEGACY] pembayaran invoice lama dicatat pada bulan invoice', function () {
    $lastMonthDate = now()->subMonthNoOverflow()->startOfMonth()->addDays(9)->toDateString();
    $lastMonth = now()->subMonthNoOverflow()->format('Y-m');

    legacyInvoice(['invoice_date' => $lastMonthDate]);

    expect((float) financeOf($this, $lastMonth)['billing_income'])->toBe(550000.0)
        ->and((float) financeOf($this)['billing_income'])->toBe(0.0);
});

it('invoice lama berstatus paid dengan paid_amount 0 dihitung sama di dashboard dan finance (inkonsistensi lama teratasi)', function () {
    legacyInvoice(['amount' => 300000, 'paid_amount' => 0]);

    expect((float) financeOf($this)['billing_income'])->toBe(300000.0);

    $this->get('/admin/dashboard')->assertOk()
        ->assertViewHas('monthlyPaidAmount', fn ($value) => (float) $value === 300000.0);
});

it('[LEGACY] pembayaran sebagian invoice lama dihitung sebesar paid_amount', function () {
    $billing = legacyInvoice(['amount' => 500000, 'paid_amount' => 200000, 'remaining_amount' => 300000, 'payment_status' => 'partial']);

    expect((float) financeOf($this)['billing_income'])->toBe(200000.0)
        ->and($billing->fresh()->payment_model)->toBe('legacy');
});

it('invoice lama tidak dapat menerima pembayaran lewat buku pembayaran (tidak diubah semantiknya)', function () {
    $billing = legacyInvoice(['amount' => 500000, 'paid_amount' => 200000, 'remaining_amount' => 300000, 'payment_status' => 'partial']);

    $this->post("/admin/billings/{$billing->id}/payments", idem() + ['tendered_amount' => 300000, 'method' => 'cash'])->assertSessionHas('error');

    expect((float) $billing->fresh()->paid_amount)->toBe(200000.0)->and($billing->payments()->count())->toBe(0);
});
