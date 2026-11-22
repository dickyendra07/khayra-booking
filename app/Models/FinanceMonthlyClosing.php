<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceMonthlyClosing extends Model
{
    protected $fillable = [
        'month',
        'period_start',
        'period_end',
        'billing_income',
        'manual_income',
        'total_income',
        'total_expense',
        'net_cashflow',
        'transaction_count',
        'closed_by',
        'closed_at',
        'notes',
        'snapshot',
        'reopened_at',
        'reopened_by',
        'reopen_reason',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'billing_income' => 'decimal:2',
        'manual_income' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'net_cashflow' => 'decimal:2',
        'transaction_count' => 'integer',
        'closed_at' => 'datetime',
        'snapshot' => 'array',
        'reopened_at' => 'datetime',
    ];

    /** Closing yang masih berlaku (belum dibuka kembali). */
    public function scopeActive($query)
    {
        return $query->whereNull('reopened_at');
    }

    public static function isClosed(string $month): bool
    {
        return static::active()->where('month', $month)->exists();
    }
}
