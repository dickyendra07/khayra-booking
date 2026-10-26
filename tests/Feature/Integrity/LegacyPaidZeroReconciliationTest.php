<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\BillingPayment;
use App\Models\FinanceMonthlyClosing;
use App\Models\Patient;
use App\Services\BillingMoney;
use App\Services\FinanceReport;
use App\Services\MonthlyClosing;
use App\Support\ClinicTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

/**
 * P0.2 — Invoice lama (payment_model = legacy) berstatus `paid` dengan paid_amount = 0.
 * Data sintetis yang meniru pola invoice produksi #2, #3, #7. Semua test juga membuktikan data TIDAK diubah.
 */
beforeEach(function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);
    $this->patient = Patient::create(['full_name' => 'Pasien Legacy Uji', 'whatsapp' => '081200000001']);
});

function paidZero(array $attrs = []): Billing
{
    return Billing::create(array_merge([
        'patient_id' => Patient::firstOrFail()->id, 'invoice_number' => 'INV-OLD-'.uniqid(), 'invoice_date' => ClinicTime::today(),
        'subtotal_amount' => 300000, 'amount' => 300000, 'paid_amount' => 0, 'remaining_amount' => 300000, 'payment_status' => 'paid',
    ], $attrs));
}

function snapshotOf(Billing $billing): array
{
    return $billing->fresh()->only(['amount', 'paid_amount', 'remaining_amount', 'payment_status', 'payment_model', 'discount_amount']);
}

it('kas, omzet, dan dashboard menghitung invoice paid/paid_amount 0 tepat satu kali (tanpa under-reporting atau ganda)', function () {
    $billing = paidZero();
    paidZero(['amount' => 200000, 'subtotal_amount' => 200000]);

    $summary = FinanceReport::forMonth(ClinicTime::monthKey())['summary'];

    expect((float) $summary['billing_income'])->toBe(500000.0)
        ->and((float) $summary['invoice_revenue'])->toBe(500000.0)
        ->and((float) $summary['refunds'])->toBe(0.0)
        ->and(BillingPayment::count())->toBe(0)
        ->and(BillingMoney::paid($billing))->toBe(300000.0);
});

it('invoice paid/paid_amount 0 yang di-void keluar dari kas dan omzet', function () {
    $billing = paidZero();
    $billing->update(['payment_status' => 'void']);

    $summary = FinanceReport::forMonth(ClinicTime::monthKey())['summary'];

    expect((float) $summary['billing_income'])->toBe(0.0)->and((float) $summary['invoice_revenue'])->toBe(0.0);
});

it('invoice lama bersama invoice ledger dalam bulan yang sama: kas = jumlah keduanya, tidak ganda', function () {
    paidZero();
    $ledger = Billing::create([
        'patient_id' => $this->patient->id, 'invoice_number' => 'INV-NEW-1', 'invoice_date' => ClinicTime::today(),
        'subtotal_amount' => 100000, 'amount' => 100000, 'paid_amount' => 100000, 'remaining_amount' => 0, 'payment_status' => 'paid', 'payment_model' => 'ledger',
    ]);
    BillingPayment::create([
        'billing_id' => $ledger->id, 'type' => BillingPayment::TYPE_PAYMENT, 'method' => 'cash', 'amount' => 100000,
        'tendered_amount' => 100000, 'change_amount' => 0, 'paid_at' => now(), 'paid_on' => ClinicTime::today(), 'recorded_by' => 'uji',
    ]);

    $summary = FinanceReport::forMonth(ClinicTime::monthKey())['summary'];

    expect((float) $summary['billing_income'])->toBe(400000.0)->and((float) $summary['invoice_revenue'])->toBe(400000.0);
});

it('snapshot tutup buku memuat invoice paid/paid_amount 0 sebesar amount dan tidak mengubah invoice', function () {
    $billing = paidZero();
    $before = snapshotOf($billing);

    $closing = MonthlyClosing::close(ClinicTime::monthKey(), 'uji', 'admin@uji.test');

    expect((float) $closing->billing_income)->toBe(300000.0)
        ->and(snapshotOf($billing))->toBe($before);
});

it('audit integritas melaporkan invoice paid/paid_amount 0 sebagai temuan informatif, bukan mengubahnya', function () {
    $billing = paidZero();
    $before = snapshotOf($billing);

    $exit = Artisan::call('billing:audit-integrity');
    $output = Artisan::output();

    expect($exit)->toBe(0)
        ->and($output)->toContain('billing #'.$billing->id)
        ->and(snapshotOf($billing))->toBe($before);
});

it('halaman admin menampilkan Dibayar = amount dan Sisa = 0 untuk invoice paid/paid_amount 0 (tidak "Rp 0")', function () {
    $billing = paidZero(['amount' => 345000, 'subtotal_amount' => 345000, 'remaining_amount' => 345000]);
    $before = snapshotOf($billing);

    foreach (["/admin/billings/{$billing->id}", "/admin/billings/{$billing->id}/print", '/admin/billings'] as $url) {
        $html = $this->get($url)->assertOk()->getContent();
        $row = preg_replace('/\s+/', ' ', $html);

        expect($row)->toContain('Rp 345.000');
    }

    $detail = preg_replace('/\s+/', ' ', strip_tags($this->get("/admin/billings/{$billing->id}")->getContent()));
    expect($detail)->toMatch('/Paid Amount\s*Rp 345\.000/')->and($detail)->toMatch('/Remaining\s*Rp 0(?!\.)/');

    expect(snapshotOf($billing))->toBe($before);
});

it('detail pasien: total Paid memakai fallback yang sama dan Outstanding tidak menghitung invoice paid', function () {
    paidZero();
    paidZero(['amount' => 200000, 'subtotal_amount' => 200000, 'remaining_amount' => 200000]);

    $this->get("/admin/patients/{$this->patient->id}")->assertOk()
        ->assertViewHas('paidTotal', fn ($value) => (float) $value === 500000.0)
        ->assertViewHas('outstandingTotal', fn ($value) => (float) $value === 0.0)
        ->assertViewHas('revenueTotal', fn ($value) => (float) $value === 500000.0);
});

it('laporan revenue CSV mengekspor paid = amount dan remaining = 0 untuk invoice paid/paid_amount 0', function () {
    paidZero();

    $csv = $this->get('/admin/reports/revenue?export=csv&date_from='.ClinicTime::monthKey().'-01&date_to='.ClinicTime::now()->endOfMonth()->toDateString())->streamedContent();
    $rows = array_map('str_getcsv', array_filter(explode("\n", $csv)));
    $header = array_shift($rows);
    $row = array_combine($header, $rows[0]);

    expect((float) $row['amount'])->toBe(300000.0)->and((float) $row['paid'])->toBe(300000.0)->and((float) $row['remaining'])->toBe(0.0);
});

it('kartu Net Revenue dashboard = omzet invoice non-void (diskon tidak dikurangkan dua kali)', function () {
    // amount sudah NET diskon: subtotal 600.000 - diskon 100.000 = 500.000, dibayar penuh.
    paidZero(['subtotal_amount' => 600000, 'discount_amount' => 100000, 'amount' => 500000, 'paid_amount' => 500000, 'remaining_amount' => 0]);

    $this->get('/admin/dashboard')->assertOk()
        ->assertViewHas('monthlyNetRevenue', fn ($value) => (float) $value === 500000.0)
        ->assertViewHas('monthlyPaidAmount', fn ($value) => (float) $value === 500000.0);
});

it('menampilkan halaman dan laporan tidak pernah menulis audit/koreksi pada invoice lama', function () {
    $billing = paidZero();
    $auditBefore = BillingAuditLog::count();
    $before = snapshotOf($billing);

    $this->get('/admin/owner-finance')->assertOk();
    $this->get('/admin/owner-dashboard')->assertOk();
    $this->get("/admin/billings/{$billing->id}")->assertOk();
    Artisan::call('billing:audit-integrity', ['--strict' => true]);

    expect(snapshotOf($billing))->toBe($before)
        ->and(BillingAuditLog::count())->toBe($auditBefore)
        ->and(BillingPayment::count())->toBe(0)
        ->and(FinanceMonthlyClosing::count())->toBe(0);
});
