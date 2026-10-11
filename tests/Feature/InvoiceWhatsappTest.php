<?php

use App\Models\Billing;
use App\Models\Patient;
use App\Support\WhatsappLink;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeBilling(array $patient = [], array $billing = []): Billing
{
    $patient = Patient::create(array_merge(['full_name' => 'Siti Aminah', 'whatsapp' => '0812-3456-7890'], $patient));

    $billing = Billing::create(array_merge([
        'patient_id' => $patient->id, 'invoice_number' => 'INV-TEST-1', 'invoice_date' => '2026-10-11',
        'subtotal_amount' => 600000, 'discount_amount' => 50000, 'amount' => 550000,
        'paid_amount' => 0, 'remaining_amount' => 550000, 'payment_status' => 'unpaid',
    ], $billing));

    $billing->items()->create(['item_type' => 'service', 'item_category' => 'program', 'description' => 'Musculoskeletal Program', 'quantity' => 1, 'unit_price' => 500000, 'line_total' => 500000]);
    $billing->items()->create(['item_type' => 'service', 'item_category' => 'add_on', 'description' => '+ USG', 'quantity' => 1, 'unit_price' => 100000, 'line_total' => 100000]);

    return $billing;
}

it('normalizes Indonesian numbers for wa.me', function (?string $raw, ?string $expected) {
    expect(WhatsappLink::normalize($raw))->toBe($expected);
})->with([
    ['0812-3456-7890', '6281234567890'],
    ['+62 812 3456 7890', '6281234567890'],
    ['6281234567890', '6281234567890'],
    ['81234567890', '6281234567890'],
    ['123', null],
    ['', null],
    [null, null],
]);

it('requires an admin session', function () {
    $billing = makeBilling();

    $this->get("/admin/billings/{$billing->id}/whatsapp")->assertRedirect('/admin/login');
});

it('opens a prefilled WhatsApp chat with the invoice summary', function () {
    $this->withSession(['admin_logged_in' => true]);
    $billing = makeBilling();

    $response = $this->get("/admin/billings/{$billing->id}/whatsapp");
    $location = $response->headers->get('Location');

    $response->assertRedirect();
    expect($location)->toStartWith('https://wa.me/6281234567890?text=');

    $text = urldecode(substr($location, strlen('https://wa.me/6281234567890?text=')));

    expect($text)->toContain('Siti Aminah')
        ->toContain('INV-TEST-1')
        ->toContain('Musculoskeletal Program x1 = Rp500.000')
        ->toContain('+ USG x1 = Rp100.000')
        ->toContain('Diskon: -Rp50.000')
        ->toContain('Total tagihan: Rp550.000')
        ->toContain('Status: Belum dibayar')
        ->toContain('Portal Pasien')
        ->not->toContain('/patient/invoices')   // no direct invoice URL
        ->not->toContain('PDF');                 // never claims an attachment
});

it('reports a partial payment with the remaining balance', function () {
    $this->withSession(['admin_logged_in' => true]);
    $billing = makeBilling([], ['payment_status' => 'partial', 'paid_amount' => 200000, 'remaining_amount' => 350000]);

    $location = $this->get("/admin/billings/{$billing->id}/whatsapp")->headers->get('Location');
    $text = urldecode($location);

    expect($text)->toContain('Status: Dibayar sebagian')
        ->toContain('Sudah dibayar: Rp200.000')
        ->toContain('Sisa tagihan: Rp350.000');
});

it('refuses to send a voided invoice', function () {
    $this->withSession(['admin_logged_in' => true]);
    $billing = makeBilling([], ['payment_status' => 'void', 'voided_at' => now()]);

    $this->get("/admin/billings/{$billing->id}/whatsapp")
        ->assertRedirect("/admin/billings/{$billing->id}")
        ->assertSessionHas('error');
});

it('explains when the patient has no usable WhatsApp number', function () {
    $this->withSession(['admin_logged_in' => true]);
    $billing = makeBilling(['whatsapp' => '-']);

    $this->get("/admin/billings/{$billing->id}/whatsapp")
        ->assertRedirect("/admin/billings/{$billing->id}")
        ->assertSessionHas('error');
});

it('shows the send action on the detail page and the list, but not for void invoices', function () {
    $this->withSession(['admin_logged_in' => true]);
    $billing = makeBilling();

    $this->get("/admin/billings/{$billing->id}")->assertOk()->assertSee('Kirim via WhatsApp');
    $this->get('/admin/billings')->assertOk()->assertSee("/admin/billings/{$billing->id}/whatsapp", false);

    $billing->update(['payment_status' => 'void', 'voided_at' => now()]);

    $this->get("/admin/billings/{$billing->id}")->assertOk()->assertDontSee('Kirim via WhatsApp');
    $this->get('/admin/billings')->assertOk()->assertDontSee("/admin/billings/{$billing->id}/whatsapp", false);
});
