<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'type',
        'source',
        'category',
        'title',
        'amount',
        'payment_method',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'income' => 'Income',
            'expense' => 'Expense',
            default => $this->type ? ucwords(str_replace('_', ' ', $this->type)) : '-',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'manual' => 'Manual Entry',
            'owner_capital' => 'Owner Capital',
            'other_income' => 'Other Income',
            'operational' => 'Operational',
            'salary' => 'Salary',
            'rent' => 'Rent',
            'utility' => 'Utility',
            'equipment' => 'Equipment',
            'consumable' => 'Consumable',
            'marketing' => 'Marketing',
            'maintenance' => 'Maintenance',
            'adjustment' => 'Adjustment',
            default => $this->source ? ucwords(str_replace('_', ' ', $this->source)) : '-',
        };
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
            'transfer' => 'Transfer',
            default => $this->payment_method ? ucwords(str_replace('_', ' ', $this->payment_method)) : '-',
        };
    }
}
