<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\FinanceTransaction;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * TEST KARAKTERISASI: mendokumentasikan perilaku keuangan SAAT INI (sebelum rancangan pembayaran
 * disetujui). Test ini bukan pernyataan bahwa perilakunya benar; tujuannya (1) membuktikan temuan
 * audit dan (2) membuat perubahan semantik tidak terjadi tanpa sengaja. Bila rancangan
 * billing_payments disetujui dan semantik berubah, test ini diperbarui secara eksplisit.
 */
beforeEach(function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);
    $this->patient = Patient::create(['full_name' => 'Pasien Karakter', 'whatsapp' => '081234567890']);
});

function chCheckout($test, int $price, int $tendered, ?string $date = null)
{
    return $test->post('/admin/cashier/checkout', [
        'patient_id' => Patient::firstOrFail()->id, 'invoice_date' => $date ?? now()->toDateString(), 'paid_amount' => $tendered, 'payment_method' => 'cash',
        'item_type' => ['service'], 'description' => ['Jasa'], 'quantity' => [1], 'unit_price' => [$price],
        'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
    ]);
}

function chFinance($test, ?string $month = null)
{
    return $test->get('/admin/owner-finance?month='.($month ?? now()->format('Y-m')))->viewData('summary');
}

it('[TEMUAN] uang kembalian ikut tercatat sebagai pemasukan: bayar 600.000 untuk invoice 550.000', function () {
    chCheckout($this, 550000, 600000);

    $billing = Billing::firstOrFail();

    expect((float) $billing->amount)->toBe(550000.0)
        ->and((float) $billing->paid_amount)->toBe(600000.0)      // uang yang DISERAHKAN pasien
        ->and((float) $billing->change_amount)->toBe(50000.0)
        ->and((float) chFinance($this)['billing_income'])->toBe(600000.0);   // pemasukan = 600.000, bukan 550.000
});

it('[TEMUAN] invoice yang di-void menghapus uang yang diterima dari laporan finance tanpa catatan refund', function () {
    chCheckout($this, 550000, 550000);
    $billing = Billing::firstOrFail();
    expect((float) chFinance($this)['billing_income'])->toBe(550000.0);

    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Salah input']);

    $billing->refresh();
    $audit = BillingAuditLog::where('billing_id', $billing->id)->where('action', 'voided')->firstOrFail();

    expect((float) chFinance($this)['billing_income'])->toBe(0.0)           // uang 550.000 lenyap dari laporan
        ->and((float) $billing->paid_amount)->toBe(0.0)
        ->and((float) $audit->before['paid_amount'])->toBe(550000.0)         // hanya tersisa di audit log
        ->and(FinanceTransaction::count())->toBe(0);             // tidak ada catatan refund/pengeluaran
});

it('[TEMUAN] pembayaran dihitung pada bulan INVOICE, bukan bulan uang diterima', function () {
    $lastMonthDate = now()->subMonthNoOverflow()->startOfMonth()->addDays(9)->toDateString();
    $lastMonth = now()->subMonthNoOverflow()->format('Y-m');

    chCheckout($this, 400000, 0, $lastMonthDate);            // belum dibayar saat dibuat
    $billing = Billing::firstOrFail();

    $this->post("/admin/billings/{$billing->id}/status", ['payment_status' => 'paid']);   // dilunasi HARI INI

    expect((float) chFinance($this, $lastMonth)['billing_income'])->toBe(400000.0)       // masuk ke bulan lalu
        ->and((float) chFinance($this)['billing_income'])->toBe(0.0);                    // bulan ini 0
});

it('[TEMUAN] dashboard dan finance berbeda untuk invoice lama berstatus paid dengan paid_amount 0', function () {
    Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-LEGACY', 'invoice_date' => now()->toDateString(), 'amount' => 300000, 'paid_amount' => 0, 'payment_status' => 'paid']);

    expect((float) chFinance($this)['billing_income'])->toBe(300000.0);   // finance memakai fallback amount

    $this->get('/admin/dashboard')->assertOk()
        ->assertViewHas('monthlyPaidAmount', fn ($value) => (float) $value === 0.0);   // dashboard menjumlah paid_amount mentah
});

it('pembayaran sebagian dihitung sebesar paid_amount dan sisa tagihan terpisah', function () {
    chCheckout($this, 500000, 200000);

    $billing = Billing::firstOrFail();

    expect($billing->payment_status)->toBe('partial')
        ->and((float) chFinance($this)['billing_income'])->toBe(200000.0)
        ->and((float) $billing->remaining_amount)->toBe(300000.0);
});
