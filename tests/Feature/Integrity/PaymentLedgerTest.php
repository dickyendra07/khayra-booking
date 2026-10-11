<?php

use App\Models\Billing;
use App\Models\BillingPayment;
use App\Models\FinanceMonthlyClosing;
use App\Models\FinanceTransaction;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Services\PaymentLedger;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'khayra_auth.admin.email' => 'admin@uji.test', 'khayra_auth.admin.password' => 'sandi-uji-lokal', 'khayra_auth.admin.password_hash' => null,
    ]);
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);
    $this->patient = Patient::create(['full_name' => 'Pasien Ledger', 'whatsapp' => '081234567890']);
});

afterEach(fn () => Carbon::setTestNow());

function ledgerCheckout($test, int $price, int $tendered, array $extra = [])
{
    return $test->post('/admin/cashier/checkout', array_merge([
        'patient_id' => Patient::firstOrFail()->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => $tendered, 'payment_method' => 'cash',
        'item_type' => ['service'], 'description' => ['Jasa'], 'quantity' => [1], 'unit_price' => [$price],
        'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
    ], $extra));
}

function ledgerFinance($test, ?string $month = null)
{
    return $test->get('/admin/owner-finance?month='.($month ?? ClinicTime::monthKey()))->viewData('summary');
}

// ---------------- uang kembalian ----------------

it('uang kembalian bukan pendapatan: invoice 550.000, diserahkan 600.000, kembalian 50.000, pendapatan 550.000', function () {
    ledgerCheckout($this, 550000, 600000);

    $billing = Billing::firstOrFail();
    $payment = BillingPayment::firstOrFail();

    expect($billing->payment_model)->toBe('ledger')
        ->and((float) $billing->amount)->toBe(550000.0)
        ->and((float) $billing->paid_amount)->toBe(550000.0)
        ->and((float) $billing->change_amount)->toBe(50000.0)
        ->and((float) $billing->remaining_amount)->toBe(0.0)
        ->and($billing->payment_status)->toBe('paid')
        ->and((float) $payment->tendered_amount)->toBe(600000.0)
        ->and((float) $payment->change_amount)->toBe(50000.0)
        ->and((float) $payment->amount)->toBe(550000.0)
        ->and($payment->method)->toBe('cash')
        ->and($payment->paid_on->toDateString())->toBe(ClinicTime::today());

    $summary = ledgerFinance($this);

    expect((float) $summary['billing_income'])->toBe(550000.0)     // bukan 600.000
        ->and((float) $summary['invoice_revenue'])->toBe(550000.0);
});

it('checkout tanpa pembayaran tidak membuat baris pembayaran dan berstatus unpaid', function () {
    ledgerCheckout($this, 400000, 0);

    $billing = Billing::firstOrFail();

    expect($billing->payment_status)->toBe('unpaid')
        ->and((float) $billing->remaining_amount)->toBe(400000.0)
        ->and(BillingPayment::count())->toBe(0)
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(0.0)
        ->and((float) ledgerFinance($this)['invoice_revenue'])->toBe(400000.0);
});

// ---------------- pembayaran sebagian ----------------

it('pembayaran sebagian: 200.000 lalu 300.000 tercatat terpisah dengan tanggal dan metode, tanpa double counting', function () {
    ledgerCheckout($this, 500000, 200000);
    $billing = Billing::firstOrFail();

    expect($billing->payment_status)->toBe('partial')
        ->and((float) $billing->remaining_amount)->toBe(300000.0)
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(200000.0);

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 300000, 'method' => 'qr', 'reference' => 'QR-123'])->assertSessionHas('success');

    $billing->refresh();
    $rows = $billing->payments()->orderBy('id')->get();

    expect($rows)->toHaveCount(2)
        ->and($rows->pluck('method')->all())->toBe(['cash', 'qr'])
        ->and($rows->pluck('amount')->map(fn ($a) => (float) $a)->all())->toBe([200000.0, 300000.0])
        ->and($rows[1]->reference)->toBe('QR-123')
        ->and($rows[1]->paid_on->toDateString())->toBe(ClinicTime::today())
        ->and($billing->payment_status)->toBe('paid')
        ->and((float) $billing->paid_amount)->toBe(500000.0)
        ->and((float) $billing->remaining_amount)->toBe(0.0)
        ->and(PaymentLedger::outstanding($billing))->toBe(0.0)
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(500000.0);   // 200.000 + 300.000, bukan lebih
});

it('pembayaran kedua melebihi sisa: kembalian dipisahkan dan hanya sisa yang dihitung', function () {
    ledgerCheckout($this, 500000, 200000);
    $billing = Billing::firstOrFail();

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 400000, 'method' => 'cash'])->assertSessionHas('success');

    $second = $billing->payments()->orderBy('id')->get()[1];

    expect((float) $second->tendered_amount)->toBe(400000.0)
        ->and((float) $second->amount)->toBe(300000.0)
        ->and((float) $second->change_amount)->toBe(100000.0)
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(500000.0);
});

it('pembayaran pada invoice yang sudah lunas ditolak dan tidak menggandakan pendapatan', function () {
    ledgerCheckout($this, 500000, 500000);
    $billing = Billing::firstOrFail();

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 100000, 'method' => 'cash'])->assertSessionHas('error');

    expect($billing->payments()->count())->toBe(1)->and((float) ledgerFinance($this)['billing_income'])->toBe(500000.0);
});

it('invoice manual: paid dan partial membuat baris pembayaran net', function () {
    $this->post('/admin/billings', ['patient_id' => $this->patient->id, 'invoice_date' => ClinicTime::today(), 'amount' => 300000, 'payment_status' => 'paid', 'payment_method' => 'debit']);
    $this->post('/admin/billings', ['patient_id' => $this->patient->id, 'invoice_date' => ClinicTime::today(), 'amount' => 300000, 'payment_status' => 'partial', 'paid_amount' => 100000, 'payment_method' => 'cash']);

    $paid = Billing::orderBy('id')->first();
    $partial = Billing::orderBy('id')->get()[1];

    expect($paid->payments()->first()->method)->toBe('debit')
        ->and((float) $paid->payments()->first()->amount)->toBe(300000.0)
        ->and($partial->payment_status)->toBe('partial')
        ->and((float) $partial->remaining_amount)->toBe(200000.0)
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(400000.0);
});

it('pembayaran pada invoice void ditolak', function () {
    ledgerCheckout($this, 500000, 0);
    $billing = Billing::firstOrFail();
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Batal']);

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 100000, 'method' => 'cash'])->assertSessionHas('error');

    expect(BillingPayment::count())->toBe(0);
});

// ---------------- void & refund ----------------

it('void invoice terbayar wajib menentukan nasib uang', function () {
    ledgerCheckout($this, 550000, 550000);
    $billing = Billing::firstOrFail();

    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Salah input'])->assertSessionHasErrors('money_disposition');
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Salah input', 'money_disposition' => 'refund'])->assertSessionHasErrors('refund_method');

    expect($billing->fresh()->payment_status)->toBe('paid');
});

it('void dengan refund: baris pembayaran tetap ada, refund transaksi tersendiri, arus kas bersih nol', function () {
    $product = InventoryItem::create(['sku' => 'R-1', 'name' => 'Tape', 'stock' => 5, 'selling_price' => 40000, 'status' => 'active']);
    ledgerCheckout($this, 550000, 550000, [
        'item_type' => ['service', 'inventory'], 'description' => ['Jasa', 'Tape'], 'quantity' => [1, 2], 'unit_price' => [550000, 40000],
        'inventory_item_id' => ['', $product->id], 'clinic_service_id' => ['', ''], 'item_category' => ['other', ''], 'paid_amount' => 630000,
    ]);
    $billing = Billing::firstOrFail();
    expect($product->fresh()->stock)->toBe(3)->and((float) $billing->amount)->toBe(630000.0);

    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Pasien batal', 'money_disposition' => 'refund', 'refund_method' => 'cash'])->assertSessionHas('success');

    $billing->refresh();
    $rows = $billing->payments()->orderBy('id')->get();

    expect($billing->payment_status)->toBe('void')
        ->and($rows->pluck('type')->all())->toBe(['payment', 'refund'])       // penerimaan tidak dihapus
        ->and((float) $rows[1]->amount)->toBe(630000.0)
        ->and($rows[1]->reason)->toContain('Pasien batal')
        ->and(PaymentLedger::heldOnVoid($billing))->toBe(0.0)
        ->and($product->fresh()->stock)->toBe(5);                              // stok kembali satu kali

    $summary = ledgerFinance($this);

    expect((float) $summary['billing_income'])->toBe(0.0)                      // 630.000 masuk - 630.000 refund
        ->and((float) $summary['refunds'])->toBe(630000.0)
        ->and((float) $summary['invoice_revenue'])->toBe(0.0);                 // omzet: invoice void tidak dihitung
});

it('refund memerlukan alasan, tidak melebihi saldo tertahan, dan tidak bisa dilakukan dua kali', function () {
    ledgerCheckout($this, 500000, 500000);
    $billing = Billing::firstOrFail();
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Batal', 'money_disposition' => 'credit']);

    $this->post("/admin/billings/{$billing->id}/refunds", ['amount' => 100000, 'method' => 'cash', 'reason' => 'x'])->assertSessionHasErrors('reason');
    $this->post("/admin/billings/{$billing->id}/refunds", ['amount' => 600000, 'method' => 'cash', 'reason' => 'Melebihi saldo'])->assertSessionHas('error');
    expect(BillingPayment::where('type', 'refund')->count())->toBe(0);

    $this->post("/admin/billings/{$billing->id}/refunds", ['amount' => 500000, 'method' => 'cash', 'reason' => 'Pasien batal total'])->assertSessionHas('success');
    $this->post("/admin/billings/{$billing->id}/refunds", ['amount' => 500000, 'method' => 'cash', 'reason' => 'Refund ganda coba'])->assertSessionHas('error');

    expect(BillingPayment::where('type', 'refund')->count())->toBe(1)->and(PaymentLedger::heldOnVoid($billing->fresh()))->toBe(0.0);
});

it('refund hanya untuk invoice void; invoice aktif tidak bisa direfund langsung', function () {
    ledgerCheckout($this, 500000, 500000);
    $billing = Billing::firstOrFail();

    $this->post("/admin/billings/{$billing->id}/refunds", ['amount' => 100000, 'method' => 'cash', 'reason' => 'Coba refund aktif'])->assertSessionHas('error');

    expect(BillingPayment::where('type', 'refund')->count())->toBe(0);
});

// ---------------- kredit ke invoice pengganti ----------------

it('kredit dipindahkan ke invoice pengganti tanpa dihitung sebagai kas baru dan tidak bisa dipakai dua kali', function () {
    ledgerCheckout($this, 500000, 500000);
    $original = Billing::firstOrFail();
    $this->post("/admin/billings/{$original->id}/void", ['void_reason' => 'Koreksi', 'money_disposition' => 'credit']);

    $payload = fn () => [
        'patient_id' => $this->patient->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0, 'replaces_billing_id' => $original->id,
        'item_type' => ['service'], 'description' => ['Jasa'], 'quantity' => [1], 'unit_price' => [450000],
        'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
    ];

    $this->post('/admin/cashier/checkout', $payload())->assertSessionHasNoErrors();
    $replacement = Billing::where('replaces_billing_id', $original->id)->firstOrFail();

    $types = BillingPayment::orderBy('id')->pluck('type')->all();

    expect($replacement->payment_status)->toBe('paid')
        ->and((float) $replacement->paid_amount)->toBe(450000.0)
        ->and($types)->toBe(['payment', 'credit_out', 'credit_in'])
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(500000.0)        // satu-satunya kas: pembayaran awal
        ->and(PaymentLedger::heldOnVoid($original->fresh()))->toBe(50000.0);         // sisa 50.000 belum direfund

    // Pemindahan kedua dari saldo yang sama tidak menambah kredit (tidak ada credit ganda).
    $second = Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-KEDUA', 'invoice_date' => ClinicTime::today(), 'amount' => 300000, 'payment_model' => 'ledger', 'payment_status' => 'unpaid']);
    $moved = (new PaymentLedger)->transferCredit($original->fresh(), $second);

    expect($moved)->toBe(50000.0)->and(PaymentLedger::heldOnVoid($original->fresh()))->toBe(0.0);

    $third = Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-KETIGA', 'invoice_date' => ClinicTime::today(), 'amount' => 300000, 'payment_model' => 'ledger', 'payment_status' => 'unpaid']);

    expect((new PaymentLedger)->transferCredit($original->fresh(), $third))->toBe(0.0);
});

it('pengganti lebih mahal dari kredit: kredit habis dan sisanya dibayar tunai baru (kas hanya selisih)', function () {
    ledgerCheckout($this, 400000, 400000);
    $original = Billing::firstOrFail();
    $this->post("/admin/billings/{$original->id}/void", ['void_reason' => 'Koreksi', 'money_disposition' => 'credit']);

    $this->post('/admin/cashier/checkout', [
        'patient_id' => $this->patient->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 100000, 'payment_method' => 'qr', 'replaces_billing_id' => $original->id,
        'item_type' => ['service'], 'description' => ['Jasa'], 'quantity' => [1], 'unit_price' => [500000],
        'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
    ])->assertSessionHasNoErrors();

    $replacement = Billing::where('replaces_billing_id', $original->id)->firstOrFail();

    expect($replacement->payment_status)->toBe('paid')
        ->and(PaymentLedger::heldOnVoid($original->fresh()))->toBe(0.0)
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(500000.0);    // 400.000 awal + 100.000 selisih tunai
});

// ---------------- tanggal bisnis WIB ----------------

it('tanggal pembayaran memakai tanggal bisnis WIB (dini hari WIB tidak jatuh ke hari sebelumnya)', function () {
    Carbon::setTestNow('2026-10-31 20:30:00');   // 03:30 WIB tanggal 1 November

    ledgerCheckout($this, 500000, 500000, ['invoice_date' => '2026-11-01']);

    $payment = BillingPayment::firstOrFail();
    $billing = Billing::firstOrFail();

    expect($payment->paid_on->toDateString())->toBe('2026-11-01')
        ->and($payment->paid_at->format('Y-m-d H:i'))->toBe('2026-10-31 20:30')       // timestamp tetap UTC
        ->and($billing->invoice_number)->toStartWith('INV-202611-')                    // nomor invoice bulan WIB
        ->and((float) ledgerFinance($this, '2026-11')['billing_income'])->toBe(500000.0)
        ->and((float) ledgerFinance($this, '2026-10')['billing_income'])->toBe(0.0);
});

it('arus kas mengikuti tanggal pembayaran, omzet mengikuti tanggal invoice', function () {
    $lastMonthDate = now()->subMonthNoOverflow()->startOfMonth()->addDays(9)->toDateString();
    $lastMonth = now()->subMonthNoOverflow()->format('Y-m');

    ledgerCheckout($this, 400000, 0, ['invoice_date' => $lastMonthDate]);
    $billing = Billing::firstOrFail();

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 400000, 'method' => 'cash'])->assertSessionHas('success');   // dibayar hari ini

    $last = ledgerFinance($this, $lastMonth);
    $now = ledgerFinance($this);

    expect((float) $last['invoice_revenue'])->toBe(400000.0)     // omzet di bulan invoice
        ->and((float) $last['billing_income'])->toBe(0.0)
        ->and((float) $now['billing_income'])->toBe(400000.0)    // kas di bulan uang diterima
        ->and((float) $now['invoice_revenue'])->toBe(0.0);
});

it('pembayaran untuk invoice bulan tertutup dicatat di bulan berjalan dan closing lama tidak berubah', function () {
    $closedDate = now()->subMonthNoOverflow()->startOfMonth()->addDays(9)->toDateString();
    $closedMonth = now()->subMonthNoOverflow()->format('Y-m');

    ledgerCheckout($this, 400000, 0, ['invoice_date' => $closedDate]);
    $billing = Billing::firstOrFail();
    $this->post('/admin/owner-finance/close', ['month' => $closedMonth])->assertSessionHas('success');

    $before = FinanceMonthlyClosing::firstOrFail()->only(['billing_income', 'total_income', 'snapshot']);

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 400000, 'method' => 'cash'])->assertSessionHas('success');

    expect(FinanceMonthlyClosing::firstOrFail()->only(['billing_income', 'total_income', 'snapshot']))->toEqual($before)
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(400000.0)
        ->and($this->get('/admin/owner-finance?month='.$closedMonth)->viewData('closingDrift'))->toBeNull();
});

it('pembayaran tidak dapat dicatat bila bulan berjalan sudah tutup buku', function () {
    ledgerCheckout($this, 400000, 0);
    $billing = Billing::firstOrFail();
    $this->post('/admin/owner-finance/close', ['month' => ClinicTime::monthKey()]);

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 400000, 'method' => 'cash'])->assertSessionHas('error');

    expect(BillingPayment::count())->toBe(0);
});

it('tutup buku menyimpan arus kas, omzet, dan basis perhitungan di snapshot', function () {
    ledgerCheckout($this, 550000, 600000);

    $this->post('/admin/owner-finance/close', ['month' => ClinicTime::monthKey()])->assertSessionHas('success');

    $closing = FinanceMonthlyClosing::firstOrFail();

    expect((float) $closing->billing_income)->toBe(550000.0)
        ->and($closing->snapshot['basis'])->toBe('cash_v2')
        ->and((float) $closing->snapshot['summary']['invoice_revenue'])->toBe(550000.0)
        ->and($closing->snapshot['transactions'][0]['kind'])->toBe('payment');
});

it('invoice lama dan baru pada bulan yang sama dijumlah tanpa double counting', function () {
    Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-LAMA', 'invoice_date' => ClinicTime::today(), 'amount' => 300000, 'paid_amount' => 300000, 'payment_status' => 'paid']);
    ledgerCheckout($this, 200000, 250000);

    $summary = ledgerFinance($this);

    expect((float) $summary['billing_income'])->toBe(500000.0)     // 300.000 (lama) + 200.000 (baru, tanpa kembalian)
        ->and((float) $summary['invoice_revenue'])->toBe(500000.0);
});

// ---------------- anti double charge ----------------

it('checkout yang dikirim dua kali dengan token yang sama hanya membuat satu invoice dan satu pembayaran', function () {
    $product = InventoryItem::create(['sku' => 'D-1', 'name' => 'Tape', 'stock' => 5, 'selling_price' => 40000, 'status' => 'active']);

    $payload = [
        'checkout_token' => 'token-uji-123',
        'item_type' => ['inventory'], 'description' => ['Tape'], 'quantity' => [1], 'unit_price' => [40000],
        'inventory_item_id' => [$product->id], 'clinic_service_id' => [''], 'item_category' => [''],
    ];

    ledgerCheckout($this, 40000, 40000, $payload);
    $response = ledgerCheckout($this, 40000, 40000, $payload);

    $response->assertRedirect('/admin/billings/'.Billing::firstOrFail()->id);

    expect(Billing::count())->toBe(1)
        ->and(BillingPayment::count())->toBe(1)
        ->and($product->fresh()->stock)->toBe(4)                               // stok berkurang sekali
        ->and((float) ledgerFinance($this)['billing_income'])->toBe(40000.0);
});

it('token berbeda membuat invoice berbeda (bukan klik ganda)', function () {
    ledgerCheckout($this, 40000, 40000, ['checkout_token' => 'token-A']);
    ledgerCheckout($this, 40000, 40000, ['checkout_token' => 'token-B']);

    expect(Billing::count())->toBe(2);
});

// ---------------- keutuhan buku ----------------

it('baris pembayaran tidak dapat diubah atau dihapus', function () {
    ledgerCheckout($this, 100000, 100000);
    $payment = BillingPayment::firstOrFail();

    expect(fn () => $payment->update(['amount' => 1]))->toThrow(LogicException::class)
        ->and(fn () => $payment->delete())->toThrow(LogicException::class)
        ->and(BillingPayment::count())->toBe(1);
});

it('halaman detail menampilkan riwayat pembayaran dengan kembalian, refund, dan kredit tertahan', function () {
    ledgerCheckout($this, 550000, 600000);
    $billing = Billing::firstOrFail();

    $this->get("/admin/billings/{$billing->id}")->assertOk()
        ->assertSee('Riwayat Pembayaran')->assertSee('Kembalian')->assertSee('Rp 600.000')->assertSee('Rp 550.000');

    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Batal', 'money_disposition' => 'credit']);

    $this->get("/admin/billings/{$billing->id}")->assertOk()->assertSee('Kredit tertahan: Rp 550.000')->assertSee('Catat Refund');
});

it('owner finance dan cetak menampilkan baris pembayaran dan refund', function () {
    ledgerCheckout($this, 550000, 550000);
    $billing = Billing::firstOrFail();
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Batal', 'money_disposition' => 'refund', 'refund_method' => 'cash']);

    $this->get('/admin/owner-finance')->assertOk()->assertSee('Pembayaran Invoice')->assertSee('Refund Pasien');
    $this->get('/admin/owner-finance/print')->assertOk();
});

it('command audit merekonsiliasi buku pembayaran: bersih untuk data konsisten, melaporkan data yang menyimpang', function () {
    ledgerCheckout($this, 500000, 200000);
    $billing = Billing::firstOrFail();
    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 300000, 'method' => 'qr']);

    $this->artisan('billing:audit-integrity')->expectsOutputToContain('10. Invoice ledger')->assertSuccessful();
    $this->artisan('billing:audit-integrity --strict')->assertSuccessful();   // bersih

    // Simulasi penyimpangan langsung di database (di luar aplikasi): paid_amount tidak sesuai buku.
    Billing::where('id', $billing->id)->update(['paid_amount' => 100000]);

    $this->artisan('billing:audit-integrity --strict')->assertFailed();
});

it('invoice void dengan kredit tertahan tampil sebagai informasi pada audit', function () {
    ledgerCheckout($this, 500000, 500000);
    $billing = Billing::firstOrFail();
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Batal', 'money_disposition' => 'credit']);

    $this->artisan('billing:audit-integrity')->expectsOutputToContain('1 invoice void dengan kredit tertahan, total Rp 500.000')->assertSuccessful();
});

it('baris pembayaran dan refund di Owner Finance tidak menawarkan tombol hapus (hanya transaksi manual yang dapat dihapus)', function () {
    ledgerCheckout($this, 550000, 550000);
    $billing = Billing::firstOrFail();
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Batal', 'money_disposition' => 'refund', 'refund_method' => 'cash']);

    $manual = FinanceTransaction::create(['transaction_date' => ClinicTime::today(), 'type' => 'expense', 'source' => 'operational', 'title' => 'Manual', 'amount' => 1000]);

    $html = $this->get('/admin/owner-finance')->assertOk()->getContent();

    expect(substr_count($html, '/admin/owner-finance/transactions/'))->toBe(1)                                  // hanya milik transaksi manual
        ->and($html)->toContain("/admin/owner-finance/transactions/{$manual->id}/delete");
});
