<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'invoice_number',
        'invoice_date',
        'amount',
        'payment_status',
        'payment_method',
        'payment_detail_notes',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
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