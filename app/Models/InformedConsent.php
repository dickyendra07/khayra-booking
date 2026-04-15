<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformedConsent extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'consent_date',
        'status',
        'physiotherapy_name',
        'treatment_location',
        'representative_name',
        'relationship_to_patient',
        'agreement_text',
        'notes',
    ];

    protected $casts = [
        'consent_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}