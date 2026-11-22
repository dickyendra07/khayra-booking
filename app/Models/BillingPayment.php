<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Baris buku pembayaran. Append-only: koreksi dilakukan dengan baris baru (refund / kredit).
 */
class BillingPayment extends Model
{
    public const UPDATED_AT = null;

    public const TYPE_PAYMENT = 'payment';

    public const TYPE_REFUND = 'refund';

    public const TYPE_CREDIT_IN = 'credit_in';

    public const TYPE_CREDIT_OUT = 'credit_out';

    protected $fillable = [
        'billing_id', 'type', 'method', 'tendered_amount', 'change_amount', 'amount',
        'paid_at', 'paid_on', 'reference', 'reason', 'recorded_by', 'linked_billing_id', 'reverses_payment_id', 'idempotency_key',
    ];

    protected $casts = [
        'tendered_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'paid_on' => 'date',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Catatan pembayaran tidak boleh diubah.'));
        static::deleting(fn () => throw new LogicException('Catatan pembayaran tidak boleh dihapus.'));
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PAYMENT => 'Pembayaran',
            self::TYPE_REFUND => 'Refund',
            self::TYPE_CREDIT_IN => 'Kredit masuk (dari invoice lama)',
            self::TYPE_CREDIT_OUT => 'Kredit keluar (ke invoice pengganti)',
            default => (string) $this->type,
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash' => 'Cash', 'qr' => 'QR', 'debit' => 'Debit', 'credit' => 'Credit Card',
            'bank_bca' => 'Bank BCA', 'bank_bni' => 'Bank BNI', 'bank_mandiri' => 'Bank Mandiri', 'insurance' => 'Insurance',
            default => $this->method ? ucwords(str_replace('_', ' ', $this->method)) : '-',
        };
    }

    /** Apakah baris ini pergerakan kas (bukan pemindahan kredit antar invoice). */
    public function getIsCashAttribute(): bool
    {
        return in_array($this->type, [self::TYPE_PAYMENT, self::TYPE_REFUND], true);
    }
}
