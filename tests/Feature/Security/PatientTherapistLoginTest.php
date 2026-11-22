<?php

use App\Models\Patient;
use App\Models\Therapist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function patientLogin($test, string $whatsapp, string $birthDate, string $ip = '10.1.0.1')
{
    return $test->withServerVariables(['REMOTE_ADDR' => $ip])
        ->post('/patient/login', ['whatsapp' => $whatsapp, 'birth_date' => $birthDate]);
}

function therapistLogin($test, string $email, string $password, string $ip = '10.2.0.1')
{
    return $test->withServerVariables(['REMOTE_ADDR' => $ip])
        ->post('/therapist/login', ['email' => $email, 'password' => $password]);
}

beforeEach(function () {
    $this->patient = Patient::create(['full_name' => 'Pasien Uji', 'whatsapp' => '081234567890', 'birth_date' => '1990-05-17']);
    $this->therapist = Therapist::create([
        'full_name' => 'Terapis Uji', 'email' => 'terapis@uji.test',
        'password' => Hash::make('sandi-terapis-uji'), 'status' => 'active',
    ]);
});

// ---------------- Pasien ----------------

it('mengizinkan login pasien yang benar (mekanisme WhatsApp + tanggal lahir tidak berubah)', function () {
    patientLogin($this, '0812-3456-7890', '1990-05-17')->assertRedirect('/patient/dashboard');

    expect(session('patient_id'))->toBe($this->patient->id);
});

it('memberi pesan error yang sama untuk nomor tidak terdaftar dan tanggal lahir salah', function () {
    patientLogin($this, '089999999999', '1990-05-17');
    $unknownNumber = sessionError('whatsapp');

    patientLogin($this, '081234567890', '1991-01-01');
    $wrongBirthDate = sessionError('whatsapp');

    expect($unknownNumber)->not->toBe('')->and($wrongBirthDate)->toBe($unknownNumber);
});

it('meregenerasi sesi saat login pasien', function () {
    $this->startSession();
    $before = session()->getId();

    patientLogin($this, '081234567890', '1990-05-17')->assertRedirect('/patient/dashboard');

    expect(session()->getId())->not->toBe($before);
});

it('memblokir setelah 5 kegagalan untuk nomor yang sama dari IP yang sama, termasuk penulisan nomor berbeda', function () {
    $variants = ['081234567890', '+62 812-3456-7890', '6281234567890', '0812 3456 7890', '081234567890'];

    foreach ($variants as $variant) {
        patientLogin($this, $variant, '1980-01-01')->assertSessionHasErrors('whatsapp');
    }

    // Tanggal lahir yang benar pun ditolak selama diblokir.
    patientLogin($this, '081234567890', '1990-05-17')->assertSessionHasErrors('whatsapp');
    expect(sessionError('whatsapp'))->toContain('Terlalu banyak percobaan');
    $this->get('/patient/dashboard')->assertRedirect('/patient');
});

it('memblokir penebakan terdistribusi lintas IP terhadap satu nomor pasien', function () {
    foreach (range(1, 20) as $i) {
        patientLogin($this, '081234567890', '1980-01-01', "10.9.0.{$i}");
    }

    patientLogin($this, '081234567890', '1990-05-17', '10.9.1.1')->assertSessionHasErrors('whatsapp');
    expect(sessionError('whatsapp'))->toContain('Terlalu banyak percobaan');
});

it('memblokir satu IP yang menebak banyak nomor berbeda', function () {
    foreach (range(1, 20) as $i) {
        patientLogin($this, '08111111'.str_pad((string) $i, 4, '0', STR_PAD_LEFT), '1980-01-01');
    }

    patientLogin($this, '081234567890', '1990-05-17')->assertSessionHasErrors('whatsapp');
    expect(sessionError('whatsapp'))->toContain('Terlalu banyak percobaan');
});

it('menginvalidasi sesi pasien saat logout', function () {
    patientLogin($this, '081234567890', '1990-05-17');
    $this->get('/patient/dashboard')->assertOk();

    $this->post('/patient/logout')->assertRedirect('/patient');

    expect(session('patient_logged_in'))->toBeNull();
    $this->get('/patient/dashboard')->assertRedirect('/patient');
});

// ---------------- Therapist ----------------

it('mengizinkan login therapist yang benar', function () {
    therapistLogin($this, 'terapis@uji.test', 'sandi-terapis-uji')->assertRedirect('/therapist/dashboard');

    $this->get('/therapist/dashboard')->assertOk();
});

it('memberi pesan yang sama untuk email tidak terdaftar, password salah, dan akun nonaktif', function () {
    therapistLogin($this, 'tidakada@uji.test', 'apa-saja');
    $unknown = session('error');

    therapistLogin($this, 'terapis@uji.test', 'salah');
    $wrongPassword = session('error');

    $this->therapist->update(['status' => 'inactive']);
    therapistLogin($this, 'terapis@uji.test', 'sandi-terapis-uji', '10.2.0.2');
    $inactive = session('error');

    expect($unknown)->toBe('Email atau password therapist salah.')
        ->and($wrongPassword)->toBe($unknown)
        ->and($inactive)->toBe($unknown);
});

it('meregenerasi sesi saat login therapist', function () {
    $this->startSession();
    $before = session()->getId();

    therapistLogin($this, 'terapis@uji.test', 'sandi-terapis-uji')->assertRedirect('/therapist/dashboard');

    expect(session()->getId())->not->toBe($before);
});

it('memblokir therapist setelah 5 kegagalan dan membersihkan hitungan setelah berhasil', function () {
    foreach (range(1, 5) as $i) {
        therapistLogin($this, 'terapis@uji.test', 'salah');
    }

    therapistLogin($this, 'terapis@uji.test', 'sandi-terapis-uji');
    expect(session('error'))->toContain('Terlalu banyak percobaan');
    $this->get('/therapist/dashboard')->assertRedirect('/therapist/login');

    // IP lain tidak terdampak oleh lapis identitas+IP, dan sukses membersihkan hitungan.
    therapistLogin($this, 'terapis@uji.test', 'sandi-terapis-uji', '10.2.5.5')->assertRedirect('/therapist/dashboard');
});

it('menginvalidasi sesi therapist saat logout', function () {
    therapistLogin($this, 'terapis@uji.test', 'sandi-terapis-uji');
    $this->post('/therapist/logout')->assertRedirect('/therapist/login');

    expect(session('therapist_logged_in'))->toBeNull();
    $this->get('/therapist/dashboard')->assertRedirect('/therapist/login');
});
