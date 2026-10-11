<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\FinanceMonthlyClosing;
use Carbon\Carbon;

/**
 * Aturan integritas invoice: penguncian, periode tutup buku, dan transisi status.
 *
 * Tidak ada mekanisme pembayaran baru: status 'paid' hanya menyamakan paid_amount
 * dengan total invoice (perilaku yang sudah ada), dan perubahan lain wajib lewat
 * void + terbit ulang agar histori tetap utuh.
 */
class BillingIntegrity
{
    /** Transisi status yang diizinkan lewat edit/ubah status. Selain ini harus lewat void. */
    private const ALLOWED_TRANSITIONS = [
        'unpaid' => ['paid'],
        'partial' => ['paid'],
        'paid' => [],
        'void' => [],
    ];

    public static function monthOf($date): ?string
    {
        if (blank($date)) {
            return null;
        }

        return Carbon::parse($date)->format('Y-m');
    }

    public static function isPeriodClosed($date): bool
    {
        $month = self::monthOf($date);

        return $month !== null && FinanceMonthlyClosing::isClosed($month);
    }

    public static function closedPeriodMessage($date): string
    {
        return 'Periode '.self::monthOf($date).' sudah tutup buku. Perubahan finansial tidak diizinkan; '
            .'buka kembali periode lewat prosedur Buka Kembali Buku di Owner Finance bila koreksi memang diperlukan.';
    }

    public static function transitionAllowed(?string $from, string $to): bool
    {
        $from = $from ?: 'unpaid';

        return $from === $to || in_array($to, self::ALLOWED_TRANSITIONS[$from] ?? [], true);
    }

    /**
     * Status penguncian sebuah invoice.
     *
     * @return array{void: bool, closed: bool, itemised: bool, paid: bool, amount_locked: bool, identity_locked: bool, method_locked: bool, status_locked: bool, reasons: array<int, string>}
     */
    public static function lock(Billing $billing): array
    {
        $void = $billing->payment_status === 'void' || ! is_null($billing->voided_at);
        $closed = self::isPeriodClosed($billing->invoice_date);
        $paid = in_array($billing->payment_status, ['paid', 'partial'], true);
        $itemised = $billing->items()->exists();

        $reasons = [];

        if ($void) {
            $reasons[] = 'Invoice sudah di-void dan tidak dapat diubah.';
        }

        if ($closed) {
            $reasons[] = 'Periode '.self::monthOf($billing->invoice_date).' sudah tutup buku.';
        }

        if ($paid) {
            $reasons[] = 'Invoice sudah memiliki pembayaran ('.$billing->payment_status.'): nominal dan data utama dikunci.';
        }

        if ($itemised) {
            $reasons[] = 'Invoice memiliki rincian item: total mengikuti item dan tidak bisa diubah manual.';
        }

        return [
            'void' => $void,
            'closed' => $closed,
            'itemised' => $itemised,
            'paid' => $paid,
            'amount_locked' => $void || $closed || $paid || $itemised,
            'identity_locked' => $void || $closed || $paid,
            'method_locked' => $void || $closed,
            'status_locked' => $void || $closed,
            'reasons' => $reasons,
        ];
    }

    /**
     * Samakan field pembayaran dengan status. Untuk 'partial' paid_amount harus
     * diberikan (0 < paid < amount).
     */
    public static function applyStatus(Billing $billing, string $status, ?float $partialPaid = null): void
    {
        $amount = (float) $billing->amount;

        $billing->payment_status = $status;

        if ($status === 'paid') {
            $billing->paid_amount = $amount;
            $billing->remaining_amount = 0;
            $billing->change_amount = 0;
        } elseif ($status === 'unpaid') {
            $billing->paid_amount = 0;
            $billing->remaining_amount = $amount;
            $billing->change_amount = 0;
        } elseif ($status === 'partial') {
            $paid = (float) $partialPaid;
            $billing->paid_amount = $paid;
            $billing->remaining_amount = max($amount - $paid, 0);
            $billing->change_amount = 0;
        }
    }
}
