<?php

use App\Models\Patient;
use App\Models\Therapist;
use App\Support\AdminCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

function hardenCreds(array $overrides = []): void
{
    config(array_merge([
        'khayra_auth.admin.email' => 'owner@uji.test',
        'khayra_auth.admin.password' => null,
        'khayra_auth.admin.password_hash' => Hash::make('sandi-uji-panjang-1'),
    ], $overrides));
}

function hardenLogin($test, string $email = 'owner@uji.test', string $password = 'sandi-uji-panjang-1')
{
    return $test->post('/admin/login', ['email' => $email, 'password' => $password]);
}

it('hash bcrypt yang terisi mengalahkan ADMIN_PASSWORD teks biasa (tidak ada bypass lewat sisa plaintext)', function () {
    hardenCreds(['khayra_auth.admin.password' => 'sisa-plaintext-lama']);

    hardenLogin($this, password: 'sisa-plaintext-lama')->assertSessionHasErrors('email');
    hardenLogin($this)->assertRedirect('/admin/dashboard');
});

it('hash yang rusak/bukan bcrypt gagal tertutup dan TIDAK jatuh ke password plaintext', function () {
    hardenCreds(['khayra_auth.admin.password_hash' => 'bukan-hash-bcrypt', 'khayra_auth.admin.password' => 'sisa-plaintext-lama']);

    expect(AdminCredentials::hashIsValid())->toBeFalse();

    hardenLogin($this, password: 'sisa-plaintext-lama')->assertSessionHasErrors('email');
    hardenLogin($this, password: 'bukan-hash-bcrypt')->assertSessionHasErrors('email');
    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
});

it('hash argon (bukan bcrypt) ditolak tanpa melempar error ke pengguna', function () {
    hardenCreds(['khayra_auth.admin.password_hash' => password_hash('sandi-uji-panjang-1', PASSWORD_ARGON2ID)]);

    hardenLogin($this)->assertSessionHasErrors('email');
});

it('email dicocokkan tanpa membedakan huruf besar/kecil dan spasi, tetapi password tetap peka huruf', function () {
    hardenCreds();

    hardenLogin($this, '  OWNER@Uji.Test ')->assertRedirect('/admin/dashboard');
    $this->post('/admin/logout');

    hardenLogin($this, password: 'SANDI-UJI-PANJANG-1')->assertSessionHasErrors('email');
});

it('email benar dengan password salah dan email salah dengan password benar memberi pesan yang sama', function () {
    hardenCreds();

    $a = hardenLogin($this, password: 'salah')->assertSessionHasErrors('email');
    $messageA = sessionError('email');
    $b = hardenLogin($this, 'lain@uji.test')->assertSessionHasErrors('email');

    expect(sessionError('email'))->toBe($messageA);
});

it('menyimpan email kanonik dari config sebagai aktor audit, bukan teks yang diketik', function () {
    hardenCreds();

    hardenLogin($this, '  OWNER@Uji.Test ');

    expect(session('admin_email'))->toBe('owner@uji.test');
});

it('rotasi kredensial membatalkan sesi admin yang sudah login', function () {
    hardenCreds();
    hardenLogin($this)->assertRedirect('/admin/dashboard');
    $this->get('/admin/dashboard')->assertOk();

    hardenCreds(['khayra_auth.admin.password_hash' => Hash::make('sandi-baru-yang-panjang')]);

    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    expect(session('admin_logged_in'))->toBeNull();

    hardenLogin($this)->assertSessionHasErrors('email');
    hardenLogin($this, password: 'sandi-baru-yang-panjang')->assertRedirect('/admin/dashboard');
    $this->get('/admin/dashboard')->assertOk();
});

it('penghapusan kredensial dari konfigurasi membatalkan sesi yang masih terbuka', function () {
    hardenCreds();
    hardenLogin($this);

    config(['khayra_auth.admin.email' => null, 'khayra_auth.admin.password_hash' => null, 'khayra_auth.admin.password' => null]);

    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
});

it('login admin yang valid menyimpan sidik jari kredensial yang berlaku di sesi', function () {
    hardenCreds();

    hardenLogin($this)->assertRedirect('/admin/dashboard');

    expect(session('admin_credential_fp'))->toBe(AdminCredentials::fingerprint())->not->toBeEmpty();
    $this->get('/admin/dashboard')->assertOk();
});

it('FAIL-CLOSED: sesi admin tanpa sidik jari (sesi versi lama) ditolak dan autentikasi admin dihapus dari sesi', function () {
    hardenCreds();

    $this->withSession(['admin_logged_in' => true, 'admin_email' => 'owner@uji.test'])
        ->get('/admin/dashboard')->assertRedirect('/admin/login');

    expect(session('admin_logged_in'))->toBeNull()->and(session('admin_email'))->toBeNull()->and(session('admin_credential_fp'))->toBeNull();

    // Sesi yang sama tidak bisa dipakai lagi pada permintaan berikutnya.
    $this->get('/admin/billings')->assertRedirect('/admin/login');
});

it('FAIL-CLOSED: sidik jari kosong, null, bukan string, atau salah ditolak', function (mixed $fingerprint) {
    hardenCreds();

    $this->withSession(['admin_logged_in' => true, 'admin_credential_fp' => $fingerprint])
        ->get('/admin/dashboard')->assertRedirect('/admin/login');

    expect(session('admin_logged_in'))->toBeNull();
})->with(['string kosong' => [''], 'null' => [null], 'palsu' => ['palsu'], 'array' => [['x']], 'angka' => [0], 'huruf besar' => [fn () => strtoupper(AdminCredentials::fingerprint())]]);

it('sidik jari yang cocok diterima', function () {
    hardenCreds();

    $this->withSession(['admin_logged_in' => true, 'admin_credential_fp' => AdminCredentials::fingerprint()])
        ->get('/admin/dashboard')->assertOk();
});

it('sidik jari lama ditolak setelah rotasi EMAIL admin', function () {
    hardenCreds();
    hardenLogin($this)->assertRedirect('/admin/dashboard');

    hardenCreds(['khayra_auth.admin.email' => 'owner-baru@uji.test']);

    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    expect(session('admin_logged_in'))->toBeNull();

    hardenLogin($this)->assertSessionHasErrors('email');
    hardenLogin($this, 'owner-baru@uji.test')->assertRedirect('/admin/dashboard');
    $this->get('/admin/dashboard')->assertOk();
});

it('sesi admin yang ditolak tidak mengganggu sesi terapis dan pasien yang sedang aktif', function () {
    hardenCreds();

    $this->withSession([
        'admin_logged_in' => true, 'admin_credential_fp' => 'kedaluwarsa',
        'therapist_logged_in' => true, 'therapist_id' => 77, 'patient_logged_in' => true, 'patient_id' => 88,
    ])->get('/admin/dashboard')->assertRedirect('/admin/login');

    expect(session('admin_logged_in'))->toBeNull()
        ->and(session('therapist_logged_in'))->toBeTrue()->and(session('therapist_id'))->toBe(77)
        ->and(session('patient_logged_in'))->toBeTrue()->and(session('patient_id'))->toBe(88);
});

it('sesi terapis dan pasien tidak membutuhkan sidik jari admin dan tetap berfungsi', function () {
    hardenCreds();
    $therapist = Therapist::create(['full_name' => 'Terapis Uji', 'email' => 'tp@uji.test', 'password' => Hash::make('x-uji-lokal'), 'status' => 'active']);
    $patient = Patient::create(['full_name' => 'Pasien Uji', 'whatsapp' => '081200000555']);

    $this->withSession(['therapist_logged_in' => true, 'therapist_id' => $therapist->id, 'therapist_name' => 'Terapis Uji'])->get('/therapist/dashboard')->assertOk();
    $this->flushSession()->withSession(['patient_logged_in' => true, 'patient_id' => $patient->id])->get('/patient/dashboard')->assertOk();
});

it('jalur tanpa login admin dan halaman login tetap dapat dibuka tanpa sidik jari', function () {
    hardenCreds();

    $this->get('/admin/login')->assertOk();
    $this->post('/admin/logout')->assertRedirect('/admin/login');
});

it('sidik jari bukan password/hash dan berubah bila email atau password berubah', function () {
    hardenCreds(['khayra_auth.admin.password_hash' => null, 'khayra_auth.admin.password' => 'plain-uji-123']);
    $plain = AdminCredentials::fingerprint();

    expect($plain)->toMatch('/^[a-f0-9]{64}$/')->not->toContain('plain-uji-123');

    config(['khayra_auth.admin.password' => 'plain-uji-124']);
    expect(AdminCredentials::fingerprint())->not->toBe($plain);

    config(['khayra_auth.admin.password' => 'plain-uji-123', 'khayra_auth.admin.email' => 'lain@uji.test']);
    expect(AdminCredentials::fingerprint())->not->toBe($plain);
});

it('menolak login tanpa menulis password ke log saat kredensial tidak terkonfigurasi', function () {
    config(['khayra_auth.admin.email' => null, 'khayra_auth.admin.password' => null, 'khayra_auth.admin.password_hash' => null]);

    Log::spy();

    hardenLogin($this, 'x@uji.test', 'rahasia-yang-diketik')->assertSessionHasErrors('email');

    Log::shouldHaveReceived('warning')->withArgs(fn ($message, $context = []) => ! str_contains($message.json_encode($context), 'rahasia-yang-diketik'))->once();
});

it('setelah logout, sesi lama tidak dapat dipakai lagi dan token CSRF diganti', function () {
    hardenCreds();
    hardenLogin($this);
    $tokenBefore = session()->token();

    $this->post('/admin/logout')->assertRedirect('/admin/login');

    expect(session()->token())->not->toBe($tokenBefore);
    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
});

it('GET ke URL logout tidak menutup sesi dan tidak menjadi jalur CSRF logout', function () {
    hardenCreds();
    hardenLogin($this);

    $this->get('/admin/logout')->assertStatus(405);
    $this->get('/admin/dashboard')->assertOk();
});

it('khayra:admin-hash mencetak hash bcrypt valid yang cocok dengan password tanpa mencetak password', function () {
    $this->artisan('khayra:admin-hash')
        ->expectsQuestion('Password admin baru (tidak ditampilkan)', 'sandi-operator-baru-1')
        ->expectsQuestion('Ulangi password', 'sandi-operator-baru-1')
        ->doesntExpectOutputToContain('sandi-operator-baru-1')
        ->expectsOutputToContain('ADMIN_PASSWORD_HASH=')
        ->assertSuccessful();
});

it('khayra:admin-hash menolak password pendek atau konfirmasi tidak sama', function () {
    $this->artisan('khayra:admin-hash')
        ->expectsQuestion('Password admin baru (tidak ditampilkan)', 'pendek')
        ->expectsQuestion('Ulangi password', 'pendek')
        ->assertFailed();

    $this->artisan('khayra:admin-hash')
        ->expectsQuestion('Password admin baru (tidak ditampilkan)', 'sandi-operator-baru-1')
        ->expectsQuestion('Ulangi password', 'sandi-operator-baru-2')
        ->assertFailed();
});

it('khayra:admin-verify membedakan kredensial benar/salah tanpa membuat sesi atau mencetak rahasia', function () {
    hardenCreds();

    $this->artisan('khayra:admin-verify')
        ->expectsQuestion('Email admin', 'owner@uji.test')
        ->expectsQuestion('Password admin (tidak ditampilkan)', 'sandi-uji-panjang-1')
        ->expectsOutputToContain('OK')
        ->assertSuccessful();

    $this->artisan('khayra:admin-verify')
        ->expectsQuestion('Email admin', 'owner@uji.test')
        ->expectsQuestion('Password admin (tidak ditampilkan)', 'salah')
        ->doesntExpectOutputToContain('salah-')
        ->assertFailed();

    expect(session('admin_logged_in'))->toBeNull();
});

it('khayra:admin-verify gagal bila hash di environment bukan bcrypt valid', function () {
    hardenCreds(['khayra_auth.admin.password_hash' => 'rusak']);

    $this->artisan('khayra:admin-verify')->expectsOutputToContain('bukan hash bcrypt')->assertFailed();
});

it('preflight menggagalkan hash rusak dan memperingatkan sisa ADMIN_PASSWORD tanpa mencetak nilainya', function () {
    hardenCreds(['khayra_auth.admin.password_hash' => 'rusak-tapi-rahasia', 'khayra_auth.admin.password' => 'plain-rahasia-9', 'cache.default' => 'file']);

    $exit = Artisan::call('khayra:preflight', ['--json' => true]);
    $output = Artisan::output();
    $rows = collect(json_decode($output, true));

    expect($exit)->not->toBe(0)
        ->and($rows->firstWhere('check', 'Format password admin')['status'])->toBe('FAIL')
        ->and($rows->firstWhere('check', 'ADMIN_PASSWORD sisa')['status'])->toBe('WARN')
        ->and($output)->not->toContain('rusak-tapi-rahasia')->not->toContain('plain-rahasia-9');
});

it('preflight lulus bersih (tanpa WARN format) dengan hash valid dan tanpa plaintext', function () {
    hardenCreds(['cache.default' => 'file']);

    Artisan::call('khayra:preflight', ['--json' => true]);
    $rows = collect(json_decode(Artisan::output(), true));

    expect($rows->firstWhere('check', 'Format password admin')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'ADMIN_PASSWORD sisa'))->toBeNull();
});

it('rotasi end-to-end: hash dari khayra:admin-hash dipakai login, lolos verify dan preflight, sesi lama gugur, tanpa lockout', function () {
    hardenCreds(['khayra_auth.admin.password_hash' => null, 'khayra_auth.admin.password' => 'kredensial-lama-plain', 'cache.default' => 'file']);
    $this->post('/admin/login', ['email' => 'owner@uji.test', 'password' => 'kredensial-lama-plain'])->assertRedirect('/admin/dashboard');

    // 1) Operator membuat hash (prompt tersembunyi) dan membaca baris yang dicetak.
    $this->artisan('khayra:admin-hash')
        ->expectsQuestion('Password admin baru (tidak ditampilkan)', 'kredensial-baru-12345')
        ->expectsQuestion('Ulangi password', 'kredensial-baru-12345')
        ->expectsOutputToContain('ADMIN_PASSWORD_HASH=')
        ->assertSuccessful();

    $hash = Hash::driver('bcrypt')->make('kredensial-baru-12345'); // setara keluaran command (bcrypt)

    // 2) Operator menaruh hash di .env, MENGHAPUS ADMIN_PASSWORD, config:cache.
    config(['khayra_auth.admin.password_hash' => $hash, 'khayra_auth.admin.password' => null]);

    // 3) Verifikasi sebelum menutup sesi lama.
    $this->artisan('khayra:admin-verify')
        ->expectsQuestion('Email admin', 'owner@uji.test')
        ->expectsQuestion('Password admin (tidak ditampilkan)', 'kredensial-baru-12345')
        ->assertSuccessful();

    Artisan::call('khayra:preflight', ['--json' => true]);
    $rows = collect(json_decode(Artisan::output(), true));
    expect($rows->firstWhere('check', 'Format password admin')['status'])->toBe('PASS');

    // 4) Sesi lama gugur; password lama ditolak; password baru masuk.
    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    hardenLogin($this, password: 'kredensial-lama-plain')->assertSessionHasErrors('email');
    hardenLogin($this, password: 'kredensial-baru-12345')->assertRedirect('/admin/dashboard');
    $this->get('/admin/dashboard')->assertOk();
});

it('login dan perintah rotasi tidak pernah menulis password atau hash ke log aplikasi', function () {
    hardenCreds();
    $logFile = storage_path('logs/laravel.log');
    @unlink($logFile);

    hardenLogin($this, password: 'salah-rahasia-777');
    $this->artisan('khayra:admin-verify')->expectsQuestion('Email admin', 'owner@uji.test')->expectsQuestion('Password admin (tidak ditampilkan)', 'salah-rahasia-777');

    $log = is_file($logFile) ? file_get_contents($logFile) : '';

    expect($log)->not->toContain('salah-rahasia-777')->not->toContain('$2y$');
});
