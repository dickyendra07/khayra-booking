<?php

namespace App\Services;

use App\Models\FinanceMonthlyClosing;
use App\Support\PeriodClosedException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Sinkronisasi tutup buku per periode (bulan, Y-m).
 *
 * MASALAH: tutup buku menghitung laporan lalu menulis snapshot, sedangkan pembayaran/refund/invoice/transaksi manual
 * berjalan di transaction lain dan hanya memeriksa "periode tertutup" tanpa kunci bersama. Transaksi yang commit di antara
 * perhitungan dan penulisan snapshot lolos dari snapshot.
 *
 * ATURAN (wajib konsisten di semua penulis finansial):
 *  1. Ambil kunci periode sebagai LANGKAH PERTAMA di dalam transaction, sebelum kunci baris lain (invoice, sumber, counter).
 *     - Penulis (invoice baru, pembayaran, refund, void, invoice manual, transaksi finance manual, penerbitan biaya
 *       registrasi, edit invoice): `shared()` = kunci BERSAMA. Sesama penulis tidak saling menunggu.
 *     - Tutup buku dan buka kembali: `exclusive()` = kunci EKSKLUSIF; menunggu semua penulis yang sedang berjalan selesai,
 *       dan penulis baru menunggu sampai tutup buku commit (lalu melihat periode tertutup).
 *  2. Beberapa periode diambil terurut menaik (deterministik) -> tidak ada lock inversion antar penulis.
 *  3. Pemeriksaan "periode tertutup" dilakukan SETELAH kunci diambil dan memakai locking read (`isClosed`), bukan
 *     `BillingIntegrity::isPeriodClosed` di luar transaction (pembacaan konsisten REPEATABLE READ bisa basi).
 *  4. Periode yang terpengaruh: omzet = bulan invoice_date; arus kas = bulan paid_on (hari bisnis transaksi).
 *     Contoh: membayar invoice bulan lalu hari ini hanya memengaruhi bulan ini (kas), bukan bulan lalu.
 *
 * Baris kunci dibuat dengan `prepare()` DI LUAR transaction (autocommit; insert di dalam transaction bisa deadlock di InnoDB,
 * pola yang sama dengan InvoiceNumberGenerator). Bila terlewat, `lockRow` membuatnya secara lazy sebagai cadangan.
 * Di SQLite (tes) kunci baris adalah no-op; urutan dan pemeriksaan tetap berjalan.
 */
class FinancePeriodLock
{
    /** @return array<int, string> bulan Y-m unik, terurut menaik */
    public static function monthsOf(iterable $dates): array
    {
        $months = [];

        foreach ($dates as $date) {
            if (blank($date)) {
                continue;
            }

            $months[Carbon::parse($date)->format('Y-m')] = true;
        }

        $months = array_keys($months);
        sort($months);

        return $months;
    }

    /** Pastikan baris kunci ada. Panggil SEBELUM membuka transaction (tidak melakukan apa pun bila sudah di dalam transaction). */
    public static function prepare(iterable $dates): void
    {
        if (DB::transactionLevel() > 0) {
            return;
        }

        foreach (self::monthsOf($dates) as $month) {
            self::ensureRow($month);
        }
    }

    /**
     * Kunci BERSAMA untuk penulis. Harus di dalam transaction, sebagai langkah pertama.
     *
     * @return array<int, string> bulan yang dikunci
     */
    public static function shared(iterable $dates): array
    {
        $months = self::monthsOf($dates);

        foreach ($months as $month) {
            self::lockRow($month, false);
        }

        return $months;
    }

    /** Kunci EKSKLUSIF untuk tutup buku / buka kembali. Harus di dalam transaction, sebagai langkah pertama. */
    public static function exclusive(string $month): void
    {
        self::lockRow($month, true);
    }

    /** Apakah periode tertutup, dibaca dengan locking read (versi terbaru yang sudah commit). Panggil SETELAH kunci periode. */
    public static function isClosed(string $monthOrDate): bool
    {
        $month = Carbon::parse(strlen($monthOrDate) === 7 ? $monthOrDate.'-01' : $monthOrDate)->format('Y-m');

        return FinanceMonthlyClosing::active()->where('month', $month)->sharedLock()->first() !== null;
    }

    /** @throws PeriodClosedException */
    public static function assertOpen($date): void
    {
        if (filled($date) && self::isClosed(Carbon::parse($date)->format('Y-m-d'))) {
            throw new PeriodClosedException(BillingIntegrity::closedPeriodMessage($date));
        }
    }

    private static function lockRow(string $month, bool $exclusive): void
    {
        $select = fn () => $exclusive
            ? DB::table('finance_period_locks')->where('month', $month)->lockForUpdate()->first()
            : DB::table('finance_period_locks')->where('month', $month)->sharedLock()->first();

        if (! $select()) {
            self::ensureRow($month);
            $select();
        }
    }

    private static function ensureRow(string $month): void
    {
        // Baca biasa (tanpa lock) dulu: INSERT IGNORE pada baris yang sudah ada mengambil kunci bersama pada baris itu dan
        // akan menunggu tutup buku yang sedang berjalan di luar urutan kunci yang disengaja.
        if (! DB::table('finance_period_locks')->where('month', $month)->exists()) {
            DB::table('finance_period_locks')->insertOrIgnore(['month' => $month, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
