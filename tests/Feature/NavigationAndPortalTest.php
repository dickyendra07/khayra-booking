<?php

use App\Models\Billing;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('semua tautan navigasi sidebar admin tetap terbuka (tidak 404/500)', function () {
    $this->withSession(adminSession());

    $html = $this->get('/admin/dashboard')->assertOk()->getContent();
    preg_match_all('/href="(\/admin\/[^"#?]*)"/', $html, $matches);

    $links = array_values(array_unique($matches[1]));

    expect(count($links))->toBeGreaterThan(15);

    $broken = [];

    foreach ($links as $link) {
        $status = $this->get($link)->getStatusCode();

        if ($status >= 400) {
            $broken[] = "{$link} => {$status}";
        }
    }

    expect($broken)->toBe([]);
});

it('portal pasien: invoice hanya terbuka untuk pasien yang login dan pemilik invoice', function () {
    $owner = Patient::create(['full_name' => 'Pemilik', 'whatsapp' => '0811', 'birth_date' => '1990-01-01']);
    $other = Patient::create(['full_name' => 'Lain', 'whatsapp' => '0822', 'birth_date' => '1991-01-01']);
    $billing = Billing::create(['patient_id' => $owner->id, 'invoice_number' => 'INV-PORTAL', 'invoice_date' => now()->toDateString(), 'amount' => 100000, 'payment_status' => 'unpaid']);

    // Tanpa login: tidak ada invoice publik.
    $this->get("/patient/invoices/{$billing->id}")->assertRedirect('/patient');

    // Pasien lain: tidak boleh melihat (404, tidak membocorkan keberadaan invoice).
    $this->withSession(['patient_logged_in' => true, 'patient_id' => $other->id])
        ->get("/patient/invoices/{$billing->id}")->assertNotFound();

    // Pemilik: boleh.
    $this->withSession(['patient_logged_in' => true, 'patient_id' => $owner->id])
        ->get("/patient/invoices/{$billing->id}")->assertOk()->assertSee('INV-PORTAL');
});

it('halaman konfirmasi WhatsApp tidak memuat tautan ke invoice pasien', function () {
    $this->withSession(adminSession());
    $patient = Patient::create(['full_name' => 'Pasien WA', 'whatsapp' => '081234567890']);
    $billing = Billing::create(['patient_id' => $patient->id, 'invoice_number' => 'INV-WA', 'invoice_date' => now()->toDateString(), 'amount' => 100000, 'payment_status' => 'unpaid']);

    $html = $this->get("/admin/billings/{$billing->id}/whatsapp")->assertOk()->getContent();
    preg_match('/wa\.me\/\d+\?text=([^"]+)"/', $html, $match);

    expect(urldecode(html_entity_decode($match[1])))->not->toContain("/patient/invoices/{$billing->id}");
});
