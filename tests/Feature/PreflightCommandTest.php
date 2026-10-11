<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

it('preflight gagal bila kredensial admin belum dikonfigurasi dan tidak menampilkan secret', function () {
    config(['khayra_auth.admin.email' => null, 'khayra_auth.admin.password' => null, 'khayra_auth.admin.password_hash' => null]);

    $this->artisan('khayra:preflight')
        ->expectsOutputToContain('Kredensial admin')
        ->assertFailed();
});

it('preflight lulus dengan konfigurasi benar dan tidak pernah mencetak nilai kredensial', function () {
    config([
        'khayra_auth.admin.email' => 'admin@uji.test', 'khayra_auth.admin.password' => 'RAHASIA-UJI-123', 'khayra_auth.admin.password_hash' => null,
        'cache.default' => 'file',
    ]);

    $exit = Artisan::call('khayra:preflight', ['--json' => true]);
    $output = Artisan::output();
    $rows = collect(json_decode($output, true));

    expect($exit)->toBe(0)
        ->and($output)->not->toContain('RAHASIA-UJI-123')->not->toContain('admin@uji.test')
        ->and($rows->firstWhere('check', 'Kredensial admin')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'Cache untuk throttling login')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'Pemanggilan fungsi env di routes/app')['status'])->toBe('PASS');
});

it('preflight menandai cache array sebagai tidak efektif untuk throttling', function () {
    config([
        'khayra_auth.admin.email' => 'admin@uji.test', 'khayra_auth.admin.password' => 'x-uji', 'cache.default' => 'array',
    ]);

    $this->artisan('khayra:preflight')->expectsOutputToContain('TIDAK efektif')->assertFailed();
});

it('preflight memeriksa zona waktu penyimpanan UTC + zona operasional klinik dan memberi peringatan format password teks biasa', function () {
    config(['khayra_auth.admin.email' => 'admin@uji.test', 'khayra_auth.admin.password' => 'x-uji', 'cache.default' => 'file']);

    Artisan::call('khayra:preflight', ['--json' => true]);
    $rows = collect(json_decode(Artisan::output(), true));

    expect($rows->firstWhere('check', 'Zona waktu penyimpanan')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'Zona waktu operasional klinik')['status'])->toBe('PASS')
        ->and($rows->firstWhere('check', 'Format password admin')['status'])->toBe('WARN');
});
