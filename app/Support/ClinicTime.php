<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Tanggal bisnis klinik (WIB) di atas penyimpanan UTC.
 *
 * Timestamp di database tetap UTC (tidak ada data historis yang bergeser). Semua "tanggal
 * operasional" (tanggal invoice, tanggal pembayaran, periode closing, nomor invoice, hari ini
 * di dashboard) harus memakai helper ini, bukan now() mentah yang bernilai UTC.
 */
class ClinicTime
{
    public static function timezone(): string
    {
        return (string) config('clinic.timezone', 'Asia/Jakarta');
    }

    /** Waktu sekarang menurut zona waktu klinik. */
    public static function now(): Carbon
    {
        return now()->setTimezone(self::timezone());
    }

    /** Tanggal bisnis hari ini (Y-m-d) menurut zona waktu klinik. */
    public static function today(): string
    {
        return self::now()->toDateString();
    }

    /** Kunci bulan bisnis (Y-m) saat ini. */
    public static function monthKey(): string
    {
        return self::now()->format('Y-m');
    }

    /** Ubah timestamp UTC dari database menjadi waktu klinik. */
    public static function toClinic($timestamp): ?Carbon
    {
        if (blank($timestamp)) {
            return null;
        }

        $carbon = $timestamp instanceof CarbonInterface ? Carbon::instance($timestamp) : Carbon::parse($timestamp, config('app.timezone'));

        return $carbon->copy()->setTimezone(self::timezone());
    }

    /** Teks untuk tampilan, mis. 2026-10-12 00:14. */
    public static function display($timestamp, string $format = 'Y-m-d H:i'): string
    {
        $clinic = self::toClinic($timestamp);

        return $clinic ? $clinic->format($format) : '-';
    }

    /**
     * Rentang UTC [awal, akhir] untuk tanggal bisnis klinik, dipakai membandingkan kolom timestamp
     * (created_at, dll.) dengan tanggal bisnis. $from dan $to berformat Y-m-d (inklusif).
     *
     * @return array{0: string, 1: string}
     */
    public static function utcRange(string $from, string $to): array
    {
        $start = Carbon::parse($from, self::timezone())->startOfDay()->setTimezone(config('app.timezone'));
        $end = Carbon::parse($to, self::timezone())->endOfDay()->setTimezone(config('app.timezone'));

        return [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
    }
}
