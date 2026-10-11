<?php

use App\Models\Billing;
use App\Models\BillingAuditLog;
use App\Models\ClinicService;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'admin@uji.test']);
    $this->patient = Patient::create(['full_name' => 'Pasien Diskon', 'whatsapp' => '081234567890']);
    $this->program = ClinicService::create(['name' => 'Prog', 'price_per_visit' => 500000, 'package_3x_price' => 1400000, 'category' => 'Program', 'status' => 'active']);
    $this->addOn = ClinicService::create(['name' => '+ Add', 'price_per_visit' => 100000, 'category' => 'Add-on', 'status' => 'active']);
    $this->promo = Promo::create(['code' => 'D10', 'name' => '10%', 'discount_type' => 'percent', 'discount_value' => 10, 'status' => 'active']);
});

function dhPost($test, array $lines, array $extra = [])
{
    $payload = array_merge([
        'patient_id' => Patient::firstOrFail()->id, 'invoice_date' => now()->toDateString(), 'paid_amount' => 0,
        'item_type' => [], 'description' => [], 'quantity' => [], 'unit_price' => [],
        'inventory_item_id' => [], 'clinic_service_id' => [], 'item_category' => [], 'price_reason' => [],
    ], $extra);

    foreach ($lines as $line) {
        $payload['item_type'][] = $line['type'] ?? 'service';
        $payload['description'][] = $line['description'];
        $payload['quantity'][] = $line['quantity'] ?? 1;
        $payload['unit_price'][] = $line['price'];
        $payload['inventory_item_id'][] = $line['inventory_item_id'] ?? '';
        $payload['clinic_service_id'][] = isset($line['service']) ? $line['service']->id : '';
        $payload['item_category'][] = $line['category'] ?? '';
        $payload['price_reason'][] = $line['reason'] ?? '';
    }

    return $test->post('/admin/cashier/checkout', $payload);
}

// ---------------- baris manual & kategori ----------------

it('baris manual tanpa kategori eksplisit tidak didiskon (default non-discountable)', function () {
    dhPost($this, [
        ['description' => 'Layanan manual', 'price' => 200000],                                  // tanpa kategori
        ['description' => 'Program master', 'price' => 500000, 'service' => $this->program],
    ], ['promo_id' => $this->promo->id]);

    $billing = Billing::with('items')->firstOrFail();

    expect((float) $billing->discount_amount)->toBe(50000.0)   // hanya dari program master
        ->and($billing->items->firstWhere('description', 'Layanan manual')->item_category)->toBe('other')
        ->and($billing->items->firstWhere('description', 'Layanan manual')->is_discount_eligible)->toBeFalse();
});

it('baris manual hanya didiskon bila admin memilih Program secara eksplisit', function () {
    dhPost($this, [['description' => 'Treatment manual', 'price' => 200000, 'category' => 'program']], ['promo_id' => $this->promo->id]);

    expect((float) Billing::firstOrFail()->discount_amount)->toBe(20000.0);
});

it('kategori tak dikenal dari form manual jatuh ke other, bukan program', function () {
    dhPost($this, [['description' => 'Manual', 'price' => 100000, 'category' => 'PROGRAM-PALSU']]);

    expect(Billing::with('items')->firstOrFail()->items->first()->item_category)->toBe('other');
});

it('kategori master dengan huruf tidak baku tetap dikenali (tidak diam-diam jadi lain)', function () {
    $this->program->update(['category' => ' program ']);
    $this->addOn->update(['category' => 'ADD ON']);

    dhPost($this, [
        ['description' => 'Prog', 'price' => 500000, 'service' => $this->program],
        ['description' => '+ Add', 'price' => 100000, 'service' => $this->addOn],
    ], ['promo_id' => $this->promo->id]);

    expect((float) Billing::firstOrFail()->amount)->toBe(550000.0);
});

it('menormalkan kategori saat layanan master dibuat dan diubah', function () {
    $this->post('/admin/services', ['name' => 'Baru', 'price_per_visit' => 1000, 'category' => ' program ', 'status' => 'active'])->assertRedirect('/admin/services');
    $this->post('/admin/services', ['name' => 'Baru2', 'price_per_visit' => 1000, 'category' => 'add on', 'status' => 'active']);
    $this->post('/admin/services', ['name' => 'Baru3', 'price_per_visit' => 1000, 'category' => 'Kategori Khusus', 'status' => 'active']);

    expect(ClinicService::where('name', 'Baru')->first()->category)->toBe('Program')
        ->and(ClinicService::where('name', 'Baru2')->first()->category)->toBe('Add-on')
        ->and(ClinicService::where('name', 'Baru3')->first()->category)->toBe('Kategori Khusus');

    $this->post("/admin/services/{$this->addOn->id}/update", ['name' => '+ Add', 'price_per_visit' => 100000, 'category' => 'ADDON', 'status' => 'active']);
    expect($this->addOn->fresh()->category)->toBe('Add-on');
});

// ---------------- perubahan harga ----------------

it('harga master (per visit atau paket) tidak memerlukan alasan', function () {
    dhPost($this, [['description' => 'Paket 3x', 'price' => 1400000, 'service' => $this->program]])->assertSessionHasNoErrors();

    $billing = Billing::with('items')->firstOrFail();

    expect((float) $billing->amount)->toBe(1400000.0)->and($billing->items->first()->notes)->toBeNull()
        ->and(BillingAuditLog::where('billing_id', $billing->id)->firstOrFail()->after['price_overrides'])->toBe([]);
});

it('harga di luar master ditolak tanpa alasan dan tidak membuat invoice', function () {
    dhPost($this, [['description' => 'Prog', 'price' => 450000, 'service' => $this->program]])->assertSessionHasErrors('price_reason');
    dhPost($this, [['description' => 'Prog', 'price' => 450000, 'service' => $this->program, 'reason' => 'ok']])->assertSessionHasErrors('price_reason');

    expect(Billing::count())->toBe(0);
});

it('harga di luar master diterima dengan alasan, tercatat di item dan audit', function () {
    dhPost($this, [['description' => 'Prog', 'price' => 450000, 'service' => $this->program, 'reason' => 'Persetujuan owner']]);

    $billing = Billing::with('items')->firstOrFail();
    $audit = BillingAuditLog::where('billing_id', $billing->id)->where('action', 'created')->firstOrFail();

    expect((float) $billing->amount)->toBe(450000.0)
        ->and($billing->items->first()->notes)->toContain('Persetujuan owner')->toContain('450.000')
        ->and($audit->after['price_overrides'][0]['reason'])->toBe('Persetujuan owner')
        ->and((float) $audit->after['price_overrides'][0]['unit_price'])->toBe(450000.0);
});

it('perubahan harga produk inventory juga wajib beralasan', function () {
    $product = InventoryItem::create(['sku' => 'H-1', 'name' => 'Tape', 'stock' => 5, 'selling_price' => 40000, 'status' => 'active']);

    dhPost($this, [['type' => 'inventory', 'description' => 'Tape', 'price' => 10000, 'inventory_item_id' => $product->id]])->assertSessionHasErrors('price_reason');
    expect(Billing::count())->toBe(0)->and($product->fresh()->stock)->toBe(5);

    dhPost($this, [['type' => 'inventory', 'description' => 'Tape', 'price' => 10000, 'inventory_item_id' => $product->id, 'reason' => 'Produk cacat kemasan']]);

    expect(Billing::count())->toBe(1)->and($product->fresh()->stock)->toBe(4);
});

it('add-on tetap tidak pernah didiskon walaupun harganya diubah', function () {
    dhPost($this, [
        ['description' => 'Prog', 'price' => 500000, 'service' => $this->program],
        ['description' => '+ Add', 'price' => 80000, 'service' => $this->addOn, 'reason' => 'Harga khusus'],
    ], ['promo_id' => $this->promo->id]);

    $billing = Billing::with('items')->firstOrFail();

    expect((float) $billing->amount)->toBe(530000.0)   // 450.000 + 80.000
        ->and((float) $billing->items->firstWhere('item_category', 'add_on')->discount_amount)->toBe(0.0);
});

// ---------------- invoice historis ----------------

it('invoice historis (tanpa kategori) tidak berubah oleh aturan baru', function () {
    $legacy = Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-OLD', 'invoice_date' => now()->toDateString(), 'subtotal_amount' => 600000, 'discount_amount' => 60000, 'amount' => 540000, 'paid_amount' => 540000, 'payment_status' => 'paid']);
    $legacy->items()->create(['item_type' => 'service', 'description' => 'Lama', 'quantity' => 1, 'unit_price' => 600000, 'line_total' => 600000]);

    dhPost($this, [['description' => 'Prog', 'price' => 500000, 'service' => $this->program]], ['promo_id' => $this->promo->id]);

    $legacy->refresh();

    expect((float) $legacy->amount)->toBe(540000.0)->and((float) $legacy->discount_amount)->toBe(60000.0)
        ->and($legacy->items()->first()->item_category)->toBeNull();
});
