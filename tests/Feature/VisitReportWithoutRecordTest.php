<?php

use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $patient = Patient::create(['full_name' => 'Pasien Baru', 'whatsapp' => '081200000009']);
    $this->therapist = Therapist::create(['full_name' => 'Fisio Uji', 'email' => 'fisio@uji.test', 'password' => Hash::make('x-uji-pass'), 'status' => 'active']);
    // Kunjungan baru dijadwalkan: BELUM ada rekam medis (tombol Report sudah tampil di dashboard terapis).
    $this->visit = Visit::create(['patient_id' => $patient->id, 'therapist_id' => $this->therapist->id, 'therapist' => 'Fisio Uji', 'visit_date' => now()->toDateString(), 'status' => 'scheduled']);
});

it('laporan terapis dan versi cetak tetap terbuka untuk kunjungan tanpa rekam medis', function () {
    $this->withSession(['therapist_logged_in' => true, 'therapist_id' => $this->therapist->id, 'therapist_name' => 'Fisio Uji']);

    $this->get("/therapist/visits/{$this->visit->id}/report")->assertOk();
    $this->get("/therapist/visits/{$this->visit->id}/report/print")->assertOk();
});

it('laporan admin dan versi cetak tetap terbuka untuk kunjungan tanpa rekam medis', function () {
    $this->withSession(adminSession());

    $this->get("/admin/visits/{$this->visit->id}/report")->assertOk();
    $this->get("/admin/visits/{$this->visit->id}/report/print")->assertOk();
});
