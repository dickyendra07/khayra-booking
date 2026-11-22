<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function preflightRows(): \Illuminate\Support\Collection
{
    Artisan::call('khayra:preflight', ['--json' => true]);

    return collect(json_decode(Artisan::output(), true));
}

it('khayra:admin-hash --account=owner mencetak OWNER_PASSWORD_HASH', function () {
    $this->artisan('khayra:admin-hash', ['--account' => 'owner'])
        ->expectsQuestion('Password owner baru (tidak ditampilkan)', 'kredensial-owner-12345')
        ->expectsQuestion('Ulangi password', 'kredensial-owner-12345')
        ->expectsOutputToContain('OWNER_PASSWORD_HASH=')
        ->assertSuccessful();
});

it('khayra:admin-hash menolak akun selain admin/owner', function () {
    $this->artisan('khayra:admin-hash', ['--account' => 'superuser'])->assertFailed();
});

it('khayra:admin-verify --account=owner mencocokkan kredensial owner, bukan admin', function () {
    $this->artisan('khayra:admin-verify', ['--account' => 'owner'])
        ->expectsQuestion('Email owner', 'owner@uji.test')
        ->expectsQuestion('Password owner (tidak ditampilkan)', 'kata-sandi-owner-uji')
        ->assertSuccessful();

    // Kredensial admin tidak boleh lolos sebagai owner.
    $this->artisan('khayra:admin-verify', ['--account' => 'owner'])
        ->expectsQuestion('Email owner', 'admin@uji.test')
        ->expectsQuestion('Password owner (tidak ditampilkan)', 'kata-sandi-uji-lokal')
        ->assertFailed();
});

it('khayra:admin-verify tanpa opsi tetap memverifikasi admin (perilaku lama)', function () {
    $this->artisan('khayra:admin-verify')
        ->expectsQuestion('Email admin', 'admin@uji.test')
        ->expectsQuestion('Password admin (tidak ditampilkan)', 'kata-sandi-uji-lokal')
        ->assertSuccessful();
});

it('preflight memperingatkan bila akun owner belum diisi (4 menu owner tidak bisa dibuka siapa pun)', function () {
    config(['khayra_auth.owner.email' => null, 'khayra_auth.owner.password' => null, 'khayra_auth.owner.password_hash' => null]);

    $row = preflightRows()->firstWhere('check', 'Kredensial owner');

    expect($row['status'])->toBe('WARN')->and($row['detail'] ?? $row['message'] ?? json_encode($row))->toContain('login owner nonaktif');
});

it('preflight menandai hash owner yang tidak valid sebagai FAIL dan tidak membocorkan nilainya', function () {
    config(['khayra_auth.owner.password' => null, 'khayra_auth.owner.password_hash' => '$2y$rusak-tapi-rahasia']);

    $exit = Artisan::call('khayra:preflight', ['--json' => true]);
    $output = Artisan::output();
    $row = collect(json_decode($output, true))->firstWhere('check', 'Format password owner');

    expect($exit)->not->toBe(0)->and($row['status'])->toBe('FAIL')->and($output)->not->toContain('rusak-tapi-rahasia');
});

it('preflight menerima hash bcrypt owner yang valid', function () {
    config(['khayra_auth.owner.password' => null, 'khayra_auth.owner.password_hash' => Hash::driver('bcrypt')->make('kredensial-owner-12345')]);

    expect(preflightRows()->firstWhere('check', 'Format password owner')['status'])->toBe('PASS');
});
