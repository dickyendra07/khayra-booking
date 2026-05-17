<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralLetter extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_id',
        'letter_number',
        'letter_date',
        'referral_to',
        'referral_reason',
        'clinical_summary',
        'recommendation',
        'notes',
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
