<?php

use App\Models\Billing;
use App\Models\ClinicService;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withSession(['admin_logged_in' => true]);

    $this->patient = Patient::create(['full_name' => 'Pasien Uji', 'whatsapp' => '081234567890']);

    $this->program = ClinicService::create(['name' => 'Uji Program', 'price_per_visit' => 500000, 'category' => 'Program', 'status' => 'active']);
    $this->specialist = ClinicService::create(['name' => 'Uji Specialist', 'price_per_visit' => 425000, 'category' => 'Specialist', 'status' => 'active']);
    $this->addOn = ClinicService::create(['name' => '+ Uji Add-on', 'price_per_visit' => 100000, 'category' => 'Add-on', 'status' => 'active']);

    $this->promo = Promo::create([
        'code' => 'DISC10', 'name' => '10 persen', 'discount_type' => 'percent',
        'discount_value' => 10, 'status' => 'active',
    ]);
});

function checkoutPayload(array $lines, array $overrides = []): array
{
    $payload = array_merge([
        'patient_id' => Patient::firstOrFail()->id,
        'invoice_date' => now()->toDateString(),
        'payment_method' => 'cash',
        'paid_amount' => 0,
        'item_type' => [], 'description' => [], 'quantity' => [], 'unit_price' => [],
        'inventory_item_id' => [], 'clinic_service_id' => [], 'item_category' => [],
    ], $overrides);

    foreach ($lines as $line) {
        $payload['item_type'][] = $line['type'] ?? 'service';
        $payload['description'][] = $line['description'];
        $payload['quantity'][] = $line['quantity'] ?? 1;
        $payload['unit_price'][] = $line['price'];
        $payload['inventory_item_id'][] = $line['inventory_item_id'] ?? '';
        $payload['clinic_service_id'][] = $line['service']->id ?? '';
        $payload['item_category'][] = $line['category'] ?? '';
    }

    return $payload;
}

it('discounts only the program and stores the correct invoice totals', function () {
    $response = $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Uji Program', 'price' => 500000, 'service' => $this->program],
        ['description' => '+ Uji Add-on', 'price' => 100000, 'service' => $this->addOn],
    ], ['promo_id' => $this->promo->id, 'paid_amount' => 550000]));

    $billing = Billing::with('items')->firstOrFail();
    $response->assertRedirect('/admin/billings/'.$billing->id);

    expect((float) $billing->subtotal_amount)->toBe(600000.0)
        ->and((float) $billing->discount_amount)->toBe(50000.0)
        ->and((float) $billing->amount)->toBe(550000.0)
        ->and($billing->promo_code)->toBe('DISC10')
        ->and($billing->payment_status)->toBe('paid');

    $program = $billing->items->firstWhere('item_category', 'program');
    $addOn = $billing->items->firstWhere('item_category', 'add_on');

    expect($program->is_discount_eligible)->toBeTrue()
        ->and((float) $program->discount_amount)->toBe(50000.0)
        ->and((float) $addOn->line_total)->toBe(100000.0)
        ->and($addOn->is_discount_eligible)->toBeFalse()
        ->and((float) $addOn->discount_amount)->toBe(0.0);
});

it('treats specialist services as programs', function () {
    $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Uji Specialist', 'price' => 425000, 'service' => $this->specialist],
    ], ['promo_id' => $this->promo->id]));

    $billing = Billing::firstOrFail();

    expect((float) $billing->discount_amount)->toBe(42500.0)
        ->and((float) $billing->amount)->toBe(382500.0);
});

it('ignores a client supplied category for a master add-on service', function () {
    $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Uji Program', 'price' => 500000, 'service' => $this->program],
        ['description' => '+ Uji Add-on', 'price' => 100000, 'service' => $this->addOn, 'category' => 'program'],
    ], ['promo_id' => $this->promo->id]));

    $billing = Billing::firstOrFail();

    expect((float) $billing->amount)->toBe(550000.0);
});

it('does not discount a manual add-on line or a booking fee', function () {
    $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Fisioterapi Umum', 'price' => 200000, 'category' => 'program'],
        ['description' => 'Add-on manual', 'price' => 100000, 'category' => 'add_on'],
        ['description' => 'Booking Fee Non-Paket', 'price' => 50000, 'category' => 'booking_fee'],
    ], ['promo_id' => $this->promo->id]));

    $billing = Billing::firstOrFail();

    expect((float) $billing->discount_amount)->toBe(20000.0)
        ->and((float) $billing->amount)->toBe(330000.0);
});

it('does not discount inventory products', function () {
    $product = InventoryItem::create(['sku' => 'P-1', 'name' => 'Tape', 'stock' => 5, 'selling_price' => 40000, 'status' => 'active']);

    $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Uji Program', 'price' => 500000, 'service' => $this->program],
        ['type' => 'inventory', 'description' => 'Tape', 'price' => 40000, 'quantity' => 2, 'inventory_item_id' => $product->id],
    ], ['promo_id' => $this->promo->id]));

    $billing = Billing::with('items')->firstOrFail();

    expect((float) $billing->amount)->toBe(530000.0) // 450,000 + 80,000
        ->and($billing->items->firstWhere('item_type', 'inventory')->item_category)->toBe('product')
        ->and($product->fresh()->stock)->toBe(3);
});

it('refuses a promo when the invoice has no program item and creates nothing', function () {
    $response = $this->from('/admin/cashier')->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => '+ Uji Add-on', 'price' => 100000, 'service' => $this->addOn],
    ], ['promo_id' => $this->promo->id]));

    $response->assertRedirect('/admin/cashier')->assertSessionHasErrors('promo_id');
    expect(Billing::count())->toBe(0);
});

it('keeps room charge and admin fee lines at full price next to a discounted program', function () {
    $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Uji Program', 'price' => 300000, 'service' => $this->program],
        ['description' => 'Biaya ruangan', 'price' => 100000, 'category' => 'room_charge'],
        ['description' => 'Admin awal kedatangan', 'price' => 35000, 'category' => 'admin_fee'],
    ], ['promo_id' => $this->promo->id]));

    $billing = Billing::with('items')->firstOrFail();

    expect((float) $billing->amount)->toBe(405000.0) // 270,000 + 100,000 + 35,000
        ->and($billing->items->pluck('item_category')->all())->toBe(['program', 'room_charge', 'admin_fee']);
});

it('records the same figures the finance ledger reads', function () {
    $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Uji Program', 'price' => 500000, 'service' => $this->program],
        ['description' => '+ Uji Add-on', 'price' => 100000, 'service' => $this->addOn],
    ], ['promo_id' => $this->promo->id, 'paid_amount' => 550000]));

    $this->get('/admin/owner-finance?month='.now()->format('Y-m'))
        ->assertOk()
        ->assertViewHas('summary', fn ($summary) => (float) $summary['billing_income'] === 550000.0
            && (float) $summary['total_income'] === 550000.0
            && $summary['transaction_count'] === 1);
});

it('leaves a historical invoice without categories untouched', function () {
    $legacy = Billing::create([
        'patient_id' => $this->patient->id, 'invoice_number' => 'INV-LEGACY',
        'invoice_date' => now()->toDateString(), 'subtotal_amount' => 600000,
        'discount_amount' => 60000, 'amount' => 540000, 'paid_amount' => 540000,
        'payment_status' => 'paid',
    ]);
    $legacy->items()->create(['item_type' => 'service', 'description' => 'Legacy', 'quantity' => 1, 'unit_price' => 600000, 'line_total' => 600000]);

    $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Uji Program', 'price' => 100000, 'service' => $this->program],
    ]));

    $legacy->refresh();
    $item = $legacy->items()->first();

    expect((float) $legacy->amount)->toBe(540000.0)
        ->and((float) $legacy->discount_amount)->toBe(60000.0)
        ->and($item->item_category)->toBeNull()
        ->and($item->category_label)->toBeNull();
});

it('renders the cashier form with category controls and the discount rule', function () {
    $this->get('/admin/cashier')
        ->assertOk()
        ->assertSee('name="item_category[]"', false)
        ->assertSee('name="clinic_service_id[]"', false)
        ->assertSee('data-category="add_on"', false)
        ->assertSee('Diskon hanya berlaku untuk item Program / Treatment.');
});

it('labels discounted and non-discounted lines on the admin detail, print and patient invoice', function () {
    $this->post('/admin/cashier/checkout', checkoutPayload([
        ['description' => 'Uji Program', 'price' => 500000, 'service' => $this->program],
        ['description' => '+ Uji Add-on', 'price' => 100000, 'service' => $this->addOn],
    ], ['promo_id' => $this->promo->id]));

    $billing = Billing::firstOrFail();

    foreach (["/admin/billings/{$billing->id}", "/admin/billings/{$billing->id}/print"] as $url) {
        $this->get($url)->assertOk()
            ->assertSee('Program / Treatment')
            ->assertSee('Diskon Rp 50.000')
            ->assertSee('Add-on')
            ->assertSee('Tidak termasuk diskon');
    }

    $this->withSession(['patient_logged_in' => true, 'patient_id' => $billing->patient_id])
        ->get("/patient/invoices/{$billing->id}")->assertOk()->assertSee('Tidak termasuk diskon');
});
