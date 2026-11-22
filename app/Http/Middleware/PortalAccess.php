<?php

namespace App\Http\Middleware;

use App\Support\PortalAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gerbang role untuk area /owner/* (deny-by-default).
 *
 * Login & validitas sesi sudah ditangani RequireAreaLogin (yang juga mengelola
 * area owner). Middleware ini hanya memastikan role-nya OWNER; admin mendapat 403.
 * Route baru di bawah /owner otomatis terlindungi tanpa guard manual.
 */
class PortalAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Path di-decode seperti router ("/%6Fwner/x" dan "/owner%2Fx" harus tetap dianggap /owner/x).
        $path = strtolower(trim($request->decodedPath(), '/'));

        if (($path === 'owner' || str_starts_with($path, 'owner/')) && PortalAuth::role() !== PortalAuth::ROLE_OWNER) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Owner.');
        }

        return $next($request);
    }
}
