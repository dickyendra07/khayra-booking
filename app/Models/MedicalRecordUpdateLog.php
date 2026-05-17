<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordUpdateLog extends Model
{
    protected $fillable = [
        'medical_record_id',
        'visit_id',
        'patient_id',
        'therapist_id',
        'updated_by_name',
        'snapshot_date',
        'complaint',
        'pain_scale',
        'assessment',
        'treatment_given',
        'response_to_treatment',
        'next_session_plan',
        'date_of_control',
        'frequency_per_week',
        'total_session',
        'control_plan',
        'summary',
    ];

    protected $casts = [
        'snapshot_date' => 'datetime',
        'date_of_control' => 'date',
        'pain_scale' => 'integer',
        'total_session' => 'integer',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }
}
