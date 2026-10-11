<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Verifikasi kredensial admin dari config('khayra_auth.admin').
 *
 * Tanpa fallback default: jika kredensial belum dikonfigurasi, login ditolak.
 */
class AdminCredentials
{
    public static function isConfigured(): bool
    {
        return filled(config('khayra_auth.admin.email'))
            && (filled(config('khayra_auth.admin.password_hash')) || filled(config('khayra_auth.admin.password')));
    }

    public static function attempt(string $email, string $password): bool
    {
        if (! self::isConfigured()) {
            Log::warning('Login admin ditolak: ADMIN_EMAIL / ADMIN_PASSWORD(_HASH) belum dikonfigurasi.');

            return false;
        }

        $emailMatches = hash_equals(
            mb_strtolower(trim((string) config('khayra_auth.admin.email'))),
            mb_strtolower(trim($email))
        );

        $passwordMatches = self::passwordMatches($password);

        // Kedua pengecekan selalu dijalankan agar waktu respons tidak membocorkan mana yang salah.
        return $emailMatches && $passwordMatches;
    }

    private static function passwordMatches(string $password): bool
    {
        $hash = config('khayra_auth.admin.password_hash');

        if (filled($hash)) {
            try {
                return Hash::check($password, $hash);
            } catch (Throwable) {
                return false;
            }
        }

        return hash_equals((string) config('khayra_auth.admin.password'), $password);
    }
}
