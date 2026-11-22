<?php

use App\Models\Billing;
use App\Models\Patient;
use App\Services\InvoiceWhatsappMessage;
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

it('menampilkan nomor tujuan dan pratinjau pesan sebelum WhatsApp dibuka (tanpa redirect otomatis)', function () {
    $this->withSession(adminSession());
    $billing = makeBilling();

    $response = $this->get("/admin/billings/{$billing->id}/whatsapp");

    $response->assertOk()
        ->assertSee('Nomor tujuan')
        ->assertSee('+62 812-3456-7890')
        ->assertSee('Siti Aminah')                      // nama lengkap hanya untuk admin di layar konfirmasi
        ->assertSee('Pratinjau pesan')
        ->assertSee('Buka WhatsApp')
        ->assertSee('https://wa.me/6281234567890?text=', false);
    expect($response->headers->get('Location'))->toBeNull();
});

it('pesan WhatsApp minimal: tanpa nama layanan, rincian item, diskon, atau tautan invoice', function () {
    $this->withSession(adminSession());
    $billing = makeBilling();

    $html = $this->get("/admin/billings/{$billing->id}/whatsapp")->getContent();
    preg_match('/wa\.me\/\d+\?text=([^"]+)"/', $html, $match);
    $text = urldecode(html_entity_decode($match[1]));

    expect($text)->toContain('Halo Siti,')                // hanya nama depan
        ->toContain('INV-TEST-1')
        ->toContain('Total tagihan: Rp550.000')
        ->toContain('Status: Belum dibayar')
        ->toContain('Sisa tagihan: Rp550.000')
        ->toContain('Portal Pasien')
        ->not->toContain('Aminah')                        // nama belakang tidak dikirim
        ->not->toContain('Musculoskeletal')               // nama layanan tidak dikirim
        ->not->toContain('USG')
        ->not->toContain('Rincian')
        ->not->toContain('Diskon')
        ->not->toContain('Subtotal')
        ->not->toContain('/patient/invoices')             // tidak ada URL invoice langsung
        ->not->toContain('PDF');                          // tidak mengklaim lampiran
});

it('pesan tidak pernah memuat data klinis atau identitas sensitif pasien', function () {
    $this->withSession(adminSession());
    $billing = makeBilling(['full_name' => 'Siti Aminah', 'nik' => '3273010101010001', 'address' => 'Jl. Rahasia 1', 'medical_record_number' => 'KP-B-01-00099', 'birth_date' => '1990-05-17']);

    $text = (new InvoiceWhatsappMessage)->build($billing);

    expect($text)->not->toContain('3273010101010001')->not->toContain('Rahasia')->not->toContain('KP-B-01')->not->toContain('1990');
});

it('melaporkan sisa tagihan untuk pembayaran sebagian', function () {
    $this->withSession(adminSession());
    $billing = makeBilling([], ['payment_status' => 'partial', 'paid_amount' => 200000, 'remaining_amount' => 350000]);

    $text = (new InvoiceWhatsappMessage)->build($billing->fresh());

    expect($text)->toContain('Status: Dibayar sebagian')->toContain('Sisa tagihan: Rp350.000')->not->toContain('Sudah dibayar');
});

it('refuses to send a voided invoice', function () {
    $this->withSession(adminSession());
    $billing = makeBilling([], ['payment_status' => 'void', 'voided_at' => now()]);

    $this->get("/admin/billings/{$billing->id}/whatsapp")
        ->assertRedirect("/admin/billings/{$billing->id}")
        ->assertSessionHas('error');
});

it('explains when the patient has no usable WhatsApp number', function () {
    $this->withSession(adminSession());
    $billing = makeBilling(['whatsapp' => '-']);

    $this->get("/admin/billings/{$billing->id}/whatsapp")
        ->assertRedirect("/admin/billings/{$billing->id}")
        ->assertSessionHas('error');
});

it('shows the send action on the detail page and the list, but not for void invoices', function () {
    $this->withSession(adminSession());
    $billing = makeBilling();

    $this->get("/admin/billings/{$billing->id}")->assertOk()->assertSee('Kirim via WhatsApp');
    $this->get('/admin/billings')->assertOk()->assertSee("/admin/billings/{$billing->id}/whatsapp", false);

    $billing->update(['payment_status' => 'void', 'voided_at' => now()]);

    $this->get("/admin/billings/{$billing->id}")->assertOk()->assertDontSee('Kirim via WhatsApp');
    $this->get('/admin/billings')->assertOk()->assertDontSee("/admin/billings/{$billing->id}/whatsapp", false);
});
