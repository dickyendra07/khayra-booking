<?php

use App\Support\AdminCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Nilai uji lokal saja; bukan kredensial production.
    config([
        'khayra_auth.admin.email' => 'admin@uji.test',
        'khayra_auth.admin.password' => 'kata-sandi-uji-lokal',
        'khayra_auth.admin.password_hash' => null,
    ]);
});

function adminLogin($test, string $email = 'admin@uji.test', string $password = 'kata-sandi-uji-lokal', string $ip = '10.0.0.1')
{
    return $test->withServerVariables(['REMOTE_ADDR' => $ip])
        ->post('/admin/login', ['email' => $email, 'password' => $password]);
}

it('mengizinkan login admin dengan kredensial dari config', function () {
    adminLogin($this)->assertRedirect('/admin/dashboard');

    $this->get('/admin/dashboard')->assertOk();
});

it('mendukung ADMIN_PASSWORD_HASH (bcrypt)', function () {
    config(['khayra_auth.admin.password' => null, 'khayra_auth.admin.password_hash' => Hash::make('sandi-hash-uji')]);

    adminLogin($this, password: 'sandi-hash-uji')->assertRedirect('/admin/dashboard');
});

it('menolak password yang salah dengan pesan generik', function () {
    adminLogin($this, password: 'salah')->assertSessionHasErrors(['email' => 'Email atau password admin salah.']);

    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
});

it('menolak login jika kredensial belum dikonfigurasi (tanpa fallback default)', function () {
    config(['khayra_auth.admin.email' => null, 'khayra_auth.admin.password' => null, 'khayra_auth.admin.password_hash' => null]);

    expect(AdminCredentials::isConfigured())->toBeFalse();

    // Kredensial default lama tidak boleh lagi berlaku.
    adminLogin($this, 'admin@khayraphysio.com', 'password')->assertSessionHasErrors('email');
    adminLogin($this, '', '')->assertSessionHasErrors();

    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
});

it('meregenerasi sesi setelah login berhasil', function () {
    $this->startSession();
    $before = session()->getId();

    adminLogin($this)->assertRedirect('/admin/dashboard');

    expect(session()->getId())->not->toBe($before);
});

it('memblokir setelah 5 kegagalan dari kombinasi email dan IP yang sama', function () {
    foreach (range(1, 5) as $i) {
        adminLogin($this, password: 'salah')->assertSessionHasErrors('email');
    }

    // Percobaan ke-6 diblokir, walaupun password benar.
    adminLogin($this)->assertSessionHasErrors('email');
    expect(sessionError('email'))->toContain('Terlalu banyak percobaan');
    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
});

it('tidak memblokir IP lain untuk kombinasi yang sama', function () {
    foreach (range(1, 5) as $i) {
        adminLogin($this, password: 'salah');
    }

    adminLogin($this, ip: '10.0.0.99')->assertRedirect('/admin/dashboard');
});

it('memblokir satu IP yang mencoba banyak identitas berbeda', function () {
    foreach (range(1, 20) as $i) {
        adminLogin($this, "orang{$i}@uji.test", 'salah');
    }

    adminLogin($this)->assertSessionHasErrors('email');
    expect(sessionError('email'))->toContain('Terlalu banyak percobaan');
});

it('membersihkan hitungan kegagalan setelah login berhasil', function () {
    foreach (range(1, 4) as $i) {
        adminLogin($this, password: 'salah');
    }

    adminLogin($this)->assertRedirect('/admin/dashboard');
    $this->post('/admin/logout');

    foreach (range(1, 4) as $i) {
        adminLogin($this, password: 'salah')->assertSessionHasErrors('email');
        expect(sessionError('email'))->not->toContain('Terlalu banyak');
    }
});

it('menginvalidasi sesi saat logout', function () {
    adminLogin($this);
    $this->get('/admin/dashboard')->assertOk();

    $this->post('/admin/logout')->assertRedirect('/admin/login');

    expect(session('admin_logged_in'))->toBeNull();
    $this->get('/admin/dashboard')->assertRedirect('/admin/login');
});

it('tetap memakai perlindungan CSRF pada form login di aplikasi (token tersedia di halaman)', function () {
    $this->get('/admin/login')->assertOk()->assertSee('name="_token"', false);
});
