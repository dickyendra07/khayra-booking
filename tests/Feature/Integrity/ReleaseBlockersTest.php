<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\BillingPayment;
use App\Models\FinanceMonthlyClosing;
use App\Models\FinanceTransaction;
use App\Models\InventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\Patient;
use App\Models\PendingRegistrationFee;
use App\Services\FinancePeriodLock;
use App\Services\FinanceReport;
use App\Services\MonthlyClosing;
use App\Services\PatientRegistration;
use App\Services\PaymentLedger;
use App\Support\ClinicTime;
use App\Support\PeriodClosedException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Testing\TestResponse;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| RELEASE BLOCKERS 1 dan 2 — DITUTUP
|--------------------------------------------------------------------------
| 1. Closing race: tutup buku dan semua penulis finansial memakai kunci periode (FinancePeriodLock) di dalam transaction.
| 2. Invoice pengganti ganda: validasi pengganti diulang di dalam transaction setelah invoice sumber dikunci.
|
| Test di file ini deterministik (satu koneksi). Keadaan "closing/penggantian rival terjadi tepat setelah pemeriksaan awal tetapi
| sebelum transaction" disimulasikan lewat DB::listen. Pembuktian balapan NYATA (banyak proses paralel) ada di
| tests/Concurrency/run.php dan dijalankan oleh test terakhir file ini bila database uji adalah MySQL.
*/

beforeEach(function () {
    $this->withSession(adminSession(['admin_email' => 'admin@uji.test']));
    $this->patient = Patient::create(['full_name' => 'Pasien Blocker', 'whatsapp' => '081234567890']);
});

afterEach(fn () => Carbon::setTestNow());

function rbLedgerInvoice(Patient $patient, int $amount, ?string $date = null, int $paid = 0): Billing
{
    static $n = 0;
    $billing = Billing::create([
        'patient_id' => $patient->id, 'invoice_number' => 'INV-RB-'.(++$n), 'invoice_date' => $date ?? ClinicTime::today(), 'subtotal_amount' => $amount, 'amount' => $amount,
        'paid_amount' => 0, 'remaining_amount' => $amount, 'payment_status' => 'unpaid', 'payment_model' => 'ledger',
    ]);

    if ($paid > 0) {
        (new PaymentLedger)->recordPayment($billing, $paid, 'cash');
    }

    return $billing->fresh();
}

function rbClosing(string $month): FinanceMonthlyClosing
{
    return FinanceMonthlyClosing::create(['month' => $month, 'period_start' => $month.'-01', 'period_end' => $month.'-28', 'closed_at' => now(), 'closed_by' => 'rival']);
}

/** Jalankan $callback; tepat setelah query pertama yang mengandung $needle, jalankan $rival (sekali). */
function rbRival(string $needle, Closure $rival, Closure $callback)
{
    $done = false;

    DB::listen(function ($query) use (&$done, $needle, $rival) {
        if (! $done && str_contains($query->sql, $needle)) {
            $done = true;
            $rival();
        }
    });

    return $callback();
}

function rbCheckout($test, array $extra = [], array $lines = [['Jasa', 100000, 'other']]): TestResponse
{
    $payload = array_merge([
        'patient_id' => Patient::firstOrFail()->id, 'invoice_date' => ClinicTime::today(), 'paid_amount' => 0, 'payment_method' => 'cash',
        'item_type' => [], 'description' => [], 'quantity' => [], 'unit_price' => [], 'inventory_item_id' => [], 'clinic_service_id' => [], 'item_category' => [],
    ], $extra);

    foreach ($lines as $line) {
        [$description, $price, $category] = $line;
        $inventoryId = $line[3] ?? null;
        $payload['item_type'][] = $category === 'product' ? 'inventory' : 'service';
        $payload['description'][] = $description;
        $payload['quantity'][] = 1;
        $payload['unit_price'][] = $price;
        $payload['inventory_item_id'][] = $inventoryId ?? '';
        $payload['clinic_service_id'][] = '';
        $payload['item_category'][] = $category === 'product' ? '' : $category;
    }

    return $test->post('/admin/cashier/checkout', $payload);
}

// ======================= BLOCKER 1 — CLOSING RACE =======================

it('BLOCKER 1: snapshot closing = keadaan final periode (omzet, kas, manual) dan dihitung di dalam transaction setelah kunci periode', function () {
    $month = ClinicTime::monthKey();
    rbLedgerInvoice($this->patient, 300000, null, 120000);
    rbLedgerInvoice($this->patient, 80000);
    FinanceTransaction::create(['transaction_date' => ClinicTime::today(), 'type' => 'expense', 'title' => 'Biaya', 'amount' => 25000, 'source' => 'operational']);

    $observed = [];
    MonthlyClosing::$afterLockHook = function () use (&$observed) {
        $observed['lock_hook_in_tx'] = DB::transactionLevel() > 0;
    };
    MonthlyClosing::$afterComputeHook = function () use (&$observed) {
        $observed['compute_hook_in_tx'] = DB::transactionLevel() > 0;
        $observed['lock_row'] = DB::table('finance_period_locks')->where('month', ClinicTime::monthKey())->exists();
    };

    try {
        $closing = MonthlyClosing::close($month, 'uji', 'admin@uji.test');
    } finally {
        MonthlyClosing::$afterLockHook = null;
        MonthlyClosing::$afterComputeHook = null;
    }

    $live = FinanceReport::forMonth($month)['summary'];
    $snap = $closing->snapshot['summary'];

    expect($observed)->toBe(['lock_hook_in_tx' => true, 'compute_hook_in_tx' => true, 'lock_row' => true])
        ->and((float) $closing->billing_income)->toBe(120000.0)
        ->and((float) $snap['invoice_revenue'])->toBe((float) $live['invoice_revenue'])->and((float) $snap['invoice_revenue'])->toBe(380000.0)
        ->and((float) $snap['billing_income'])->toBe((float) $live['billing_income'])
        ->and((float) $snap['total_expense'])->toBe(25000.0)->and((float) $snap['net_cashflow'])->toBe(95000.0)
        ->and(MonthlyClosing::$afterLockHook)->toBeNull()
        ->and(BillingAuditLog::where('action', 'period_closed')->where('finance_month', $month)->count())->toBe(1);
});

it('BLOCKER 1: tutup ulang ditolak, buka kembali lalu tutup ulang menghasilkan snapshot baru yang konsisten; closing historis tidak diubah', function () {
    Carbon::setTestNow('2026-09-20 03:00:00');
    $invoice = rbLedgerInvoice($this->patient, 200000, null, 50000);
    MonthlyClosing::close('2026-09', null, 'admin@uji.test');
    $first = FinanceMonthlyClosing::where('month', '2026-09')->firstOrFail()->toArray();

    expect(fn () => MonthlyClosing::close('2026-09', null, 'admin@uji.test'))->toThrow(DomainException::class, 'sudah tutup buku');

    expect(FinanceMonthlyClosing::where('month', '2026-09')->firstOrFail()->toArray())->toBe($first);       // tidak ditimpa

    // Bulan lain (Agustus, tidak tertutup) tidak tersentuh; bulan Oktober: pembayaran invoice September berjalan di bulan terbuka.
    Carbon::setTestNow('2026-10-02 03:00:00');
    (new PaymentLedger)->recordPayment($invoice, 100000, 'cash');
    expect(FinanceMonthlyClosing::where('month', '2026-09')->firstOrFail()->toArray())->toBe($first)
        ->and(FinanceReport::forMonth('2026-09')['summary']['billing_income'])->toBe(50000.0)                 // kas September tidak bergeser
        ->and(FinanceReport::forMonth('2026-10')['summary']['billing_income'])->toBe(100000.0);

    MonthlyClosing::reopen('2026-09', 'Koreksi uji buka kembali', 'admin@uji.test');
    expect(fn () => MonthlyClosing::reopen('2026-09', 'Buka lagi tanpa tutup', 'admin@uji.test'))->toThrow(DomainException::class, 'tidak dalam status tutup buku');

    $second = MonthlyClosing::close('2026-09', 'tutup ulang', 'admin@uji.test');

    expect((float) $second->billing_income)->toBe(50000.0)->and($second->reopened_at)->toBeNull()
        ->and(BillingAuditLog::where('action', 'period_reopened')->count())->toBe(1)
        ->and(BillingAuditLog::where('action', 'period_closed')->count())->toBe(2);
});

it('BLOCKER 1 (F): membayar invoice bulan lalu pada bulan berjalan yang terbuka diizinkan dan hanya memengaruhi kas bulan berjalan', function () {
    Carbon::setTestNow('2026-10-10 03:00:00');
    $invoice = rbLedgerInvoice($this->patient, 400000, '2026-09-15');
    MonthlyClosing::close('2026-09', null, 'admin@uji.test');
    $snapshot = FinanceMonthlyClosing::where('month', '2026-09')->firstOrFail()->snapshot;

    $this->post("/admin/billings/{$invoice->id}/payments", ['tendered_amount' => 150000, 'method' => 'cash'] + idem())->assertSessionHas('success');

    $payment = BillingPayment::firstOrFail();

    expect($payment->paid_on->toDateString())->toBe('2026-10-10')
        ->and(FinanceMonthlyClosing::where('month', '2026-09')->firstOrFail()->snapshot)->toBe($snapshot)
        ->and(FinanceReport::forMonth('2026-09')['summary']['billing_income'])->toBe(0.0)
        ->and(FinanceReport::forMonth('2026-10')['summary']['billing_income'])->toBe(150000.0);

    // Void invoice yang periodenya tertutup tetap ditolak; audit integrity tidak menandai kasus sah ini.
    $this->post("/admin/billings/{$invoice->id}/void", ['void_reason' => 'Coba void', 'money_disposition' => 'refund', 'refund_method' => 'cash'])->assertSessionHas('error', fn ($m) => str_contains($m, 'tutup buku'));
    $this->artisan('billing:audit-integrity', ['--strict' => true])->expectsOutputToContain('Tidak ada temuan')->assertSuccessful();
});

it('BLOCKER 1 (C): checkout yang lolos pemeriksaan awal tetapi periodenya ditutup sebelum transaction ditolak DI DALAM transaction (tanpa invoice/stok/pembayaran)', function () {
    $item = InventoryItem::create(['sku' => 'RB-1', 'name' => 'Produk', 'stock' => 5, 'selling_price' => 50000, 'status' => 'active']);

    rbRival('invoice_sequences', fn () => rbClosing(ClinicTime::monthKey()), fn () => rbCheckout($this, ['paid_amount' => 50000], [['Produk', 50000, 'product', $item->id]])
        ->assertSessionHasErrors('invoice_date'));

    expect(Billing::count())->toBe(0)->and(BillingPayment::count())->toBe(0)->and((int) $item->fresh()->stock)->toBe(5)->and(InventoryStockMovement::count())->toBe(0);
});

it('BLOCKER 1 (D): invoice manual dan transaksi finance manual pada periode tertutup ditolak setelah kunci periode', function () {
    rbRival('invoice_sequences', fn () => rbClosing(ClinicTime::monthKey()), fn () => $this->post('/admin/billings', [
        'patient_id' => $this->patient->id, 'invoice_date' => ClinicTime::today(), 'amount' => 80000, 'paid_amount' => 30000, 'payment_status' => 'partial', 'payment_method' => 'cash',
    ])->assertSessionHasErrors('invoice_date'));

    expect(Billing::count())->toBe(0)->and(BillingPayment::count())->toBe(0);

    // Periode sudah tertutup: transaksi manual create dan delete ditolak.
    $this->post('/admin/owner-finance/transactions', ['transaction_date' => ClinicTime::today(), 'type' => 'income', 'title' => 'Modal', 'amount' => 1000000])
        ->assertSessionHas('error', fn ($m) => str_contains($m, 'tutup buku'));
    expect(FinanceTransaction::count())->toBe(0);

    FinanceMonthlyClosing::query()->delete();
    $transaction = FinanceTransaction::create(['transaction_date' => ClinicTime::today(), 'type' => 'income', 'title' => 'Modal', 'amount' => 1000, 'source' => 'other_income']);
    rbClosing(ClinicTime::monthKey());
    $this->post("/admin/owner-finance/transactions/{$transaction->id}/delete")->assertSessionHas('error', fn ($m) => str_contains($m, 'tutup buku'));
    expect(FinanceTransaction::count())->toBe(1);
});

it('BLOCKER 1 (E,B): void dan refund pada periode yang ditutup setelah pemeriksaan awal ditolak di dalam transaction', function () {
    $invoice = rbLedgerInvoice($this->patient, 200000, null, 200000);

    rbRival('finance_period_locks', fn () => rbClosing(ClinicTime::monthKey()), fn () => $this->post("/admin/billings/{$invoice->id}/void", [
        'void_reason' => 'Void saat closing', 'money_disposition' => 'refund', 'refund_method' => 'cash',
    ])->assertSessionHas('error', fn ($m) => str_contains($m, 'tutup buku')));

    expect($invoice->fresh()->payment_status)->toBe('paid')->and(BillingPayment::where('type', 'refund')->count())->toBe(0);

    // Pembayaran dan refund langsung lewat service: ditolak karena periode kas tertutup.
    FinanceMonthlyClosing::query()->delete();
    $void = rbLedgerInvoice($this->patient, 100000, null, 100000);
    $void->update(['payment_status' => 'void', 'voided_at' => now(), 'paid_amount' => 0]);
    rbClosing(ClinicTime::monthKey());

    expect(fn () => (new PaymentLedger)->refund($void, 50000, 'cash', 'Refund saat tutup buku'))->toThrow(DomainException::class, 'tutup buku')
        ->and(fn () => (new PaymentLedger)->recordPayment(rbLedgerInvoice($this->patient, 1000, '2026-01-01'), 500, 'cash'))->toThrow(DomainException::class, 'tutup buku');
});

it('BLOCKER 1 (registrasi/pending): bila periode ditutup tepat sebelum transaction registrasi, kewajiban dicatat TERTUNDA (bukan invoice di periode tertutup)', function () {
    $result = rbRival('finance_period_locks', fn () => rbClosing(ClinicTime::monthKey()), fn () => PatientRegistration::register(new Patient(['full_name' => 'Baru', 'whatsapp' => '0899']), 'token-blocker-1'));

    expect($result['fee'])->toBeNull()->and($result['pending'])->not->toBeNull()
        ->and(PendingRegistrationFee::count())->toBe(1)->and(Billing::count())->toBe(0);
});

it('BLOCKER 1: kunci periode — bulan terurut menaik tanpa duplikat, tanggal kosong diabaikan, assertOpen melempar bila tertutup', function () {
    expect(FinancePeriodLock::monthsOf(['2026-11-03', null, '2026-09-30', '2026-11-30', ''.'2026-10-01 10:00:00']))->toBe(['2026-09', '2026-10', '2026-11']);

    DB::transaction(function () {
        expect(FinancePeriodLock::shared(['2026-12-05', '2026-10-01']))->toBe(['2026-10', '2026-12']);
        FinancePeriodLock::exclusive('2026-10');
    });

    expect(DB::table('finance_period_locks')->pluck('month')->sort()->values()->all())->toBe(['2026-10', '2026-12']);

    FinancePeriodLock::assertOpen('2026-10-15');
    rbClosing('2026-10');

    expect(fn () => FinancePeriodLock::assertOpen('2026-10-15'))->toThrow(PeriodClosedException::class)
        ->and(FinancePeriodLock::isClosed('2026-10'))->toBeTrue()->and(FinancePeriodLock::isClosed('2026-12-01'))->toBeFalse();
});

// ======================= BLOCKER 2 — INVOICE PENGGANTI GANDA =======================

function rbVoidedSource($test, int $credit = 500000): array
{
    $item = InventoryItem::create(['sku' => 'RB-'.random_int(1000, 9999), 'name' => 'Produk Koreksi', 'stock' => 10, 'selling_price' => 50000, 'status' => 'active']);

    rbCheckout($test, ['paid_amount' => 0], [['Jasa', $credit - 50000, 'other'], ['Produk Koreksi', 50000, 'product', $item->id]])->assertSessionHasNoErrors();
    $source = Billing::latest('id')->firstOrFail();
    (new PaymentLedger)->recordPayment($source, $credit, 'cash');
    $test->post("/admin/billings/{$source->id}/void", ['void_reason' => 'Void untuk koreksi', 'money_disposition' => 'credit'])->assertSessionHasNoErrors();

    return [$source->fresh(), $item];
}

function rbReplace($test, Billing $source, Model $item, string $token)
{
    return rbCheckout($test, ['paid_amount' => 0, 'replaces_billing_id' => $source->id, 'checkout_token' => $token], [['Jasa', $source->amount - 50000, 'other'], ['Produk Koreksi', 50000, 'product', $item->id]]);
}

it('BLOCKER 2 (A,B,C,D): pengganti rival yang muncul setelah pemeriksaan awal ditolak di dalam transaction — tanpa invoice, kredit, stok, atau audit ganda', function () {
    [$source, $item] = rbVoidedSource($this);
    $billingsBefore = Billing::count();
    $stockBefore = (int) $item->fresh()->stock;
    $movementsBefore = InventoryStockMovement::count();

    // Rival: tepat setelah pemeriksaan awal, request lain sudah menerbitkan pengganti untuk invoice sumber yang sama.
    $rival = fn () => Billing::create([
        'patient_id' => $this->patient->id, 'invoice_number' => 'INV-RIVAL', 'invoice_date' => ClinicTime::today(), 'amount' => 500000, 'subtotal_amount' => 500000,
        'paid_amount' => 0, 'remaining_amount' => 500000, 'payment_status' => 'unpaid', 'payment_model' => 'ledger', 'replaces_billing_id' => $source->id,
    ]);

    rbRival('invoice_sequences', $rival, fn () => rbReplace($this, $source, $item, 'tok-kalah')->assertSessionHasErrors('replaces_billing_id'));

    expect(Billing::count())->toBe($billingsBefore + 1)                                               // hanya invoice rival
        ->and(Billing::where('replaces_billing_id', $source->id)->count())->toBe(1)
        ->and(Billing::where('checkout_token', 'tok-kalah')->exists())->toBeFalse()                 // tidak ada invoice setengah jadi
        ->and((int) $item->fresh()->stock)->toBe($stockBefore)->and(InventoryStockMovement::count())->toBe($movementsBefore)
        ->and(BillingPayment::where('type', 'credit_out')->count())->toBe(0)->and(BillingPayment::where('type', 'credit_in')->count())->toBe(0)
        ->and(PaymentLedger::heldOnVoid($source->fresh()))->toBe(500000.0)                          // saldo kredit tidak hilang
        ->and(BillingAuditLog::where('billing_id', $source->id)->where('action', 'replaced_by')->count())->toBe(0);
});

it('BLOCKER 2 (E): koreksi sah berhasil — kredit non-kas dipindahkan sekali, stok dikurangi sekali, audit lengkap; percobaan kedua ditolak', function () {
    [$source, $item] = rbVoidedSource($this);

    rbReplace($this, $source, $item, 'tok-1')->assertSessionHasNoErrors();
    rbReplace($this, $source, $item, 'tok-2')->assertSessionHasErrors('replaces_billing_id');

    $replacement = Billing::where('replaces_billing_id', $source->id)->firstOrFail();

    expect(Billing::where('replaces_billing_id', $source->id)->where('payment_status', '!=', 'void')->count())->toBe(1)
        ->and($replacement->payment_status)->toBe('paid')->and((float) $replacement->paid_amount)->toBe(500000.0)
        ->and(BillingPayment::where('billing_id', $replacement->id)->where('type', 'payment')->count())->toBe(0)       // bukan kas baru
        ->and((float) BillingPayment::where('type', 'credit_out')->sum('amount'))->toBe(500000.0)
        ->and((float) BillingPayment::where('type', 'credit_in')->sum('amount'))->toBe(500000.0)
        ->and(PaymentLedger::heldOnVoid($source->fresh()))->toBe(0.0)
        ->and((int) $item->fresh()->stock)->toBe(9)                                                                    // 10 -> 9 (sumber void mengembalikan, pengganti mengurangi)
        ->and(InventoryStockMovement::where('inventory_item_id', $item->id)->where('type', 'out')->count())->toBe(2)  // sumber + pengganti
        ->and(BillingAuditLog::where('billing_id', $source->id)->where('action', 'replaced_by')->count())->toBe(1)
        ->and(BillingAuditLog::where('billing_id', $replacement->id)->where('action', 'created')->count())->toBe(1);
});

it('BLOCKER 2 (F): pengganti yang kemudian di-void mengikuti aturan koreksi existing (kredit kembali padanya, dapat digantikan lagi)', function () {
    [$source, $item] = rbVoidedSource($this);
    rbReplace($this, $source, $item, 'tok-1')->assertSessionHasNoErrors();
    $first = Billing::where('replaces_billing_id', $source->id)->firstOrFail();

    $this->post("/admin/billings/{$first->id}/void", ['void_reason' => 'Void pengganti', 'money_disposition' => 'credit'])->assertSessionHasNoErrors();

    expect(PaymentLedger::heldOnVoid($first->fresh()))->toBe(500000.0);

    rbReplace($this, $first->fresh(), $item, 'tok-3')->assertSessionHasNoErrors();           // pengganti dari pengganti
    $third = Billing::where('replaces_billing_id', $first->id)->firstOrFail();

    expect($third->payment_status)->toBe('paid')->and((int) $item->fresh()->stock)->toBe(9)
        ->and(Billing::where('replaces_billing_id', $first->id)->where('payment_status', '!=', 'void')->count())->toBe(1)
        ->and(PaymentLedger::heldOnVoid($first->fresh()))->toBe(0.0);

    // Invoice sumber pertama sudah punya pengganti yang di-void: aturan existing mengizinkan penggantian baru atasnya (tanpa kredit tersisa).
    rbReplace($this, $source->fresh(), $item, 'tok-4')->assertSessionHasNoErrors();

    $this->artisan('billing:audit-integrity', ['--strict' => true])->expectsOutputToContain('Tidak ada temuan')->assertSuccessful();
});

it('BLOCKER 2 (G): checkout pengganti yang gagal (stok kurang) tidak meninggalkan invoice setengah jadi dan tidak memindahkan kredit', function () {
    [$source, $item] = rbVoidedSource($this);
    $item->update(['stock' => 0]);                                                            // setelah void stok = 0: pengganti tidak bisa mengambil produk
    $billingsBefore = Billing::count();

    rbReplace($this, $source, $item, 'tok-gagal')->assertSessionHasErrors('stock');

    expect(Billing::count())->toBe($billingsBefore)->and(Billing::where('checkout_token', 'tok-gagal')->exists())->toBeFalse()
        ->and(BillingPayment::where('type', 'credit_out')->count())->toBe(0)
        ->and(PaymentLedger::heldOnVoid($source->fresh()))->toBe(500000.0)
        ->and(BillingAuditLog::where('billing_id', $source->id)->where('action', 'replaced_by')->count())->toBe(0);

    // Setelah stok tersedia, koreksi yang sama berhasil (kredit tidak hilang).
    $item->update(['stock' => 3]);
    rbReplace($this, $source, $item, 'tok-ulang')->assertSessionHasNoErrors();

    expect(Billing::where('replaces_billing_id', $source->id)->count())->toBe(1)->and(PaymentLedger::heldOnVoid($source->fresh()))->toBe(0.0);
});

it('BLOCKER 2: pengganti hanya untuk invoice void dan pasien yang sama (diperiksa ulang di dalam transaction)', function () {
    $live = rbLedgerInvoice($this->patient, 100000);

    rbCheckout($this, ['replaces_billing_id' => $live->id])->assertSessionHasErrors('replaces_billing_id');

    // Invoice sumber "void" menurut pemeriksaan awal tetapi dihidupkan/dipindah pasien oleh rival sebelum transaction.
    $void = rbLedgerInvoice($this->patient, 100000);
    $void->update(['payment_status' => 'void', 'voided_at' => now(), 'paid_amount' => 0, 'remaining_amount' => 0]);
    $other = Patient::create(['full_name' => 'Pasien Lain', 'whatsapp' => '08555']);

    rbRival('invoice_sequences', fn () => $void->update(['patient_id' => $other->id]), fn () => rbCheckout($this, ['replaces_billing_id' => $void->id])->assertSessionHasErrors('replaces_billing_id'));

    expect(Billing::where('replaces_billing_id', $void->id)->count())->toBe(0);
});

// ======================= SKEMA & BUKTI PARALEL NYATA =======================

it('migration kunci periode additive: tabel finance_period_locks (primary key month) dan tidak ada data historis yang diubah', function () {
    expect(Schema::hasTable('finance_period_locks'))->toBeTrue()
        ->and(Schema::hasColumns('finance_period_locks', ['month', 'created_at', 'updated_at']))->toBeTrue();

    $migration = file_get_contents(base_path('database/migrations/2026_10_15_100000_create_finance_period_locks_table.php'));

    expect($migration)->not->toContain('->update(')->not->toContain('DB::table(')->not->toContain('dropColumn');
});

it('PARALEL NYATA (MySQL): tests/Concurrency/run.php membuktikan closing tanpa drift dan satu pengganti aktif pada proses paralel', function () {
    $process = Process::timeout(900)->env([
        'DB_HOST' => config('database.connections.mysql.host'), 'DB_PORT' => (string) config('database.connections.mysql.port'),
        'DB_USERNAME' => config('database.connections.mysql.username'), 'DB_PASSWORD' => (string) config('database.connections.mysql.password'),
        'RACE_ITERATIONS' => '4',
    ])->run([PHP_BINARY, base_path('tests/Concurrency/run.php')]);

    expect($process->output())->toContain('SEMUA LULUS')->and($process->exitCode())->toBe(0);
})->skip(fn () => config('database.default') !== 'mysql', 'Butuh database uji MySQL (DB_CONNECTION=mysql); skrip membuat database sementara sendiri.');
