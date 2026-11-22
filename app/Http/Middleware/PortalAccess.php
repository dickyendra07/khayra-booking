<?php

namespace App\Http\Middleware;

use App\Support\PortalAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pagar akses terpusat untuk area internal (deny-by-default).
 *
 *  - /admin/*  : wajib login (admin atau owner), kecuali halaman login/logout.
 *  - /owner/*  : wajib login sebagai OWNER. Admin mendapat 403.
 *
 * Route baru di bawah /owner otomatis terlindungi tanpa perlu guard manual.
 */
class PortalAccess
{
    private const PUBLIC_PATHS = ['admin/login', 'admin/logout'];

    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');

        $isAdminArea = $path === 'admin' || str_starts_with($path, 'admin/');
        $isOwnerArea = $path === 'owner' || str_starts_with($path, 'owner/');

        if (! $isAdminArea && ! $isOwnerArea) {
            return $next($request);
        }

        if (in_array($path, self::PUBLIC_PATHS, true)) {
            return $next($request);
        }

        $role = PortalAuth::role();

        if ($role === null) {
            return redirect('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($isOwnerArea && $role !== PortalAuth::ROLE_OWNER) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Owner.');
        }

        return $next($request);
    }
}
