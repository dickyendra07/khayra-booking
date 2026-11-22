<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Pemetaan hostname <-> portal dan URL kanonis (config/khayra_hosts.php).
 *
 * Hanya lapis pertahanan tambahan: sesi login dan RBAC tetap menjadi penentu akses.
 */
class PortalHosts
{
    public const ADMIN = 'admin';

    public const THERAPIST = 'therapist';

    public const PATIENT = 'patient';

    /** Penegakan aktif? null di config = hanya saat APP_ENV=production. */
    public static function enforcing(): bool
    {
        $configured = config('khayra_hosts.enforce');

        return $configured === null ? app()->environment('production') : (bool) $configured;
    }

    public static function host(string $portal): ?string
    {
        $host = config("khayra_hosts.hosts.{$portal}");

        return is_string($host) && $host !== '' ? $host : null;
    }

    /** Portal pemilik hostname ini, atau null bila hostname tidak dikenal. */
    public static function portalForHost(string $host): ?string
    {
        $host = strtolower(rtrim($host, '.'));

        foreach ([self::ADMIN, self::THERAPIST, self::PATIENT] as $portal) {
            if (self::host($portal) === $host) {
                return $portal;
            }
        }

        return null;
    }

    /**
     * Segmen pertama path yang sudah di-decode (router Laravel mencocokkan path setelah rawurldecode,
     * sehingga "/%6Fwner/x" dan "/owner%2Fx" sampai ke route yang sama dengan "/owner/x").
     */
    public static function firstSegment(Request $request): string
    {
        $path = strtolower(trim($request->decodedPath(), '/'));

        return $path === '' ? '' : explode('/', $path)[0];
    }

    public static function portalForSegment(string $segment): ?string
    {
        $portal = config("khayra_hosts.areas.{$segment}");

        return is_string($portal) ? $portal : null;
    }

    /**
     * URL tautan lintas portal untuk halaman internal. Saat penegakan aktif: URL absolut ke hostname
     * portal tujuan; selain itu (lokal/testing): path relatif seperti semula.
     */
    public static function url(string $portal, string $path = '/'): string
    {
        $path = '/'.ltrim($path, '/');
        $host = self::host($portal);

        if (! self::enforcing() || $host === null) {
            return $path;
        }

        return 'https://'.$host.$path;
    }

    /**
     * URL portal pasien KANONIS dari konfigurasi. Tidak pernah memakai Host/X-Forwarded-Host permintaan,
     * sehingga aman dipakai untuk tautan yang dikirim ke pasien.
     */
    public static function patientUrl(string $path = ''): string
    {
        $base = (string) config('khayra_hosts.urls.patient');

        return $path === '' ? $base : $base.'/'.ltrim($path, '/');
    }
}
