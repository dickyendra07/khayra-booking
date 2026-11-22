<?php

use App\Support\AdminCredentials;
use App\Support\PortalAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

// Kredensial uji lokal (lihat tests/TestCase.php): admin@uji.test / owner@uji.test.

const OWNER_ONLY_GETS = [
    '/owner/erm-dashboard',
    '/owner/dashboard',
    '/owner/finance',
    '/owner/finance/print',
    '/owner/reports',
    '/owner/reports/monthly-clinic',
    '/owner/reports/revenue',
    '/owner/reports/inventory',
    '/owner/reports/therapist-performance',
];

const OWNER_ONLY_POSTS = [
    '/owner/finance/close',
    '/owner/finance/reopen',
    '/owner/finance/transactions',
    '/owner/finance/transactions/1/delete',
];

function portalLogin($test, string $email, string $password)
{
    return $test->post('/admin/login', ['email' => $email, 'password' => $password]);
}

it('mengalihkan tamu dari semua halaman owner dan admin ke login', function () {
    foreach (array_merge(OWNER_ONLY_GETS, ['/owner', '/admin/bookings', '/admin/billings', '/admin/bookings/1/update']) as $url) {
        $this->get($url)->assertRedirect('/admin/login');
    }
});

it('memblokir admin dari 4 area owner (GET)', function () {
    foreach (OWNER_ONLY_GETS as $url) {
        $this->withSession(adminSession())->get($url)->assertForbidden();
    }
});

it('memblokir admin dari aksi tulis owner (POST), termasuk tutup/buka buku dan transaksi', function () {
    foreach (OWNER_ONLY_POSTS as $url) {
        $this->withSession(adminSession())->post($url)->assertForbidden();
    }
});

it('mengarahkan URL lama ke URL owner dengan query dipertahankan', function () {
    $this->withSession(ownerSession());

    $this->get('/admin/owner-finance?month=2026-10')->assertRedirect('/owner/finance?month=2026-10');
    $this->get('/admin/dashboard')->assertRedirect('/owner/erm-dashboard');
    $this->get('/admin/reports/revenue?x=1')->assertRedirect('/owner/reports/revenue?x=1');
    $this->get('/admin/owner-dashboard')->assertRedirect('/owner/dashboard');
});

it('tetap menolak admin yang mengikuti URL lama sampai ke halaman owner', function () {
    $this->withSession(adminSession())->followingRedirects()->get('/admin/owner-finance')->assertForbidden();
});

it('mengizinkan owner membuka semua halaman owner', function () {
    foreach (OWNER_ONLY_GETS as $url) {
        $this->withSession(ownerSession())->get($url)->assertSuccessful();
    }
});

it('mengizinkan owner masuk ke halaman admin (drill-down)', function () {
    $this->withSession(ownerSession());

    $this->get('/admin/bookings')->assertSuccessful();
    $this->get('/admin/billings')->assertSuccessful();
});

it('mengizinkan admin membuka halaman admin miliknya sendiri', function () {
    $this->withSession(adminSession());

    $this->get('/admin/bookings')->assertSuccessful();
    $this->get('/admin/billings')->assertSuccessful();
});

it('menyembunyikan menu owner dari sidebar admin dan menampilkannya untuk owner', function () {
    $this->withSession(adminSession())->get('/admin/bookings')
        ->assertDontSee('Owner Finance / Cashflow')
        ->assertDontSee('Reporting Center')
        ->assertDontSee('Dashboard ERM')
        ->assertSee('Logout Admin');

    $this->withSession(ownerSession())->get('/admin/bookings')
        ->assertSee('Owner Finance / Cashflow')
        ->assertSee('Reporting Center')
        ->assertSee('Logout Owner');
});

it('login admin mendarat di home admin dan login owner di home owner, dengan sidik jari akun masing-masing', function () {
    portalLogin($this, 'admin@uji.test', 'kata-sandi-uji-lokal')->assertRedirect(PortalAuth::ADMIN_HOME);
    expect(session('portal_role'))->toBe('admin')
        ->and(session('admin_credential_fp'))->toBe(AdminCredentials::fingerprint('admin'));

    $this->post('/admin/logout');

    portalLogin($this, 'owner@uji.test', 'kata-sandi-owner-uji')->assertRedirect(PortalAuth::OWNER_HOME);
    expect(session('portal_role'))->toBe('owner')
        ->and(session('admin_credential_fp'))->toBe(AdminCredentials::fingerprint('owner'));

    $this->get('/owner/finance')->assertOk();
});

it('login owner mendukung OWNER_PASSWORD_HASH (bcrypt) dan hash mengalahkan password teks', function () {
    config(['khayra_auth.owner.password' => 'sisa-plaintext', 'khayra_auth.owner.password_hash' => Hash::make('sandi-owner-hash')]);

    portalLogin($this, 'owner@uji.test', 'sisa-plaintext')->assertSessionHasErrors('email');
    portalLogin($this, 'owner@uji.test', 'sandi-owner-hash')->assertRedirect(PortalAuth::OWNER_HOME);
});

it('menolak kredensial salah dan menonaktifkan akun owner bila belum dikonfigurasi', function () {
    portalLogin($this, 'admin@uji.test', 'salah')->assertSessionHasErrors('email');

    config(['khayra_auth.owner.email' => null, 'khayra_auth.owner.password' => null, 'khayra_auth.owner.password_hash' => null]);

    expect(AdminCredentials::isConfigured('owner'))->toBeFalse()
        ->and(PortalAuth::attempt('', ''))->toBeNull()
        ->and(PortalAuth::attempt('owner@uji.test', 'kata-sandi-owner-uji'))->toBeNull();

    portalLogin($this, 'owner@uji.test', 'kata-sandi-owner-uji')->assertSessionHasErrors('email');
});

it('memberi hak paling rendah (admin) bila kredensial owner sama persis dengan admin', function () {
    config([
        'khayra_auth.owner.email' => 'admin@uji.test',
        'khayra_auth.owner.password' => 'kata-sandi-uji-lokal',
    ]);

    expect(PortalAuth::attempt('admin@uji.test', 'kata-sandi-uji-lokal'))->toBe('admin');
});

it('menganggap sesi tanpa portal_role sebagai admin (tidak bisa ke area owner)', function () {
    $this->withSession(adminSession())->get('/owner/finance')->assertForbidden();
});

it('menolak sesi yang role-nya dipalsukan: sidik jari admin tidak sah untuk role owner', function () {
    // Sesi dengan portal_role=owner tetapi sidik jari akun admin harus ditolak (fail-closed).
    $this->withSession(adminSession(['portal_role' => 'owner']))->get('/owner/finance')->assertRedirect('/admin/login');
});

it('merotasi kredensial owner membatalkan sesi owner yang sedang login', function () {
    $this->withSession(ownerSession())->get('/owner/finance')->assertOk();

    config(['khayra_auth.owner.password' => 'kata-sandi-owner-baru']);

    $this->get('/owner/finance')->assertRedirect('/admin/login');
});

it('logout menghapus sesi sehingga halaman owner tertutup lagi', function () {
    $this->withSession(ownerSession())->get('/owner/finance')->assertOk();

    $this->post('/admin/logout')->assertRedirect('/admin/login');
    $this->get('/owner/finance')->assertRedirect('/admin/login');
});
