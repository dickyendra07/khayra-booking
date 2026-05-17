<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Billing extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'invoice_number',
        'invoice_date',
        'amount',
        'remaining_amount',
        'original_payment_status',
        'void_reason',
        'voided_at',
        'change_amount',
        'paid_amount',
        'promo_code',
        'discount_amount',
        'discount_value',
        'discount_type',
        'subtotal_amount',
        'payment_status',
        'payment_method',
        'payment_detail_notes',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'voided_at' => 'datetime',
    ];


    public function items(): HasMany
    {
        return $this->hasMany(BillingItem::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function getIsVoidedAttribute(): bool
    {
        return !is_null($this->voided_at) || $this->payment_status === 'void';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'qr' => 'QR',
            'debit' => 'Debit',
            'credit' => 'Credit Card',
            'bank_bca' => 'Bank BCA',
            'bank_bni' => 'Bank BNI',
            'bank_mandiri' => 'Bank Mandiri',
            'insurance' => 'Insurance',
            default => $this->payment_method ? ucwords(str_replace('_', ' ', $this->payment_method)) : '-',
        };
    }
}