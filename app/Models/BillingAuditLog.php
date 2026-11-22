<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * Jejak audit append-only: tidak boleh diubah atau dihapus lewat aplikasi.
 */
class BillingAuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'billing_id',
        'finance_month',
        'action',
        'actor',
        'ip_address',
        'reason',
        'before',
        'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Audit log tidak boleh diubah.'));
        static::deleting(fn () => throw new LogicException('Audit log tidak boleh dihapus.'));
    }
}
