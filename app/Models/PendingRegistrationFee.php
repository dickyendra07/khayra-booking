<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kewajiban biaya administrasi pasien baru yang tertunda (periode tutup buku saat registrasi).
 * Satu baris per pasien (unique patient_id). Baris tidak pernah dihapus: status berubah pending -> issued.
 */
class PendingRegistrationFee extends Model
{
    public const PENDING = 'pending';

    public const ISSUED = 'issued';

    protected $fillable = ['patient_id', 'amount', 'status', 'closed_month', 'reason', 'billing_id', 'created_by', 'issued_at', 'issued_by'];

    protected $casts = ['amount' => 'decimal:2', 'issued_at' => 'datetime'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }
}
