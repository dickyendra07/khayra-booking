<?php

namespace App\Http\Middleware;

use App\Support\AdminCredentials;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pengaman terpusat (fail-closed) untuk area login berbasis sesi.
 *
 * Setiap path di bawah /admin, /therapist, dan /patient wajib punya sesi login
 * yang sesuai, kecuali halaman login/logout. Rute baru otomatis terlindungi
 * walaupun lupa menambahkan pengecekan sesi di dalam closure-nya. Pengecekan
 * inline yang sudah ada tetap berjalan sebagai lapis kedua.
 */
class RequireAreaLogin
{
    /**
     * @var array<string, array{session: string, login: string, public: array<int, string>}>
     */
    private const AREAS = [
        'admin' => [
            'session' => 'admin_logged_in',
            'login' => '/admin/login',
            'public' => ['admin/login', 'admin/logout'],
        ],
        'therapist' => [
            'session' => 'therapist_logged_in',
            'login' => '/therapist/login',
            'public' => ['therapist', 'therapist/login', 'therapist/logout'],
        ],
        'patient' => [
            'session' => 'patient_logged_in',
            'login' => '/patient',
            'public' => ['patient', 'patient/login', 'patient/logout'],
        ],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');
        $area = explode('/', $path)[0];

        if (! isset(self::AREAS[$area])) {
            return $next($request);
        }

        $config = self::AREAS[$area];

        if (in_array($path, $config['public'], true)) {
            return $next($request);
        }

        if ($request->session()->get($config['session'])) {
            // Rotasi kredensial admin membatalkan sesi admin yang dibuat dengan kredensial lama.
            if ($area === 'admin' && ! AdminCredentials::sessionStillValid($request->session()->get('admin_credential_fp'))) {
                $request->session()->forget(['admin_logged_in', 'admin_email', 'admin_credential_fp']);

                return redirect($config['login']);
            }

            return $next($request);
        }

        return redirect($config['login']);
    }
}
