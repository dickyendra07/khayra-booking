<?php

use App\Models\Billing;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Services\InvoiceNumberGenerator;
use App\Support\InvoiceNumberIndex;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

function simpleCheckout($test, int $price = 100000)
{
    return $test->post('/admin/cashier/checkout', [
        'patient_id' => Patient::firstOrFail()->id, 'invoice_date' => now()->toDateString(),
        'item_type' => ['service'], 'description' => ['Uji'], 'quantity' => [1], 'unit_price' => [$price],
        'inventory_item_id' => [''], 'clinic_service_id' => [''], 'item_category' => ['other'],
    ]);
}

beforeEach(function () {
    $this->withSession(adminSession());
    Patient::create(['full_name' => 'Pasien No', 'whatsapp' => '081234567890']);
    Carbon::setTestNow('2026-10-11 10:00:00');
});

afterEach(fn () => Carbon::setTestNow());

it('membuat nomor berurutan per bulan dengan format INV-YYYYMM-0001', function () {
    $generator = new InvoiceNumberGenerator;

    expect($generator->next())->toBe('INV-202610-0001')
        ->and($generator->next())->toBe('INV-202610-0002')
        ->and($generator->next(Carbon::parse('2026-11-02')))->toBe('INV-202611-0001')
        ->and($generator->next())->toBe('INV-202610-0003');
});

it('tidak menghasilkan nomor kembar pada checkout di detik yang sama', function () {
    simpleCheckout($this);
    simpleCheckout($this);
    simpleCheckout($this);

    $numbers = Billing::orderBy('id')->pluck('invoice_number')->all();

    expect($numbers)->toBe(['INV-202610-0001', 'INV-202610-0002', 'INV-202610-0003'])->and(array_unique($numbers))->toHaveCount(3);
});

it('melewati nomor yang sudah dipakai invoice lama dan tidak mengubah nomor lama', function () {
    Billing::create(['patient_id' => Patient::first()->id, 'invoice_number' => 'INV-202610-0001', 'invoice_date' => '2026-10-01', 'amount' => 1, 'payment_status' => 'unpaid']);
    Billing::create(['patient_id' => Patient::first()->id, 'invoice_number' => 'INV-20261011-100000', 'invoice_date' => '2026-10-11', 'amount' => 1, 'payment_status' => 'unpaid']);

    expect((new InvoiceNumberGenerator)->next())->toBe('INV-202610-0002')
        ->and(Billing::pluck('invoice_number')->all())->toContain('INV-202610-0001', 'INV-20261011-100000');
});

it('checkout yang gagal tidak menghabiskan nomor (counter ikut rollback)', function () {
    $product = InventoryItem::create(['sku' => 'N-1', 'name' => 'Stok Habis', 'stock' => 0, 'selling_price' => 1000, 'status' => 'active']);

    $this->from('/admin/cashier')->post('/admin/cashier/checkout', [
        'patient_id' => Patient::first()->id, 'invoice_date' => now()->toDateString(),
        'item_type' => ['inventory'], 'description' => ['Stok Habis'], 'quantity' => [1], 'unit_price' => [1000],
        'inventory_item_id' => [$product->id], 'clinic_service_id' => [''], 'item_category' => [''],
    ])->assertSessionHasErrors('stock');

    expect(Billing::count())->toBe(0);

    simpleCheckout($this);

    expect(Billing::firstOrFail()->invoice_number)->toBe('INV-202610-0001');
});

it('invoice manual memakai generator yang sama', function () {
    simpleCheckout($this);

    $this->post('/admin/billings', ['patient_id' => Patient::first()->id, 'invoice_date' => now()->toDateString(), 'amount' => 50000, 'payment_status' => 'unpaid']);

    expect(Billing::orderBy('id')->pluck('invoice_number')->all())->toBe(['INV-202610-0001', 'INV-202610-0002']);
});

it('database menolak nomor invoice kembar lewat unique index', function () {
    expect(InvoiceNumberIndex::exists())->toBeTrue();

    Billing::create(['patient_id' => Patient::first()->id, 'invoice_number' => 'INV-DUP', 'invoice_date' => now()->toDateString(), 'amount' => 1, 'payment_status' => 'unpaid']);

    expect(fn () => Billing::create(['patient_id' => Patient::first()->id, 'invoice_number' => 'INV-DUP', 'invoice_date' => now()->toDateString(), 'amount' => 1, 'payment_status' => 'unpaid']))
        ->toThrow(QueryException::class);
});

it('unique index tidak dipasang bila ada duplikat lama, data tidak diubah, dan dipasang setelah bersih', function () {
    InvoiceNumberIndex::drop();
    expect(InvoiceNumberIndex::exists())->toBeFalse();

    foreach ([1, 2] as $i) {
        DB::table('billings')->insert(['patient_id' => Patient::first()->id, 'invoice_number' => 'INV-LAMA', 'invoice_date' => '2026-01-01', 'amount' => $i, 'created_at' => now(), 'updated_at' => now()]);
    }

    expect(InvoiceNumberIndex::duplicates())->toBe([['invoice_number' => 'INV-LAMA', 'total' => 2]])
        ->and(InvoiceNumberIndex::ensure())->toBe('blocked_by_duplicates')
        ->and(InvoiceNumberIndex::exists())->toBeFalse()
        ->and(Billing::where('invoice_number', 'INV-LAMA')->count())->toBe(2);

    $this->artisan('billing:ensure-invoice-unique-index')->assertFailed();

    DB::table('billings')->where('invoice_number', 'INV-LAMA')->where('amount', 2)->update(['invoice_number' => 'INV-LAMA-2']);

    $this->artisan('billing:ensure-invoice-unique-index')->assertSuccessful();
    expect(InvoiceNumberIndex::exists())->toBeTrue();
});

it('menganggap beberapa invoice tanpa nomor (NULL) bukan duplikat', function () {
    InvoiceNumberIndex::drop();

    foreach ([1, 2] as $i) {
        DB::table('billings')->insert(['patient_id' => Patient::first()->id, 'invoice_number' => null, 'invoice_date' => '2026-01-01', 'amount' => $i, 'created_at' => now(), 'updated_at' => now()]);
    }

    expect(InvoiceNumberIndex::duplicates())->toBe([])->and(InvoiceNumberIndex::ensure())->toBe('created');
});

it('tabel pendukung hasil migration tersedia dan kolom lama tidak hilang', function () {
    expect(Schema::hasTable('invoice_sequences'))->toBeTrue()
        ->and(Schema::hasTable('billing_audit_logs'))->toBeTrue()
        ->and(Schema::hasColumn('billings', 'replaces_billing_id'))->toBeTrue()
        ->and(Schema::hasColumn('finance_monthly_closings', 'reopened_at'))->toBeTrue()
        ->and(Schema::hasColumns('billings', ['invoice_number', 'amount', 'paid_amount', 'discount_amount', 'payment_status']))->toBeTrue();
});
