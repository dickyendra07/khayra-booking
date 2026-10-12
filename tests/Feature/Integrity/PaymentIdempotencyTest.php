<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\BillingPayment;
use App\Models\Patient;
use App\Services\FinanceReport;
use App\Services\PaymentLedger;
use App\Support\ClinicTime;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);
    $this->patient = Patient::create(['full_name' => 'Pasien Idempoten', 'whatsapp' => '081234567890']);
});

function idemInvoice($test, int $price, int $tendered = 0)
{
    $test->post('/admin/cashier/checkout', [
        'patient_id' => Patient::firstOrFail()->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => $tendered, 'payment_method' => 'cash',
        'item_type' => ['service'], 'description' => ['Jasa'], 'quantity' => [1], 'unit_price' => [$price],
        'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
    ])->assertSessionHasNoErrors();

    return Billing::latest('id')->firstOrFail();
}

function idemCash(int $month = 0): float
{
    return (float) app(FinanceReport::class)::forMonth(ClinicTime::monthKey())['summary']['billing_income'];
}

// ---------------- pembayaran cicilan ----------------

it('pengiriman ulang request pembayaran yang sama (token sama) tidak mencatat pembayaran dua kali', function () {
    $billing = idemInvoice($this, 500000);
    $key = (string) Str::uuid();
    $payload = ['tendered_amount' => 200000, 'method' => 'cash'] + idem($key);

    $this->post("/admin/billings/{$billing->id}/payments", $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'tercatat') && ! str_contains($m, 'sebelumnya'));
    $this->post("/admin/billings/{$billing->id}/payments", $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tercatat sebelumnya'));
    $this->post("/admin/billings/{$billing->id}/payments", $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tercatat sebelumnya'));

    $billing->refresh();

    expect(BillingPayment::where('billing_id', $billing->id)->count())->toBe(1)
        ->and((float) $billing->paid_amount)->toBe(200000.0)
        ->and((float) $billing->remaining_amount)->toBe(300000.0)
        ->and($billing->payment_status)->toBe('partial')
        ->and(BillingAuditLog::where('billing_id', $billing->id)->where('action', 'payment_recorded')->count())->toBe(1)
        ->and(idemCash())->toBe(200000.0);
});

it('pelunasan yang dikirim ulang tidak berubah menjadi error "sudah lunas" dan tidak menambah baris', function () {
    $billing = idemInvoice($this, 300000);
    $payload = ['tendered_amount' => 300000, 'method' => 'cash'] + idem();

    $this->post("/admin/billings/{$billing->id}/payments", $payload)->assertSessionHas('success');
    $this->post("/admin/billings/{$billing->id}/payments", $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tercatat sebelumnya'))->assertSessionMissing('error');

    expect(BillingPayment::count())->toBe(1)->and($billing->fresh()->payment_status)->toBe('paid');
});

it('dua cicilan sungguhan (token berbeda) tetap tercatat terpisah', function () {
    $billing = idemInvoice($this, 500000);

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 200000, 'method' => 'cash'] + idem())->assertSessionHas('success');
    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 300000, 'method' => 'qr'] + idem())->assertSessionHas('success');

    expect(BillingPayment::count())->toBe(2)->and($billing->fresh()->payment_status)->toBe('paid')->and(idemCash())->toBe(500000.0);
});

it('pembayaran tanpa token ditolak (formulir kedaluwarsa) dan tidak mencatat apa pun', function () {
    $billing = idemInvoice($this, 500000);

    $this->post("/admin/billings/{$billing->id}/payments", ['tendered_amount' => 200000, 'method' => 'cash'])->assertSessionHasErrors('idempotency_key');

    expect(BillingPayment::count())->toBe(0);
});

it('token yang sama tidak boleh dipakai untuk invoice lain atau jenis transaksi lain', function () {
    $first = idemInvoice($this, 500000);
    $second = idemInvoice($this, 400000);
    $key = (string) Str::uuid();

    $this->post("/admin/billings/{$first->id}/payments", ['tendered_amount' => 100000, 'method' => 'cash'] + idem($key))->assertSessionHas('success');
    $this->post("/admin/billings/{$second->id}/payments", ['tendered_amount' => 100000, 'method' => 'cash'] + idem($key))->assertSessionHas('error', fn ($m) => str_contains($m, 'sudah dipakai untuk transaksi lain'));

    expect(BillingPayment::where('billing_id', $second->id)->count())->toBe(0)->and(BillingPayment::count())->toBe(1);
});

it('level service: recordPayment dengan kunci yang sama mengembalikan baris yang sama, tanpa kunci tidak idempoten', function () {
    $billing = idemInvoice($this, 500000);
    $ledger = new PaymentLedger;

    $one = $ledger->recordPayment($billing, 100000, 'cash', null, 'kunci-service-1');
    $two = $ledger->recordPayment($billing, 100000, 'cash', null, 'kunci-service-1');

    expect($two->id)->toBe($one->id)->and($one->wasRecentlyCreated)->toBeTrue()->and($two->wasRecentlyCreated)->toBeFalse()
        ->and(BillingPayment::count())->toBe(1);

    // Kompatibilitas pemanggil internal tanpa kunci: perilaku lama (setiap panggilan = satu pembayaran).
    $ledger->recordPayment($billing, 50000, 'cash');
    $ledger->recordPayment($billing, 50000, 'cash');
    expect(BillingPayment::count())->toBe(3);
});

it('database menolak dua baris dengan idempotency_key yang sama (unique constraint), baris tanpa kunci boleh banyak', function () {
    $billing = idemInvoice($this, 500000);
    $row = fn (?string $key) => [
        'billing_id' => $billing->id, 'type' => 'payment', 'method' => 'cash', 'tendered_amount' => 1, 'change_amount' => 0, 'amount' => 1,
        'paid_on' => ClinicTime::today(), 'idempotency_key' => $key, 'created_at' => now(),
    ];

    DB::table('billing_payments')->insert($row('kunci-db'));

    expect(fn () => DB::table('billing_payments')->insert($row('kunci-db')))->toThrow(QueryException::class);

    DB::table('billing_payments')->insert($row(null));
    DB::table('billing_payments')->insert($row(null));

    expect(DB::table('billing_payments')->whereNull('idempotency_key')->count())->toBe(2);
});

// ---------------- refund ----------------

it('refund yang dikirim ulang (token sama) hanya tercatat satu kali dan saldo tertahan berkurang satu kali', function () {
    $billing = idemInvoice($this, 500000, 500000);
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Pasien batal', 'money_disposition' => 'credit'])->assertRedirect();
    $payload = ['amount' => 200000, 'method' => 'cash', 'reason' => 'Refund sebagian pasien'] + idem();

    $this->post("/admin/billings/{$billing->id}/refunds", $payload)->assertSessionHas('success', fn ($m) => ! str_contains($m, 'sebelumnya'));
    $this->post("/admin/billings/{$billing->id}/refunds", $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tercatat sebelumnya'));

    expect(BillingPayment::where('type', 'refund')->count())->toBe(1)
        ->and(PaymentLedger::heldOnVoid($billing->fresh()))->toBe(300000.0)           // 500.000 - 200.000, bukan 100.000
        ->and(BillingAuditLog::where('billing_id', $billing->id)->where('action', 'refund_recorded')->count())->toBe(1)
        ->and((float) app(FinanceReport::class)::forMonth(ClinicTime::monthKey())['summary']['refunds'])->toBe(200000.0);
});

it('refund dengan token baru setelah saldo habis tetap ditolak (bukan dianggap pengiriman ulang)', function () {
    $billing = idemInvoice($this, 500000, 500000);
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Pasien batal', 'money_disposition' => 'credit'])->assertRedirect();

    $this->post("/admin/billings/{$billing->id}/refunds", ['amount' => 500000, 'method' => 'cash', 'reason' => 'Refund penuh pasien'] + idem())->assertSessionHas('success');
    $this->post("/admin/billings/{$billing->id}/refunds", ['amount' => 500000, 'method' => 'cash', 'reason' => 'Refund penuh pasien'] + idem())->assertSessionHas('error');

    expect(BillingPayment::where('type', 'refund')->count())->toBe(1);
});

it('refund tanpa token ditolak', function () {
    $billing = idemInvoice($this, 500000, 500000);
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Pasien batal', 'money_disposition' => 'credit'])->assertRedirect();

    $this->post("/admin/billings/{$billing->id}/refunds", ['amount' => 100000, 'method' => 'cash', 'reason' => 'Tanpa token'])->assertSessionHasErrors('idempotency_key');

    expect(BillingPayment::where('type', 'refund')->count())->toBe(0);
});

it('void yang dikirim ulang dengan refund hanya membuat satu baris refund', function () {
    $billing = idemInvoice($this, 500000, 500000);
    $payload = ['void_reason' => 'Pasien batal', 'money_disposition' => 'refund', 'refund_method' => 'cash'];

    $this->post("/admin/billings/{$billing->id}/void", $payload)->assertRedirect();
    $this->post("/admin/billings/{$billing->id}/void", $payload)->assertRedirect();

    expect(BillingPayment::where('type', 'refund')->count())->toBe(1)->and(PaymentLedger::heldOnVoid($billing->fresh()))->toBe(0.0);
});

// ---------------- billing manual & checkout ----------------

it('billing manual yang dikirim ulang (checkout_token sama) hanya membuat satu invoice dan satu pembayaran', function () {
    $payload = [
        'patient_id' => $this->patient->id, 'invoice_date' => ClinicTime::today(), 'amount' => 400000, 'paid_amount' => 150000,
        'payment_status' => 'partial', 'payment_method' => 'cash', 'checkout_token' => (string) Str::uuid(),
    ];

    $this->post('/admin/billings', $payload)->assertRedirect('/admin/billings');
    $this->post('/admin/billings', $payload)->assertRedirect()->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tersimpan sebelumnya'));

    expect(Billing::count())->toBe(1)->and(BillingPayment::count())->toBe(1)->and(idemCash())->toBe(150000.0);
});

it('checkout kasir yang dikirim ulang (checkout_token sama) tidak membuat invoice atau pembayaran kedua', function () {
    $payload = [
        'patient_id' => $this->patient->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 300000, 'payment_method' => 'cash', 'checkout_token' => (string) Str::uuid(),
        'item_type' => ['service'], 'description' => ['Jasa'], 'quantity' => [1], 'unit_price' => [300000],
        'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
    ];

    $this->post('/admin/cashier/checkout', $payload)->assertSessionHasNoErrors();
    $this->post('/admin/cashier/checkout', $payload)->assertSessionHas('success', fn ($m) => str_contains($m, 'sudah tersimpan sebelumnya'));

    expect(Billing::count())->toBe(1)->and(BillingPayment::count())->toBe(1)->and(idemCash())->toBe(300000.0);
});

it('halaman detail invoice menyertakan token baru di setiap tampilan formulir pembayaran dan refund', function () {
    $billing = idemInvoice($this, 500000);

    $first = $this->get("/admin/billings/{$billing->id}")->assertOk()->getContent();
    $second = $this->get("/admin/billings/{$billing->id}")->assertOk()->getContent();

    preg_match('/name="idempotency_key" value="([0-9a-f-]{36})"/', $first, $a);
    preg_match('/name="idempotency_key" value="([0-9a-f-]{36})"/', $second, $b);

    expect($a)->not->toBeEmpty()->and($b)->not->toBeEmpty()->and($a[1])->not->toBe($b[1])
        ->and($first)->toContain('data-once-form');
});
