<?php

use App\Models\Promo;
use App\Services\BillingCalculator;

function promo(array $attributes = []): Promo
{
    return new Promo(array_merge([
        'code' => 'TEST',
        'name' => 'Test promo',
        'discount_type' => 'percent',
        'discount_value' => 10,
        'minimum_purchase' => 0,
        'maximum_discount' => 0,
        'status' => 'active',
    ], $attributes));
}

function line(string $category, int $price, int $qty = 1): array
{
    return ['category' => $category, 'quantity' => $qty, 'unit_price' => $price];
}

it('discounts the program only and leaves the add-on at full price', function () {
    $result = (new BillingCalculator)->calculate([
        line('program', 500000),
        line('add_on', 100000),
    ], promo());

    expect($result['subtotal'])->toBe(600000.0)
        ->and($result['discount_base'])->toBe(500000.0)
        ->and($result['discount_amount'])->toBe(50000.0)
        ->and($result['grand_total'])->toBe(550000.0) // not 540,000
        ->and($result['lines'][0]['discount_amount'])->toBe(50000.0)
        ->and($result['lines'][1]['discount_amount'])->toBe(0.0)
        ->and($result['lines'][1]['is_discount_eligible'])->toBeFalse();
});

it('never discounts add-ons, products, fees or room charges', function (string $category) {
    $result = (new BillingCalculator)->calculate([
        line('program', 300000),
        line($category, 100000),
    ], promo());

    expect($result['discount_amount'])->toBe(30000.0)
        ->and($result['grand_total'])->toBe(370000.0);
})->with(['add_on', 'consultation', 'product', 'booking_fee', 'admin_fee', 'room_charge', 'other']);

it('keeps room charges and admin fee separate from the treatment price', function () {
    $result = (new BillingCalculator)->calculate([
        line('program', 300000),
        line('room_charge', 100000),
        line('admin_fee', 35000),
    ]);

    expect($result['subtotal'])->toBe(435000.0)
        ->and($result['discount_amount'])->toBe(0.0)
        ->and($result['grand_total'])->toBe(435000.0)
        ->and($result['lines'][0]['line_total'])->toBe(300000.0);
});

it('rejects a promo when there is no program item to discount', function () {
    $result = (new BillingCalculator)->calculate([
        line('add_on', 100000),
        line('product', 50000),
    ], promo());

    expect($result['promo_applied'])->toBeFalse()
        ->and($result['promo_error'])->not->toBeNull()
        ->and($result['discount_amount'])->toBe(0.0)
        ->and($result['grand_total'])->toBe(150000.0);
});

it('applies a nominal promo capped at the program base, not the whole invoice', function () {
    $result = (new BillingCalculator)->calculate([
        line('program', 100000),
        line('add_on', 400000),
    ], promo(['discount_type' => 'nominal', 'discount_value' => 250000]));

    expect($result['discount_amount'])->toBe(100000.0)
        ->and($result['grand_total'])->toBe(400000.0);
});

it('honours maximum discount', function () {
    $result = (new BillingCalculator)->calculate([
        line('program', 1000000),
    ], promo(['discount_value' => 50, 'maximum_discount' => 100000]));

    expect($result['discount_amount'])->toBe(100000.0)
        ->and($result['grand_total'])->toBe(900000.0);
});

it('checks minimum purchase against the program base only', function () {
    $minimum = promo(['minimum_purchase' => 400000]);

    $notMet = (new BillingCalculator)->calculate([
        line('program', 300000),
        line('add_on', 300000),
    ], $minimum);

    $met = (new BillingCalculator)->calculate([
        line('program', 400000),
        line('add_on', 10000),
    ], $minimum);

    expect($notMet['promo_applied'])->toBeFalse()
        ->and($notMet['grand_total'])->toBe(600000.0)
        ->and($met['promo_applied'])->toBeTrue()
        ->and($met['discount_amount'])->toBe(40000.0);
});

it('does not apply inactive or expired promos', function () {
    $inactive = (new BillingCalculator)->calculate([line('program', 100000)], promo(['status' => 'inactive']));
    $expired = (new BillingCalculator)->calculate([line('program', 100000)], promo(['end_date' => now()->subDay()]));

    expect($inactive['promo_applied'])->toBeFalse()
        ->and($expired['promo_applied'])->toBeFalse();
});

it('multiplies quantity and allocates the discount exactly across program lines', function () {
    $result = (new BillingCalculator)->calculate([
        line('program', 33333, 3),
        line('program', 33334),
        line('add_on', 75000, 2),
    ], promo(['discount_value' => 15]));

    $allocated = array_sum(array_column($result['lines'], 'discount_amount'));

    expect($result['subtotal'])->toBe(283333.0)
        ->and($result['discount_base'])->toBe(133333.0)
        ->and($allocated)->toEqualWithDelta($result['discount_amount'], 0.001)
        ->and($result['lines'][2]['discount_amount'])->toBe(0.0)
        ->and($result['grand_total'])->toEqualWithDelta(283333 - $result['discount_amount'], 0.001);
});

it('returns the undiscounted total when no promo is used', function () {
    $result = (new BillingCalculator)->calculate([line('program', 500000), line('add_on', 100000)]);

    expect($result['discount_amount'])->toBe(0.0)
        ->and($result['grand_total'])->toBe(600000.0)
        ->and($result['promo_error'])->toBeNull();
});

it('maps clinic service master categories to billing categories', function () {
    expect(BillingCalculator::categoryForService('Program'))->toBe('program')
        ->and(BillingCalculator::categoryForService('Specialist'))->toBe('program')
        ->and(BillingCalculator::categoryForService('Add-on'))->toBe('add_on')
        ->and(BillingCalculator::categoryForService('Consultation'))->toBe('consultation')
        ->and(BillingCalculator::categoryForService(null))->toBe('other')
        ->and(BillingCalculator::isDiscountable('add_on'))->toBeFalse()
        ->and(BillingCalculator::isDiscountable('program'))->toBeTrue();
});
