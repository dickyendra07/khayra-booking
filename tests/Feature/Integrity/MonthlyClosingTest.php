<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\FinanceMonthlyClosing;
use App\Models\FinanceTransaction;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'khayra_auth.admin.email' => 'admin@uji.test',
        'khayra_auth.admin.password' => 'sandi-uji-lokal',
        'khayra_auth.admin.password_hash' => null,
    ]);

    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);
    $this->patient = Patient::create(['full_name' => 'Pasien Closing', 'whatsapp' => '081234567890']);
    $this->month = now()->format('Y-m');

    Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-C-1', 'invoice_date' => now()->toDateString(), 'amount' => 400000, 'paid_amount' => 400000, 'payment_status' => 'paid']);
});

function reopenPayload(string $month, array $overrides = []): array
{
    return array_merge(['month' => $month, 'reason' => 'Koreksi invoice salah input nominal', 'confirm_month' => $month, 'password' => 'sandi-uji-lokal'], $overrides);
}

it('menutup buku sekali dan mencatat audit period_closed', function () {
    $this->post('/admin/owner-finance/close', ['month' => $this->month, 'notes' => 'final'])->assertRedirect();

    $closing = FinanceMonthlyClosing::firstOrFail();

    expect((float) $closing->billing_income)->toBe(400000.0)
        ->and($closing->closed_by)->toBe('admin@uji.test')
        ->and($closing->reopened_at)->toBeNull()
        ->and(BillingAuditLog::where('action', 'period_closed')->where('finance_month', $this->month)->count())->toBe(1);
});

it('menolak penutupan ulang yang menimpa closing dan snapshot tidak berubah', function () {
    $this->post('/admin/owner-finance/close', ['month' => $this->month, 'notes' => 'asli']);
    $original = FinanceMonthlyClosing::firstOrFail()->only(['billing_income', 'total_income', 'notes', 'closed_at', 'snapshot']);

    Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-C-2', 'invoice_date' => now()->toDateString(), 'amount' => 999999, 'paid_amount' => 999999, 'payment_status' => 'paid']);

    $this->post('/admin/owner-finance/close', ['month' => $this->month, 'notes' => 'menimpa'])->assertSessionHas('error');

    expect(FinanceMonthlyClosing::count())->toBe(1)
        ->and(FinanceMonthlyClosing::firstOrFail()->only(['billing_income', 'total_income', 'notes', 'closed_at', 'snapshot']))->toEqual($original);
});

it('menolak transaksi finance manual pada bulan yang sudah ditutup (tambah dan hapus)', function () {
    $this->post('/admin/owner-finance/close', ['month' => $this->month]);
    $transaction = FinanceTransaction::create(['transaction_date' => now()->toDateString(), 'type' => 'expense', 'source' => 'operational', 'title' => 'Uji', 'amount' => 1000]);

    $this->post('/admin/owner-finance/transactions', ['transaction_date' => now()->toDateString(), 'type' => 'income', 'title' => 'Baru', 'amount' => 5000])->assertSessionHas('error');
    $this->post("/admin/owner-finance/transactions/{$transaction->id}/delete")->assertSessionHas('error');

    expect(FinanceTransaction::count())->toBe(1);
});

it('membuka kembali buku hanya dengan alasan, konfirmasi bulan, dan password yang benar', function () {
    $this->post('/admin/owner-finance/close', ['month' => $this->month]);

    $this->post('/admin/owner-finance/reopen', reopenPayload($this->month, ['reason' => 'pendek']))->assertSessionHasErrors('reason');
    $this->post('/admin/owner-finance/reopen', reopenPayload($this->month, ['confirm_month' => '1999-01']))->assertSessionHas('error');
    $this->post('/admin/owner-finance/reopen', reopenPayload($this->month, ['password' => 'salah']))->assertSessionHas('error');

    expect(FinanceMonthlyClosing::isClosed($this->month))->toBeTrue();

    $this->post('/admin/owner-finance/reopen', reopenPayload($this->month))->assertSessionHas('success');

    $closing = FinanceMonthlyClosing::firstOrFail();
    $log = BillingAuditLog::where('action', 'period_reopened')->firstOrFail();

    expect(FinanceMonthlyClosing::isClosed($this->month))->toBeFalse()
        ->and($closing->reopen_reason)->toBe('Koreksi invoice salah input nominal')
        ->and($closing->reopened_by)->toBe('admin@uji.test')
        ->and((float) $closing->billing_income)->toBe(400000.0)   // data closing lama tidak diubah
        ->and($closing->snapshot)->not->toBeNull()
        ->and($log->reason)->toBe('Koreksi invoice salah input nominal')
        ->and((float) $log->before['billing_income'])->toBe(400000.0);
});

it('membatasi percobaan password salah pada prosedur buka kembali', function () {
    $this->post('/admin/owner-finance/close', ['month' => $this->month]);

    foreach (range(1, 5) as $i) {
        $this->post('/admin/owner-finance/reopen', reopenPayload($this->month, ['password' => 'salah']))->assertSessionHas('error');
    }

    // Percobaan berikutnya diblokir walaupun password benar.
    $this->post('/admin/owner-finance/reopen', reopenPayload($this->month));

    expect(session('error'))->toContain('Terlalu banyak percobaan')->and(FinanceMonthlyClosing::isClosed($this->month))->toBeTrue();
});

it('menolak buka kembali untuk bulan yang tidak tertutup', function () {
    $this->post('/admin/owner-finance/reopen', reopenPayload($this->month))->assertSessionHas('error');

    expect(BillingAuditLog::where('action', 'period_reopened')->count())->toBe(0);
});

it('mengizinkan tutup ulang setelah dibuka dan menyimpan closing sebelumnya di audit', function () {
    $this->post('/admin/owner-finance/close', ['month' => $this->month, 'notes' => 'pertama']);
    $this->post('/admin/owner-finance/reopen', reopenPayload($this->month));

    Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-C-3', 'invoice_date' => now()->toDateString(), 'amount' => 100000, 'paid_amount' => 100000, 'payment_status' => 'paid']);

    $this->post('/admin/owner-finance/close', ['month' => $this->month, 'notes' => 'kedua'])->assertSessionHas('success');

    $closing = FinanceMonthlyClosing::firstOrFail();
    $closeLogs = BillingAuditLog::where('action', 'period_closed')->orderBy('id')->get();

    expect((float) $closing->billing_income)->toBe(500000.0)
        ->and($closing->reopened_at)->toBeNull()
        ->and(FinanceMonthlyClosing::isClosed($this->month))->toBeTrue()
        ->and($closeLogs)->toHaveCount(2)
        ->and((float) $closeLogs[1]->before['previous_closing']['billing_income'])->toBe(400000.0)
        ->and($closeLogs[1]->before['previous_closing']['snapshot'])->not->toBeNull();
});

it('owner finance tetap konsisten: invoice void dikeluarkan dan pengganti dihitung sekali', function () {
    $original = Billing::firstOrFail();
    $this->post("/admin/billings/{$original->id}/void", ['void_reason' => 'Salah nominal'])->assertRedirect();

    $this->post('/admin/billings', ['patient_id' => $this->patient->id, 'invoice_date' => now()->toDateString(), 'amount' => 350000, 'payment_status' => 'paid']);

    $this->get('/admin/owner-finance?month='.$this->month)->assertOk()
        ->assertViewHas('summary', fn ($s) => (float) $s['billing_income'] === 350000.0 && $s['transaction_count'] === 1);
});

it('menampilkan peringatan bila snapshot closing berbeda dari data billing saat ini (tanpa mengubah snapshot)', function () {
    $this->post('/admin/owner-finance/close', ['month' => $this->month]);

    // Simulasi data lama yang berubah sebelum proteksi ada (langsung di database).
    Billing::query()->update(['paid_amount' => 100000, 'amount' => 100000]);

    $this->get('/admin/owner-finance?month='.$this->month)->assertOk()
        ->assertSee('data berbeda dari snapshot closing')
        ->assertViewHas('closingDrift');

    expect((float) FinanceMonthlyClosing::firstOrFail()->billing_income)->toBe(400000.0);
});

it('menampilkan form buka kembali pada bulan tertutup', function () {
    $this->post('/admin/owner-finance/close', ['month' => $this->month]);

    $this->get('/admin/owner-finance?month='.$this->month)->assertOk()
        ->assertSee('Buka Kembali Buku')->assertSee('name="confirm_month"', false);
});
