<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

/**
 * Daftar halaman area login yang memang publik (halaman login/logout).
 */
const PUBLIC_AREA_PATHS = [
    'admin/login', 'admin/logout',
    'therapist', 'therapist/login', 'therapist/logout',
    'patient', 'patient/login', 'patient/logout',
];

function protectedAreaRoutes(): array
{
    $routes = [];

    foreach (Route::getRoutes() as $route) {
        $uri = $route->uri();
        $area = explode('/', $uri)[0];

        if (! in_array($area, ['admin', 'therapist', 'patient'], true) || in_array($uri, PUBLIC_AREA_PATHS, true)) {
            continue;
        }

        foreach ($route->methods() as $method) {
            if (in_array($method, ['GET', 'POST'], true)) {
                $routes[] = [$method, '/'.preg_replace('/\{[^}]+\}/', '1', $uri), $area];
            }
        }
    }

    return $routes;
}

it('menemukan rute area login untuk diuji', function () {
    expect(count(protectedAreaRoutes()))->toBeGreaterThan(100);
});

it('mengarahkan SEMUA rute admin/therapist/patient tanpa login ke halaman login', function () {
    $loginPaths = ['admin' => '/admin/login', 'therapist' => '/therapist/login', 'patient' => '/patient'];
    $failures = [];

    foreach (protectedAreaRoutes() as [$method, $uri, $area]) {
        $response = $this->call($method, $uri);

        $location = parse_url((string) $response->headers->get('Location'), PHP_URL_PATH);

        if ($response->getStatusCode() !== 302 || $location !== $loginPaths[$area]) {
            $failures[] = "{$method} {$uri} => {$response->getStatusCode()}";
        }
    }

    expect($failures)->toBe([]);
});

it('menutup laporan therapist-performance dari pengguna tanpa login dan tidak membocorkan isinya', function () {
    $response = $this->get('/admin/reports/therapist-performance');

    $response->assertRedirect('/admin/login');
    expect($response->getContent())->not->toContain('Rp ')->not->toContain('patients');
});

it('tetap mengizinkan admin yang sudah login membuka laporan therapist-performance', function () {
    $this->withSession(adminSession())
        ->get('/admin/reports/therapist-performance')
        ->assertOk();
});

it('tidak membiarkan sesi pasien atau therapist membuka halaman admin', function () {
    $this->withSession(['patient_logged_in' => true, 'patient_id' => 1])
        ->get('/admin/dashboard')->assertRedirect('/admin/login');

    $this->withSession(['therapist_logged_in' => true, 'therapist_id' => 1])
        ->get('/admin/billings')->assertRedirect('/admin/login');
});

it('tidak membiarkan sesi admin menggantikan sesi pasien di portal pasien', function () {
    $this->withSession(adminSession())
        ->get('/patient/dashboard')->assertRedirect('/patient');
});

it('membiarkan halaman publik tetap terbuka', function () {
    foreach (['/admin/login', '/patient', '/therapist/login', '/booking', '/'] as $url) {
        $this->get($url)->assertOk();
    }
});

it('tidak lagi memakai env() di routes atau app (kompatibel config:cache)', function () {
    $offenders = [];

    foreach ([base_path('routes'), app_path()] as $dir) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php'
                && preg_match('/(?<![A-Za-z_>:])env\(/', file_get_contents($file->getPathname()))) {
                $offenders[] = $file->getPathname();
            }
        }
    }

    expect($offenders)->toBe([]);
});
