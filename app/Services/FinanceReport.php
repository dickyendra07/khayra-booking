<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\BillingPayment;
use App\Models\FinanceTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Satu sumber perhitungan Owner Finance, cetak, dan tutup buku.
 *
 * Dua pandangan yang SENGAJA dipisah:
 *  - OMZET (invoice_revenue): total invoice non-void berdasarkan TANGGAL INVOICE.
 *  - ARUS KAS (billing_income): uang yang benar-benar masuk/keluar.
 *      * Invoice baru (payment_model = ledger): baris billing_payments berdasarkan TANGGAL
 *        PEMBAYARAN (paid_on, WIB). Pembayaran = penerimaan BERSIH (tanpa kembalian), refund
 *        mengurangi, pemindahan kredit antar invoice bukan kas dan tidak dihitung.
 *      * Invoice lama (legacy): rumus lama persis (BillingMoney) berdasarkan tanggal invoice,
 *        karena tidak ada tanggal pembayaran historis. Tidak dihitung ulang.
 */
class FinanceReport
{
    /**
     * @return array{rows: Collection, summary: array, manual: Collection}
     */
    public static function forMonth(string $month, bool $descending = true): array
    {
        $start = Carbon::parse($month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $billingRows = self::billingCashRows($start->toDateString(), $end->toDateString());

        $manual = FinanceTransaction::whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->latest('transaction_date')->latest('id')->get();

        $manualRows = $manual->map(fn ($transaction) => [
            'kind' => 'manual',
            'id' => $transaction->id,
            'date' => $transaction->transaction_date,
            'type' => $transaction->type,
            'source' => $transaction->source_label,
            'category' => $transaction->category ?: '-',
            'title' => $transaction->title ?: $transaction->source_label,
            'description' => $transaction->notes ?: '',
            'amount' => (float) $transaction->amount,
            'signed' => $transaction->type === 'income' ? (float) $transaction->amount : -(float) $transaction->amount,
            'payment_method' => $transaction->payment_method_label,
            'editable' => true,
            'url' => null,
        ]);

        $key = fn ($row) => ((string) $row['date']).'-'.str_pad((string) $row['id'], 8, '0', STR_PAD_LEFT);
        $rows = $billingRows->concat($manualRows);
        $rows = $descending ? $rows->sortByDesc($key)->values() : $rows->sortBy($key)->values();

        $billingIncome = (float) $billingRows->sum('signed');
        $manualIncome = (float) $manual->where('type', 'income')->sum('amount');
        $manualExpense = (float) $manual->where('type', 'expense')->sum('amount');
        $totalIncome = $billingIncome + $manualIncome;

        return [
            'rows' => $rows,
            'manual' => $manual,
            'summary' => [
                'billing_income' => $billingIncome,
                'manual_income' => $manualIncome,
                'total_income' => $totalIncome,
                'expense' => $manualExpense,
                'net_cashflow' => $totalIncome - $manualExpense,
                'transaction_count' => $rows->count(),
                // Informasi tambahan (tidak masuk arus kas):
                'invoice_revenue' => self::invoiceRevenue($start->toDateString(), $end->toDateString()),
                'refunds' => (float) $billingRows->where('kind', 'refund')->sum('amount'),
                'held_credit' => self::heldCredit(),
            ],
        ];
    }

    /** Baris arus kas dari invoice untuk rentang tanggal bisnis (Y-m-d, inklusif). */
    public static function billingCashRows(string $from, string $to): Collection
    {
        $legacy = Billing::with(['patient'])
            ->where(fn ($q) => $q->whereNull('payment_model')->orWhere('payment_model', '!=', 'ledger'))
            ->whereBetween('invoice_date', [$from, $to])
            ->where('payment_status', '!=', 'void')
            ->get()
            ->map(function ($billing) {
                $paid = BillingMoney::paid($billing);

                return [
                    'kind' => 'auto_billing',
                    'id' => $billing->id,
                    'date' => $billing->invoice_date,
                    'type' => 'income',
                    'source' => 'Auto Billing',
                    'category' => 'Treatment Revenue',
                    'title' => $billing->invoice_number ?: ('Invoice #'.$billing->id),
                    'description' => optional($billing->patient)->full_name ?: 'Patient belum terhubung',
                    'amount' => $paid,
                    'signed' => $paid,
                    'payment_method' => $billing->payment_method_label ?? '-',
                    'editable' => false,
                    'url' => '/admin/billings/'.$billing->id,
                ];
            });

        $ledger = BillingPayment::with(['billing.patient'])
            ->whereIn('type', [BillingPayment::TYPE_PAYMENT, BillingPayment::TYPE_REFUND])
            ->whereBetween('paid_on', [$from, $to])
            ->get()
            ->map(function ($payment) {
                $isRefund = $payment->type === BillingPayment::TYPE_REFUND;
                $billing = $payment->billing;

                return [
                    'kind' => $isRefund ? 'refund' : 'payment',
                    'id' => $payment->id,
                    'date' => $payment->paid_on,
                    'type' => $isRefund ? 'expense' : 'income',
                    'source' => $isRefund ? 'Refund Pasien' : 'Pembayaran Invoice',
                    'category' => $isRefund ? 'Refund' : 'Treatment Revenue',
                    'title' => optional($billing)->invoice_number ?: ('Invoice #'.$payment->billing_id),
                    'description' => optional(optional($billing)->patient)->full_name ?: 'Patient belum terhubung',
                    'amount' => (float) $payment->amount,
                    'signed' => $isRefund ? -(float) $payment->amount : (float) $payment->amount,
                    'payment_method' => $payment->method_label,
                    'editable' => false,
                    'url' => '/admin/billings/'.$payment->billing_id,
                ];
            });

        return $legacy->concat($ledger)->filter(fn ($row) => (float) $row['amount'] > 0)->values();
    }

    /** OMZET: total invoice non-void berdasarkan tanggal invoice. */
    public static function invoiceRevenue(string $from, string $to): float
    {
        return (float) Billing::whereBetween('invoice_date', [$from, $to])->where('payment_status', '!=', 'void')->sum('amount');
    }

    /** Uang tertahan pada invoice void yang belum direfund/dipindahkan (kewajiban, bukan pendapatan). */
    public static function heldCredit(): float
    {
        return (float) Billing::where('payment_model', 'ledger')->where('payment_status', 'void')->get()
            ->sum(fn ($billing) => PaymentLedger::heldOnVoid($billing));
    }
}
