<?php

namespace App\Services;

/**
 * Satu sumber perhitungan "uang terbayar" dan "sisa tagihan" untuk laporan.
 *
 * Perilaku SAMA PERSIS dengan closure $moneyPaid / $moneyOutstanding yang sebelumnya
 * disalin di banyak route (halaman finance, closing, laporan, dashboard). Tidak ada
 * perubahan semantik: invoice void = 0; status paid dengan paid_amount <= 0 dianggap
 * terbayar penuh (fallback untuk data lama); selain itu paid_amount apa adanya.
 *
 * Catatan desain (belum diaktifkan, menunggu persetujuan): paid_amount yang berisi uang
 * yang diserahkan pasien (termasuk kembalian) tetap dihitung apa adanya di sini.
 */
class BillingMoney
{
    public static function paid($billing): float
    {
        if (($billing->payment_status ?? null) === 'void') {
            return 0.0;
        }

        $paid = (float) ($billing->paid_amount ?? 0);
        $amount = (float) ($billing->amount ?? 0);

        if (($billing->payment_status ?? null) === 'paid' && $paid <= 0) {
            return $amount;
        }

        return $paid;
    }

    public static function outstanding($billing): float
    {
        if (($billing->payment_status ?? null) === 'void') {
            return 0.0;
        }

        $total = (float) ($billing->grand_total ?? $billing->total_amount ?? $billing->amount ?? 0);
        $paid = (float) ($billing->paid_amount ?? 0);
        $status = $billing->payment_status ?: 'unpaid';

        if ($status === 'paid') {
            return 0.0;
        }

        return max($total - $paid, 0);
    }
}
