<?php

use App\Models\Billing;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps existing billing, patient and finance pages working with legacy and new invoices', function () {
    $this->withSession(['admin_logged_in' => true]);

    $patient = Patient::create(['full_name' => 'Smoke', 'whatsapp' => '08123456789', 'religion' => 'islam']);

    // Legacy invoice: no categories, no discount fields filled in by the new code.
    $legacy = Billing::create(['patient_id' => $patient->id, 'invoice_number' => 'INV-OLD', 'invoice_date' => now()->toDateString(), 'amount' => 300000, 'paid_amount' => 300000, 'payment_status' => 'paid']);
    $legacy->items()->create(['item_type' => 'service', 'description' => 'Lama', 'quantity' => 1, 'unit_price' => 300000, 'line_total' => 300000]);

    $manual = Billing::create(['patient_id' => $patient->id, 'invoice_number' => 'INV-MANUAL', 'invoice_date' => now()->toDateString(), 'amount' => 100000, 'payment_status' => 'unpaid']);

    foreach ([
        '/admin/billings', "/admin/billings/{$legacy->id}", "/admin/billings/{$legacy->id}/print",
        "/admin/billings/{$legacy->id}/edit", "/admin/billings/{$manual->id}", "/admin/billings/{$manual->id}/print",
        '/admin/billings/create', '/admin/cashier', '/admin/patients', "/admin/patients/{$patient->id}",
        "/admin/patients/{$patient->id}/edit", '/admin/patients/create',
        '/admin/owner-finance', '/admin/owner-finance/print', '/admin/reports/revenue', '/admin/reports/monthly-clinic',
    ] as $url) {
        $this->get($url)->assertOk();
    }

    $this->withSession(['patient_logged_in' => true, 'patient_id' => $patient->id])
        ->get("/patient/invoices/{$legacy->id}")->assertOk();
});
