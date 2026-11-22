<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\FinanceMonthlyClosing;
use App\Models\InventoryItem;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withSession(adminSession(['admin_email' => 'admin@uji.test']));
    $this->patient = Patient::create(['full_name' => 'Pasien Lock', 'whatsapp' => '081234567890']);
});

function mkInvoice(array $attrs = [], bool $withItem = false): Billing
{
    $billing = Billing::create(array_merge([
        'patient_id' => Patient::firstOrFail()->id, 'invoice_number' => 'INV-T-'.uniqid(), 'invoice_date' => now()->toDateString(),
        'subtotal_amount' => 500000, 'discount_amount' => 0, 'amount' => 500000,
        'paid_amount' => 0, 'remaining_amount' => 500000, 'change_amount' => 0, 'payment_status' => 'unpaid',
    ], $attrs));

    if ($withItem) {
        $billing->items()->create(['item_type' => 'service', 'item_category' => 'program', 'description' => 'Program', 'quantity' => 1, 'unit_price' => 500000, 'line_total' => 500000]);
    }

    return $billing;
}

function updatePayload(Billing $billing, array $overrides = []): array
{
    return array_merge([
        'patient_id' => $billing->patient_id, 'invoice_date' => $billing->invoice_date->format('Y-m-d'),
        'amount' => $billing->amount, 'payment_status' => $billing->payment_status, 'payment_method' => $billing->payment_method,
        'notes' => $billing->notes,
    ], $overrides);
}

function closeMonthOf(string $date): void
{
    $start = Carbon::parse($date)->startOfMonth();

    FinanceMonthlyClosing::create([
        'month' => $start->format('Y-m'), 'period_start' => $start->toDateString(), 'period_end' => $start->copy()->endOfMonth()->toDateString(),
        'billing_income' => 0, 'manual_income' => 0, 'total_income' => 0, 'total_expense' => 0, 'net_cashflow' => 0,
        'transaction_count' => 0, 'closed_by' => 'uji', 'closed_at' => now(),
    ]);
}

// ---------------- invoice lunas / itemised ----------------

it('menolak perubahan nominal pada invoice lunas (nominal tetap, hanya catatan yang boleh berubah)', function () {
    $billing = mkInvoice(['payment_status' => 'paid', 'paid_amount' => 500000, 'remaining_amount' => 0], withItem: true);

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['amount' => 1000, 'notes' => 'catatan baru']))
        ->assertRedirect("/admin/billings/{$billing->id}");

    $billing->refresh();

    expect((float) $billing->amount)->toBe(500000.0)
        ->and((float) $billing->paid_amount)->toBe(500000.0)
        ->and($billing->notes)->toBe('catatan baru')
        ->and($billing->items()->sum('line_total'))->toEqual(500000);
});

it('tidak mengizinkan pindah pasien, tanggal, atau visit pada invoice yang sudah dibayar', function () {
    $other = Patient::create(['full_name' => 'Lain', 'whatsapp' => '08999']);
    $billing = mkInvoice(['payment_status' => 'paid', 'paid_amount' => 500000, 'remaining_amount' => 0]);

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['patient_id' => $other->id, 'invoice_date' => '2020-01-01']));

    $billing->refresh();

    expect($billing->patient_id)->toBe($this->patient->id)->and($billing->invoice_date->format('Y-m-d'))->toBe(now()->toDateString());
});

it('mengunci nominal invoice itemised walaupun belum dibayar', function () {
    $billing = mkInvoice(withItem: true);

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['amount' => 1]));

    expect((float) $billing->fresh()->amount)->toBe(500000.0);
});

it('menangani invoice partial dengan aman: nominal dan histori pembayaran tidak berubah', function () {
    $billing = mkInvoice(['payment_status' => 'partial', 'paid_amount' => 200000, 'remaining_amount' => 300000]);

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['amount' => 10, 'payment_status' => 'unpaid']))
        ->assertSessionHasErrors('payment_status');

    $billing->refresh();

    expect((float) $billing->amount)->toBe(500000.0)
        ->and((float) $billing->paid_amount)->toBe(200000.0)
        ->and($billing->payment_status)->toBe('partial');
});

it('memperbolehkan perubahan invoice manual yang belum dibayar, dengan alasan, dan mencatatnya di audit', function () {
    $billing = mkInvoice();

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['amount' => 450000]))
        ->assertSessionHasErrors('change_reason');
    expect((float) $billing->fresh()->amount)->toBe(500000.0);

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['amount' => 450000, 'change_reason' => 'Salah input nominal']))
        ->assertRedirect("/admin/billings/{$billing->id}");

    $billing->refresh();
    $log = BillingAuditLog::where('billing_id', $billing->id)->where('action', 'updated')->firstOrFail();

    expect((float) $billing->amount)->toBe(450000.0)
        ->and((float) $billing->remaining_amount)->toBe(450000.0)
        ->and((float) $log->before['amount'])->toBe(500000.0)
        ->and((float) $log->after['amount'])->toBe(450000.0)
        ->and($log->reason)->toBe('Salah input nominal')
        ->and($log->actor)->toBe('admin@uji.test');
});

// ---------------- transisi status ----------------

it('tidak mengizinkan status lunas diturunkan dan paid_amount tetap', function () {
    $billing = mkInvoice(['payment_status' => 'paid', 'paid_amount' => 500000, 'remaining_amount' => 0]);

    $this->post("/admin/billings/{$billing->id}/status", ['payment_status' => 'unpaid'])->assertSessionHas('error');
    $this->post("/admin/billings/{$billing->id}/status", ['payment_status' => 'partial'])->assertSessionHas('error');

    expect($billing->fresh())->payment_status->toBe('paid')->and((float) $billing->fresh()->paid_amount)->toBe(500000.0);
});

it('tidak mengizinkan partial diturunkan ke unpaid atau unpaid diubah ke partial tanpa nominal', function () {
    $partial = mkInvoice(['payment_status' => 'partial', 'paid_amount' => 100000, 'remaining_amount' => 400000]);
    $unpaid = mkInvoice();

    $this->post("/admin/billings/{$partial->id}/status", ['payment_status' => 'unpaid'])->assertSessionHas('error');
    $this->post("/admin/billings/{$unpaid->id}/status", ['payment_status' => 'partial'])->assertSessionHas('error');

    expect((float) $partial->fresh()->paid_amount)->toBe(100000.0)->and($unpaid->fresh()->payment_status)->toBe('unpaid');
});

it('melunasi invoice menyamakan paid, remaining, dan change secara konsisten serta tercatat di audit', function () {
    $billing = mkInvoice(['payment_status' => 'partial', 'paid_amount' => 200000, 'remaining_amount' => 300000]);

    $this->post("/admin/billings/{$billing->id}/status", ['payment_status' => 'paid'])->assertSessionHas('success');

    $billing->refresh();
    $log = BillingAuditLog::where('billing_id', $billing->id)->where('action', 'status_changed')->firstOrFail();

    expect($billing->payment_status)->toBe('paid')
        ->and((float) $billing->paid_amount)->toBe(500000.0)
        ->and((float) $billing->remaining_amount)->toBe(0.0)
        ->and($log->before['payment_status'])->toBe('partial')
        ->and($log->after['payment_status'])->toBe('paid')
        ->and((float) $log->before['paid_amount'])->toBe(200000.0);
});

// ---------------- hapus & void ----------------

it('tidak melakukan hard delete invoice dan semua data tetap ada', function () {
    $billing = mkInvoice(['payment_status' => 'paid', 'paid_amount' => 500000, 'remaining_amount' => 0], withItem: true);

    $this->post("/admin/billings/{$billing->id}/delete")->assertRedirect("/admin/billings/{$billing->id}")->assertSessionHas('error');

    expect(Billing::find($billing->id))->not->toBeNull()->and($billing->items()->count())->toBe(1);
});

it('void mempertahankan jumlah yang pernah dibayar di audit, item tetap ada, dan stok kembali', function () {
    $product = InventoryItem::create(['sku' => 'V-1', 'name' => 'Tape', 'stock' => 3, 'selling_price' => 40000, 'status' => 'active']);
    $billing = mkInvoice(['payment_status' => 'paid', 'paid_amount' => 500000, 'remaining_amount' => 0]);
    $billing->items()->create(['item_type' => 'inventory', 'inventory_item_id' => $product->id, 'description' => 'Tape', 'quantity' => 2, 'unit_price' => 40000, 'line_total' => 80000]);

    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'Salah input item'])->assertRedirect();

    $billing->refresh();
    $log = BillingAuditLog::where('billing_id', $billing->id)->where('action', 'voided')->firstOrFail();

    expect($billing->payment_status)->toBe('void')
        ->and((float) $log->before['paid_amount'])->toBe(500000.0)
        ->and($log->reason)->toBe('Salah input item')
        ->and($billing->items()->count())->toBe(1)
        ->and($product->fresh()->stock)->toBe(5);
});

it('invoice void tidak dapat diedit atau diubah statusnya', function () {
    $billing = mkInvoice(['payment_status' => 'void', 'voided_at' => now(), 'paid_amount' => 0, 'remaining_amount' => 0]);

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['amount' => 1, 'notes' => 'ubah']))->assertSessionHas('error');
    $this->post("/admin/billings/{$billing->id}/status", ['payment_status' => 'paid'])->assertSessionHas('error');

    expect((float) $billing->fresh()->amount)->toBe(500000.0)->and($billing->fresh()->notes)->toBeNull();
});

// ---------------- periode tutup buku ----------------

it('melindungi invoice pada periode tutup buku dari edit, status, void, dan delete', function () {
    $date = now()->subMonths(2)->startOfMonth()->addDays(4)->toDateString();
    $billing = mkInvoice(['invoice_date' => $date]);
    closeMonthOf($date);

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['amount' => 1, 'change_reason' => 'coba ubah', 'notes' => 'catatan boleh']));
    $this->post("/admin/billings/{$billing->id}/status", ['payment_status' => 'paid'])->assertSessionHas('error');
    $this->post("/admin/billings/{$billing->id}/void", ['void_reason' => 'coba void'])->assertSessionHas('error');
    $this->post("/admin/billings/{$billing->id}/delete")->assertSessionHas('error');

    $billing->refresh();

    // Nominal, status, dan void tidak berubah; hanya catatan non-finansial yang diizinkan.
    expect((float) $billing->amount)->toBe(500000.0)
        ->and($billing->payment_status)->toBe('unpaid')
        ->and($billing->voided_at)->toBeNull()
        ->and($billing->notes)->toBe('catatan boleh');
});

it('menolak invoice baru (kasir dan manual) pada periode tutup buku', function () {
    $date = now()->subMonths(2)->startOfMonth()->addDays(2)->toDateString();
    closeMonthOf($date);

    $this->post('/admin/billings', [
        'patient_id' => $this->patient->id, 'invoice_date' => $date, 'amount' => 100000, 'payment_status' => 'unpaid',
    ])->assertSessionHasErrors('invoice_date');

    $this->post('/admin/cashier/checkout', [
        'patient_id' => $this->patient->id, 'invoice_date' => $date,
        'item_type' => ['service'], 'description' => ['Uji'], 'quantity' => [1], 'unit_price' => [100000],
        'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
    ])->assertSessionHasErrors('invoice_date');

    expect(Billing::count())->toBe(0);
});

it('menolak memindahkan tanggal invoice ke periode yang sudah tutup buku', function () {
    $closedDate = now()->subMonths(2)->startOfMonth()->addDays(3)->toDateString();
    closeMonthOf($closedDate);
    $billing = mkInvoice();

    $this->post("/admin/billings/{$billing->id}/update", updatePayload($billing, ['invoice_date' => $closedDate, 'change_reason' => 'pindah tanggal']))
        ->assertSessionHasErrors('invoice_date');

    expect($billing->fresh()->invoice_date->format('Y-m-d'))->toBe(now()->toDateString());
});

it('invoice pada bulan yang dibuka kembali boleh dikoreksi lagi', function () {
    $date = now()->subMonths(2)->startOfMonth()->addDays(4)->toDateString();
    $billing = mkInvoice(['invoice_date' => $date]);
    closeMonthOf($date);
    FinanceMonthlyClosing::query()->update(['reopened_at' => now(), 'reopened_by' => 'uji', 'reopen_reason' => 'koreksi']);

    $this->post("/admin/billings/{$billing->id}/status", ['payment_status' => 'paid'])->assertSessionHas('success');

    expect($billing->fresh()->payment_status)->toBe('paid');
});

// ---------------- audit log ----------------

it('audit log bersifat append-only (tidak bisa diubah atau dihapus)', function () {
    $log = BillingAuditLog::create(['action' => 'uji', 'actor' => 'x']);

    expect(fn () => $log->update(['action' => 'ubah']))->toThrow(LogicException::class)
        ->and(fn () => $log->delete())->toThrow(LogicException::class)
        ->and(BillingAuditLog::count())->toBe(1);
});

it('membuat invoice manual mencatat audit created dan menyelaraskan field pembayaran', function () {
    $this->post('/admin/billings', [
        'patient_id' => $this->patient->id, 'invoice_date' => now()->toDateString(), 'amount' => 300000, 'payment_status' => 'paid', 'payment_method' => 'cash',
    ])->assertRedirect('/admin/billings');

    $billing = Billing::firstOrFail();

    expect((float) $billing->paid_amount)->toBe(300000.0)
        ->and((float) $billing->remaining_amount)->toBe(0.0)
        ->and($billing->payment_method)->toBe('cash')
        ->and(BillingAuditLog::where('billing_id', $billing->id)->where('action', 'created')->exists())->toBeTrue();
});

it('invoice manual partial wajib menyebut nominal terbayar yang valid', function () {
    $base = ['patient_id' => $this->patient->id, 'invoice_date' => now()->toDateString(), 'amount' => 300000, 'payment_status' => 'partial'];

    $this->post('/admin/billings', $base)->assertSessionHasErrors('paid_amount');
    $this->post('/admin/billings', $base + ['paid_amount' => 300000])->assertSessionHasErrors('paid_amount');
    $this->post('/admin/billings', $base + ['paid_amount' => 100000])->assertRedirect('/admin/billings');

    $billing = Billing::firstOrFail();
    expect((float) $billing->paid_amount)->toBe(100000.0)->and((float) $billing->remaining_amount)->toBe(200000.0);
});

it('menampilkan halaman detail dan edit untuk invoice lama, baru, dan void tanpa error', function () {
    $legacy = mkInvoice(['payment_status' => 'paid', 'paid_amount' => 0, 'remaining_amount' => 0, 'invoice_number' => 'INV-LAMA']);
    $void = mkInvoice(['payment_status' => 'void', 'voided_at' => now(), 'void_reason' => 'uji']);
    $itemised = mkInvoice(withItem: true);

    foreach ([$legacy, $void, $itemised] as $billing) {
        $this->get("/admin/billings/{$billing->id}")->assertOk()->assertSee('Riwayat Audit');
        $this->get("/admin/billings/{$billing->id}/edit")->assertOk();
    }

    $this->get("/admin/billings/{$void->id}")->assertSee('Terbitkan Ulang');
    $this->get("/admin/billings/{$legacy->id}/edit")->assertSee('dikunci');
});
