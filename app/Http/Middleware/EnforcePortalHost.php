<?php

namespace App\Http\Middleware;

use App\Support\PortalHosts;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Isolasi hostname portal (fail-closed), middleware GLOBAL (jalan sebelum sesi dimulai).
 *
 *  app.khayraphysio.com     -> /admin/*, /owner/*
 *  physio.khayraphysio.com  -> /therapist/*
 *  patient.khayraphysio.com -> /patient/*, /booking
 *
 * Hostname tidak dikenal, akses lintas portal (semua method: GET, POST, ...), dan path di luar
 * pemetaan dijawab 404 polos. `/` diizinkan di tiga hostname resmi (route "/" mengarahkan ke login portalnya)
 * dan `/up` (health check) di hostname apa pun.
 *
 * Ini LAPIS TAMBAHAN. Sesi login (RequireAreaLogin) dan RBAC (PortalAccess, guard inline) tetap berjalan penuh.
 * Hostname dibaca dari Host permintaan; X-Forwarded-Host tidak pernah dipercaya (lihat bootstrap/app.php).
 * Nonaktif di lokal/testing kecuali PORTAL_HOST_ENFORCE=true.
 */
class EnforcePortalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! PortalHosts::enforcing()) {
            return $next($request);
        }

        $segment = PortalHosts::firstSegment($request);

        if (in_array($segment, (array) config('khayra_hosts.any_host_paths', []), true)) {
            return $next($request);
        }

        $hostPortal = PortalHosts::portalForHost($request->getHost());

        if ($hostPortal === null) {
            abort(404);
        }

        if ($segment === '') {
            return $next($request);
        }

        if (PortalHosts::portalForSegment($segment) !== $hostPortal) {
            abort(404);
        }

        return $next($request);
    }
}
