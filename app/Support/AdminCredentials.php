<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Verifikasi kredensial akun internal dari config('khayra_auth.{account}').
 *
 * Akun: 'admin' (staf operasional, default) dan 'owner' (pemilik klinik).
 * Tanpa fallback default: jika kredensial belum dikonfigurasi, login ditolak.
 */
class AdminCredentials
{
    public static function isConfigured(string $account = 'admin'): bool
    {
        return filled(config("khayra_auth.{$account}.email"))
            && (filled(config("khayra_auth.{$account}.password_hash")) || filled(config("khayra_auth.{$account}.password")));
    }

    public static function attempt(string $email, string $password, string $account = 'admin'): bool
    {
        if (! self::isConfigured($account)) {
            Log::warning('Login '.$account.' ditolak: kredensial '.$account.' belum dikonfigurasi.');

            return false;
        }

        $emailMatches = hash_equals(
            self::email($account),
            mb_strtolower(trim($email))
        );

        $passwordMatches = self::passwordMatches($password, $account);

        // Kedua pengecekan selalu dijalankan agar waktu respons tidak membocorkan mana yang salah.
        return $emailMatches && $passwordMatches;
    }

    /** Email admin kanonik dari config (dipakai sebagai aktor audit, bukan teks yang diketik pengguna). */
    public static function email(string $account = 'admin'): string
    {
        return mb_strtolower(trim((string) config("khayra_auth.{$account}.email")));
    }

    /** True bila ADMIN_PASSWORD_HASH terisi dengan format bcrypt yang valid. */
    public static function hashIsValid(string $account = 'admin'): bool
    {
        return (bool) preg_match('/^\$2[aby]\$\d{2}\$[.\/A-Za-z0-9]{53}$/', (string) config("khayra_auth.{$account}.password_hash"));
    }

    /** True bila login saat ini bergantung pada ADMIN_PASSWORD teks biasa (tidak ada hash). */
    public static function usesLegacyPlaintext(string $account = 'admin'): bool
    {
        return blank(config("khayra_auth.{$account}.password_hash")) && filled(config("khayra_auth.{$account}.password"));
    }

    /**
     * Sidik jari kredensial yang sedang berlaku (HMAC dengan APP_KEY; bukan password/hash itu sendiri).
     * Disimpan di sesi server saat login; bila kredensial dirotasi nilainya berubah sehingga sesi lama otomatis ditolak.
     */
    public static function fingerprint(string $account = 'admin'): string
    {
        if (! self::isConfigured($account)) {
            return '';
        }

        $secret = filled(config("khayra_auth.{$account}.password_hash"))
            ? 'hash:'.config("khayra_auth.{$account}.password_hash")
            : 'plain:'.config("khayra_auth.{$account}.password");

        return hash_hmac('sha256', self::email($account).'|'.$secret, (string) config('app.key'));
    }

    /**
     * Sesi admin masih berlaku terhadap kredensial saat ini? FAIL-CLOSED: sesi tanpa sidik jari (mis. dibuat oleh versi aplikasi
     * sebelum fitur ini), sidik jari kosong/bukan string, atau sidik jari yang tidak cocok dengan kredensial yang berlaku
     * SELALU ditolak. Tidak ada jalur khusus untuk sesi legacy: admin dari versi lama harus login ulang.
     */
    public static function sessionStillValid(mixed $sessionFingerprint, string $account = 'admin'): bool
    {
        if (! is_string($sessionFingerprint) || $sessionFingerprint === '' || ! self::isConfigured($account)) {
            return false;
        }

        return hash_equals(self::fingerprint($account), $sessionFingerprint);
    }

    private static function passwordMatches(string $password, string $account = 'admin'): bool
    {
        $hash = config("khayra_auth.{$account}.password_hash");

        if (filled($hash)) {
            try {
                return Hash::check($password, $hash);
            } catch (Throwable) {
                return false;
            }
        }

        return hash_equals((string) config("khayra_auth.{$account}.password"), $password);
    }
}
