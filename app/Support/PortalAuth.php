<?php

namespace App\Support;

/**
 * Helper role untuk portal internal (admin & owner).
 *
 * Kedua role memakai flag session `admin_logged_in` (kompatibel dengan guard
 * lama di routes/web.php) dan dibedakan oleh session `portal_role`.
 */
class PortalAuth
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_OWNER = 'owner';

    public const ADMIN_HOME = '/admin/bookings';

    public const OWNER_HOME = '/owner/dashboard';

    /**
     * Cocokkan kredensial ke role. Admin dicek lebih dulu (least privilege):
     * kalau kredensial owner sama persis dengan admin, hasilnya tetap admin.
     */
    public static function attempt(string $email, string $password): ?string
    {
        $accounts = [
            self::ROLE_ADMIN => [config('portal.admin_email'), config('portal.admin_password')],
            self::ROLE_OWNER => [config('portal.owner_email'), config('portal.owner_password')],
        ];

        foreach ($accounts as $role => [$accountEmail, $accountPassword]) {
            if (! is_string($accountEmail) || $accountEmail === '' || ! is_string($accountPassword) || $accountPassword === '') {
                continue;
            }

            if (hash_equals($accountEmail, $email) && hash_equals($accountPassword, $password)) {
                return $role;
            }
        }

        return null;
    }

    /** Role sesi saat ini, null jika belum login. Sesi lama tanpa role = admin. */
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
