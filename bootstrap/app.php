<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Isolasi hostname portal (global, sebelum sesi dimulai). Hanya lapis tambahan; RBAC tetap berjalan.
        $middleware->append(\App\Http\Middleware\EnforcePortalHost::class);

        // Header X-Forwarded-Host TIDAK pernah dipercaya (hostname hanya dari Host). Daftar proxy tepercaya
        // dikendalikan config/trustedproxy.php (default: kosong = tidak ada proxy yang dipercaya).
        $middleware->trustProxies(headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
            | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
            | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO);

        // Pengaman terpusat area admin / therapist / patient (lihat RequireAreaLogin).
        $middleware->appendToGroup('web', \App\Http\Middleware\RequireAreaLogin::class);

        // Gerbang role: /owner/* hanya untuk Owner (berjalan setelah RequireAreaLogin).
        $middleware->appendToGroup('web', \App\Http\Middleware\PortalAccess::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
