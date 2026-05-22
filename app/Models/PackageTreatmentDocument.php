<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageTreatmentDocument extends Model
{
    protected $fillable = [
        'patient_id',
        'billing_id',
        'therapist_id',
        'document_number',
        'document_date',
        'package_name',
        'package_type',
        'total_sessions',
        'package_price',
        'buying_date',
        'valid_until',
        'terms',
        'notes',
    ];

    protected $casts = [
        'document_date' => 'date',
        'buying_date' => 'date',
        'valid_until' => 'date',
        'package_price' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }
}
