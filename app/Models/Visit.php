<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'patient_id',
        'booking_id',
        'therapist_id',
        'visit_date',
        'therapist',
        'notes',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function therapistRelation()
    {
        return $this->belongsTo(Therapist::class, 'therapist_id');
    }
}