<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\BillingPayment;
use App\Support\ClinicTime;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Buku pembayaran invoice (payment_model = 'ledger'). Satu-satunya tempat yang menulis
 * baris billing_payments dan menyelaraskan kolom paid/remaining/change/status pada billings.
 *
 * - Uang kembalian BUKAN pendapatan: yang dicatat sebagai penerimaan adalah nilai bersih
 *   (tendered - change), dibatasi sisa tagihan.
 * - Refund adalah baris tersendiri dan memerlukan alasan.
 * - Pemindahan kredit ke invoice pengganti adalah dua baris non-kas (credit_out / credit_in),
 *   sehingga tidak dihitung sebagai penerimaan kas baru dan tidak bisa dipindahkan dua kali.
 * - Setiap operasi berjalan dalam transaction dengan row lock pada invoice.
 *
 * Invoice lama (legacy) tidak pernah disentuh oleh kelas ini.
 */
class PaymentLedger
{
    /** Total yang menutup tagihan invoice ini: pembayaran + kredit masuk - refund - kredit keluar. */
    public static function paidTotal(Billing $billing): float
    {
        $rows = BillingPayment::where('billing_id', $billing->id)->get();

        return round(
            (float) $rows->where('type', BillingPayment::TYPE_PAYMENT)->sum('amount')
            + (float) $rows->where('type', BillingPayment::TYPE_CREDIT_IN)->sum('amount')
            - (float) $rows->where('type', BillingPayment::TYPE_REFUND)->sum('amount')
            - (float) $rows->where('type', BillingPayment::TYPE_CREDIT_OUT)->sum('amount'),
            2
        );
    }

    /** Uang yang masih "tertahan" pada invoice void (belum direfund / dipindahkan). */
    public static function heldOnVoid(Billing $billing): float
    {
        if (! $billing->is_ledger) {
            return 0.0;
        }

        return max(self::paidTotal($billing), 0.0);
    }

    public static function outstanding(Billing $billing): float
    {
        return round(max((float) $billing->amount - self::paidTotal($billing), 0), 2);
    }

    /**
     * Catat pembayaran pada invoice yang sudah ada (cicilan atau pelunasan).
     *
     * @throws DomainException bila tidak boleh dicatat
     */
    public function recordPayment(Billing $billing, float $tendered, ?string $method, ?string $reference = null): BillingPayment
    {
        return DB::transaction(function () use ($billing, $tendered, $method, $reference) {
            $locked = Billing::lockForUpdate()->findOrFail($billing->id);

            if (! $locked->is_ledger) {
                throw new DomainException('Invoice lama (legacy) tidak memakai buku pembayaran.');
            }

            if ($locked->payment_status === 'void' || $locked->voided_at) {
                throw new DomainException('Invoice void tidak dapat menerima pembayaran.');
            }

            $payment = $this->applyPayment($locked, $tendered, $method, $reference);
            BillingAudit::record('payment_recorded', $locked, null, [
                'tendered' => $payment->tendered_amount, 'change' => $payment->change_amount, 'net' => $payment->amount,
                'method' => $payment->method, 'paid_on' => $payment->paid_on->toDateString(),
            ]);

            return $payment;
        });
    }

    /**
     * Pembayaran awal saat invoice baru dibuat (invoice sudah terkunci oleh transaction pembuatnya).
     */
    public function applyPayment(Billing $billing, float $tendered, ?string $method, ?string $reference = null): BillingPayment
    {
        if ($tendered <= 0) {
            throw new DomainException('Jumlah pembayaran harus lebih dari 0.');
        }

        $paidOn = ClinicTime::today();

        if (BillingIntegrity::isPeriodClosed($paidOn)) {
            throw new DomainException('Periode '.BillingIntegrity::monthOf($paidOn).' sudah tutup buku; pembayaran tidak dapat dicatat pada periode tertutup.');
        }

        $outstanding = self::outstanding($billing);

        if ($outstanding <= 0) {
            throw new DomainException('Invoice sudah lunas.');
        }

        $net = round(min($tendered, $outstanding), 2);
        $change = round($tendered - $net, 2);

        $payment = BillingPayment::create([
            'billing_id' => $billing->id,
            'type' => BillingPayment::TYPE_PAYMENT,
            'method' => $method,
            'tendered_amount' => round($tendered, 2),
            'change_amount' => $change,
            'amount' => $net,
            'paid_at' => now(),
            'paid_on' => $paidOn,
            'reference' => $reference,
            'recorded_by' => session('admin_email') ?: 'admin',
        ]);

        $this->sync($billing);

        return $payment;
    }

    /**
     * Refund uang yang tertahan pada invoice void. Alasan wajib. Tidak bisa melebihi saldo tertahan.
     *
     * @throws DomainException
     */
    public function refund(Billing $billing, float $amount, string $method, string $reason): BillingPayment
    {
        if (mb_strlen(trim($reason)) < 5) {
            throw new DomainException('Alasan refund wajib diisi (minimal 5 karakter).');
        }

        return DB::transaction(function () use ($billing, $amount, $method, $reason) {
            $locked = Billing::lockForUpdate()->findOrFail($billing->id);

            if (! $locked->is_ledger || ($locked->payment_status !== 'void' && ! $locked->voided_at)) {
                throw new DomainException('Refund hanya untuk invoice void yang memakai buku pembayaran.');
            }

            $paidOn = ClinicTime::today();

            if (BillingIntegrity::isPeriodClosed($paidOn)) {
                throw new DomainException('Periode '.BillingIntegrity::monthOf($paidOn).' sudah tutup buku; refund tidak dapat dicatat pada periode tertutup.');
            }

            $held = self::heldOnVoid($locked);
            $amount = round($amount, 2);

            if ($amount <= 0 || $amount > $held + 0.001) {
                throw new DomainException('Jumlah refund harus lebih dari 0 dan tidak melebihi saldo tertahan Rp'.number_format($held, 0, ',', '.').'.');
            }

            $refund = BillingPayment::create([
                'billing_id' => $locked->id,
                'type' => BillingPayment::TYPE_REFUND,
                'method' => $method,
                'tendered_amount' => $amount,
                'change_amount' => 0,
                'amount' => $amount,
                'paid_at' => now(),
                'paid_on' => $paidOn,
                'reason' => $reason,
                'recorded_by' => session('admin_email') ?: 'admin',
            ]);

            BillingAudit::record('refund_recorded', $locked, null, ['amount' => $amount, 'method' => $method, 'paid_on' => $paidOn], $reason);

            return $refund;
        });
    }

    /**
     * Pindahkan kredit dari invoice void ke invoice pengganti. Non-kas. Hanya sebesar saldo tertahan
     * dan sisa tagihan invoice pengganti; setelah dipindahkan, saldo tertahan berkurang sehingga
     * tidak dapat dipindahkan atau direfund dua kali.
     *
     * @return float jumlah yang dipindahkan
     */
    public function transferCredit(Billing $from, Billing $to): float
    {
        return DB::transaction(function () use ($from, $to) {
            // Kunci kedua invoice dengan urutan id yang sama agar tidak deadlock.
            $ids = [$from->id, $to->id];
            sort($ids);
            $locked = Billing::lockForUpdate()->whereIn('id', $ids)->get()->keyBy('id');
            $source = $locked[$from->id];
            $target = $locked[$to->id];

            if (! $source->is_ledger || ! $target->is_ledger || ($source->payment_status !== 'void' && ! $source->voided_at)) {
                return 0.0;
            }

            $transfer = round(min(self::heldOnVoid($source), self::outstanding($target)), 2);

            if ($transfer <= 0) {
                return 0.0;
            }

            $common = ['tendered_amount' => $transfer, 'change_amount' => 0, 'amount' => $transfer, 'paid_at' => now(), 'paid_on' => ClinicTime::today(), 'recorded_by' => session('admin_email') ?: 'admin'];

            BillingPayment::create($common + ['billing_id' => $source->id, 'type' => BillingPayment::TYPE_CREDIT_OUT, 'linked_billing_id' => $target->id, 'reason' => 'Kredit dipindahkan ke invoice pengganti']);
            BillingPayment::create($common + ['billing_id' => $target->id, 'type' => BillingPayment::TYPE_CREDIT_IN, 'linked_billing_id' => $source->id, 'reason' => 'Kredit dari invoice yang digantikan']);

            $this->sync($target);

            BillingAudit::record('credit_transferred', $source, null, ['amount' => $transfer, 'to_billing_id' => $target->id]);
            BillingAudit::record('credit_received', $target, null, ['amount' => $transfer, 'from_billing_id' => $source->id]);

            return $transfer;
        });
    }

    /** Selaraskan kolom turunan pada invoice ledger (bukan void) dari baris pembayaran. */
    public function sync(Billing $billing): void
    {
        if (! $billing->is_ledger || $billing->payment_status === 'void' || $billing->voided_at) {
            return;
        }

        $paid = max(self::paidTotal($billing), 0.0);
        $amount = (float) $billing->amount;

        $billing->paid_amount = $paid;
        $billing->remaining_amount = round(max($amount - $paid, 0), 2);
        $billing->change_amount = round((float) BillingPayment::where('billing_id', $billing->id)->where('type', BillingPayment::TYPE_PAYMENT)->sum('change_amount'), 2);
        $billing->payment_status = $paid <= 0 ? 'unpaid' : ($paid < $amount ? 'partial' : 'paid');
        $billing->save();
    }
}
