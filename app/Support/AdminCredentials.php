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

    /** Email admin kanonik dari config (dipakai sebagai aktor audit, bukan teks yang diketik pengguna). */
    public static function email(): string
    {
        return mb_strtolower(trim((string) config('khayra_auth.admin.email')));
    }

    /** True bila ADMIN_PASSWORD_HASH terisi dengan format bcrypt yang valid. */
    public static function hashIsValid(): bool
    {
        return (bool) preg_match('/^\$2[aby]\$\d{2}\$[.\/A-Za-z0-9]{53}$/', (string) config('khayra_auth.admin.password_hash'));
    }

    /** True bila login saat ini bergantung pada ADMIN_PASSWORD teks biasa (tidak ada hash). */
    public static function usesLegacyPlaintext(): bool
    {
        return blank(config('khayra_auth.admin.password_hash')) && filled(config('khayra_auth.admin.password'));
    }

    /**
     * Sidik jari kredensial yang sedang berlaku (HMAC dengan APP_KEY; bukan password/hash itu sendiri).
     * Disimpan di sesi server saat login; bila kredensial dirotasi nilainya berubah sehingga sesi lama otomatis ditolak.
     */
    public static function fingerprint(): string
    {
        if (! self::isConfigured()) {
            return '';
        }

        $secret = filled(config('khayra_auth.admin.password_hash'))
            ? 'hash:'.config('khayra_auth.admin.password_hash')
            : 'plain:'.config('khayra_auth.admin.password');

        return hash_hmac('sha256', self::email().'|'.$secret, (string) config('app.key'));
    }

    /**
     * Sesi admin masih berlaku terhadap kredensial saat ini?
     * Sesi tanpa sidik jari (dibuat sebelum fitur ini ada) tetap diterima sampai kedaluwarsa normal; sesi dengan
     * sidik jari berbeda (kredensial sudah dirotasi atau dihapus) ditolak.
     */
    public static function sessionStillValid(?string $sessionFingerprint): bool
    {
        if ($sessionFingerprint === null) {
            return true;
        }

        return self::isConfigured() && hash_equals(self::fingerprint(), $sessionFingerprint);
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
