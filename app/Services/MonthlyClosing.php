<?php

namespace App\Services;

use App\Models\FinanceMonthlyClosing;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Tutup buku dan buka kembali periode bulanan. Perhitungan laporan dan penulisan snapshot terjadi dalam SATU transaction
 * yang memegang kunci EKSKLUSIF periode (FinancePeriodLock): tidak ada transaksi finansial yang dapat commit di antara
 * perhitungan dan penyimpanan, sehingga snapshot selalu sama dengan keadaan final periode itu.
 */
class MonthlyClosing
{
    /**
     * Hanya untuk uji konkurensi: dipanggil setelah kunci eksklusif diambil dan sebelum laporan dihitung,
     * untuk memperlebar jendela balapan secara deterministik. Selalu null di aplikasi.
     *
     * @var (\Closure(string): void)|null
     */
    public static ?\Closure $afterLockHook = null;

    /**
     * Hanya untuk uji konkurensi: dipanggil SETELAH laporan dihitung dan SEBELUM snapshot ditulis (jendela balapan klasik).
     * Selalu null di aplikasi.
     *
     * @var (\Closure(string): void)|null
     */
    public static ?\Closure $afterComputeHook = null;

    /**
     * @throws DomainException bila periode sudah tutup
     */
    public static function close(string $month, ?string $notes, string $actor): FinanceMonthlyClosing
    {
        FinancePeriodLock::prepare([$month.'-01']);

        return DB::transaction(function () use ($month, $notes, $actor) {
            // LANGKAH PERTAMA: kunci eksklusif periode (menunggu semua penulis yang sedang berjalan).
            FinancePeriodLock::exclusive($month);

            if (self::$afterLockHook) {
                (self::$afterLockHook)($month);
            }

            // Closing tidak boleh ditimpa. Untuk menutup ulang, bulan harus dibuka kembali lebih dulu.
            if (FinancePeriodLock::isClosed($month)) {
                throw new DomainException('Bulan '.$month.' sudah tutup buku dan tidak bisa ditimpa. Gunakan Buka Kembali Buku bila koreksi diperlukan.');
            }

            $startDate = Carbon::parse($month.'-01')->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            // Satu sumber perhitungan yang sama dengan halaman Owner Finance, dihitung SETELAH kunci diambil.
            $report = FinanceReport::forMonth($month, false);
            $transactions = $report['rows']->map(function ($row) {
                unset($row['editable'], $row['url']);
                $row['date'] = $row['date'] instanceof \DateTimeInterface ? $row['date']->format('Y-m-d') : (string) $row['date'];

                return $row;
            })->values();

            $summary = $report['summary'];
            $closingSummary = [
                'invoice_revenue' => $summary['invoice_revenue'],
                'billing_income' => $summary['billing_income'],
                'manual_income' => $summary['manual_income'],
                'total_income' => $summary['total_income'],
                'total_expense' => $summary['expense'],
                'net_cashflow' => $summary['net_cashflow'],
                'transaction_count' => $transactions->count(),
            ];

            if (self::$afterComputeHook) {
                (self::$afterComputeHook)($month);
            }

            // Closing sebelumnya (yang sudah dibuka kembali) dicatat di audit sebelum diganti.
            $previousClosing = FinanceMonthlyClosing::where('month', $month)->first();

            BillingAudit::record(
                'period_closed',
                null,
                $previousClosing ? ['previous_closing' => $previousClosing->only(['billing_income', 'manual_income', 'total_income', 'total_expense', 'net_cashflow', 'transaction_count', 'closed_at', 'reopened_at', 'reopened_by', 'reopen_reason', 'snapshot'])] : null,
                $closingSummary,
                $notes,
                $month
            );

            return FinanceMonthlyClosing::updateOrCreate(
                ['month' => $month],
                [
                    'reopened_at' => null,
                    'reopened_by' => null,
                    'reopen_reason' => null,
                    'period_start' => $startDate->toDateString(),
                    'period_end' => $endDate->toDateString(),
                    'billing_income' => $summary['billing_income'],
                    'manual_income' => $summary['manual_income'],
                    'total_income' => $summary['total_income'],
                    'total_expense' => $summary['expense'],
                    'net_cashflow' => $summary['net_cashflow'],
                    'transaction_count' => $transactions->count(),
                    'closed_by' => $actor,
                    'closed_at' => now(),
                    'notes' => $notes,
                    'snapshot' => [
                        // basis: arus kas (invoice baru per tanggal pembayaran, invoice lama per tanggal invoice).
                        // invoice_revenue: omzet per tanggal invoice (informasi, tidak masuk arus kas).
                        'basis' => 'cash_v2',
                        'summary' => $closingSummary,
                        'transactions' => $transactions->values()->all(),
                    ],
                ]
            );
        }, 3);
    }

    /**
     * Buka kembali periode. Kunci eksklusif yang sama dengan tutup buku, dan status "masih tertutup" dibaca ulang
     * setelah kunci diambil.
     *
     * @throws DomainException bila periode tidak dalam status tutup buku
     */
    public static function reopen(string $month, string $reason, string $actor): FinanceMonthlyClosing
    {
        FinancePeriodLock::prepare([$month.'-01']);

        return DB::transaction(function () use ($month, $reason, $actor) {
            FinancePeriodLock::exclusive($month);

            $closing = FinanceMonthlyClosing::active()->where('month', $month)->lockForUpdate()->first();

            if (! $closing) {
                throw new DomainException('Bulan '.$month.' tidak dalam status tutup buku.');
            }

            $closing->update(['reopened_at' => now(), 'reopened_by' => $actor, 'reopen_reason' => $reason]);

            BillingAudit::record(
                'period_reopened',
                null,
                $closing->only(['billing_income', 'manual_income', 'total_income', 'total_expense', 'net_cashflow', 'transaction_count', 'closed_at']),
                ['reopened' => true],
                $reason,
                $month
            );

            return $closing;
        }, 3);
    }
}
