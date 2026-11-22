<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Role portal internal (admin & owner), di atas AdminCredentials.
 *
 * Kedua role memakai flag sesi `admin_logged_in` (kompatibel dengan guard lama)
 * dan dibedakan oleh sesi `portal_role`. Sesi tanpa `portal_role` = admin.
 */
class PortalAuth
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_OWNER = 'owner';

    public const ADMIN_HOME = '/admin/bookings';

    public const OWNER_HOME = '/owner/dashboard';

    /**
     * Cocokkan kredensial ke role. Admin dicek lebih dulu (least privilege): kalau
     * kredensial owner sama persis dengan admin, hasilnya tetap admin. Akun owner
     * yang belum dikonfigurasi dilewati tanpa efek apa pun.
     */
    public static function attempt(string $email, string $password): ?string
    {
        if (AdminCredentials::attempt($email, $password, self::ROLE_ADMIN)) {
            return self::ROLE_ADMIN;
        }

        if (AdminCredentials::isConfigured(self::ROLE_OWNER)
            && AdminCredentials::attempt($email, $password, self::ROLE_OWNER)) {
            return self::ROLE_OWNER;
        }

        return null;
    }

    /** Tulis sesi login untuk role yang sudah terverifikasi (panggil setelah session()->regenerate()). */
    public static function login(Request $request, string $role): void
    {
        $request->session()->put([
            'admin_logged_in' => true,
            'admin_email' => AdminCredentials::email($role),
            'admin_credential_fp' => AdminCredentials::fingerprint($role),
            'portal_role' => $role,
        ]);
    }

    /** Role sesi saat ini, null jika belum login. */
    public static function role(): ?string
    {
        if (! session('admin_logged_in')) {
            return null;
        }

        return session('portal_role') === self::ROLE_OWNER
            ? self::ROLE_OWNER
            : self::ROLE_ADMIN;
    }

    public static function isOwner(): bool
    {
        return self::role() === self::ROLE_OWNER;
    }

    public static function homeFor(?string $role): string
    {
        return $role === self::ROLE_OWNER ? self::OWNER_HOME : self::ADMIN_HOME;
    }
}
