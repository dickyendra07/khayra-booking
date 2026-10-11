<?php

use App\Models\Billing;
use App\Models\Booking;
use App\Models\FinanceMonthlyClosing;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * Regresi: middleware pengaman terpusat tidak boleh merusak alur kerja
 * admin / therapist / pasien yang sudah login.
 */
beforeEach(function () {
    $this->patient = Patient::create(['full_name' => 'Pasien Regresi', 'whatsapp' => '081234567890', 'birth_date' => '1990-05-17', 'gender' => 'female']);
    $this->therapist = Therapist::create(['full_name' => 'Terapis Regresi', 'email' => 'reg@uji.test', 'password' => Hash::make('x-uji-lokal'), 'status' => 'active']);
});

it('admin login: membuka booking, kalender, pasien, visit, billing, kasir, finance', function () {
    $this->withSession(['admin_logged_in' => true]);

    $booking = Booking::create(['patient_id' => $this->patient->id, 'full_name' => 'Pasien Regresi', 'whatsapp' => '081234567890', 'service' => 'Uji', 'booking_date' => now()->toDateString(), 'booking_time' => '09:00', 'status' => 'confirmed']);
    $visit = Visit::create(['patient_id' => $this->patient->id, 'therapist_id' => $this->therapist->id, 'therapist' => 'Terapis Regresi', 'visit_date' => now()->toDateString(), 'status' => 'scheduled']);

    foreach ([
        '/admin/dashboard', '/admin/bookings', '/admin/bookings/calendar', '/admin/bookings/create',
        "/admin/bookings/{$booking->id}", "/admin/bookings/{$booking->id}/edit",
        '/admin/patients', "/admin/patients/{$this->patient->id}", '/admin/visits', '/admin/visits/create', "/admin/visits/{$visit->id}/edit",
        '/admin/billings', '/admin/cashier', '/admin/owner-finance', '/admin/owner-dashboard', '/admin/reports', '/admin/reports/revenue',
        '/admin/therapists', '/admin/promos', '/admin/services', '/admin/inventory',
    ] as $url) {
        $this->get($url)->assertOk();
    }
});

it('admin login: membuat booking dan visit lewat form seperti biasa', function () {
    $this->withSession(['admin_logged_in' => true]);

    $this->post('/admin/bookings/create', [
        'patient_id' => $this->patient->id, 'therapist_id' => $this->therapist->id, 'full_name' => 'Pasien Regresi',
        'whatsapp' => '081234567890', 'service' => 'Uji', 'booking_date' => now()->toDateString(),
        'booking_time' => '10:00', 'status' => 'confirmed',
    ])->assertRedirect();

    $this->post('/admin/visits', [
        'patient_id' => $this->patient->id, 'therapist_id' => $this->therapist->id,
        'visit_date' => now()->toDateString(), 'status' => 'scheduled',
    ])->assertRedirect('/admin/visits');

    expect(Booking::count())->toBe(1)->and(Visit::count())->toBe(1);
});

it('admin login: tutup buku bulanan tetap berjalan dan memakai angka invoice', function () {
    $this->withSession(['admin_logged_in' => true]);

    Billing::create(['patient_id' => $this->patient->id, 'invoice_number' => 'INV-REG', 'invoice_date' => now()->toDateString(), 'amount' => 250000, 'paid_amount' => 250000, 'payment_status' => 'paid']);
    $month = now()->format('Y-m');

    $this->post('/admin/owner-finance/close', ['month' => $month])->assertRedirect("/admin/owner-finance?month={$month}");

    expect((float) FinanceMonthlyClosing::firstOrFail()->billing_income)->toBe(250000.0);

    $this->get("/admin/owner-finance/print?month={$month}")->assertOk();
});

it('therapist login: dashboard dan logout tetap berfungsi', function () {
    $this->withSession(['therapist_logged_in' => true, 'therapist_id' => $this->therapist->id, 'therapist_name' => 'Terapis Regresi']);

    $this->get('/therapist/dashboard')->assertOk();
    $this->post('/therapist/logout')->assertRedirect('/therapist/login');
});

it('pasien login: dashboard dan logout tetap berfungsi', function () {
    $this->withSession(['patient_logged_in' => true, 'patient_id' => $this->patient->id, 'patient_name' => 'Pasien Regresi']);

    $this->get('/patient/dashboard')->assertOk();
    $this->post('/patient/logout')->assertRedirect('/patient');
});

it('halaman booking publik tetap bisa dibuka tanpa login', function () {
    $this->get('/booking')->assertOk();
});
