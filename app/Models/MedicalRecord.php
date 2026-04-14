<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'visit_id',
        'created_by_therapist_id',
        'updated_by_therapist_id',

        'complaint',
        'assessment',
        'treatment',
        'progress_note',
        'recommendation',

        'onset',
        'condition_felt',
        'pain_scale',
        'pain_type',
        'functional_limitation_initial',
        'pain_body_chart_note',

        'subjective_examination',
        'objective_examination',
        'severity_level',
        'irritability_level',
        'nature_type',
        'easing_factors',
        'aggravating_factors',
        'special_test_notes',

        'physiotherapy_diagnosis',
        'impairment',
        'functional_limitation_clinical',
        'patient_goal',
        'referral',

        'program_patient',
        'date_of_control',
        'total_session',
        'frequency_per_week',
        'control_plan',

        'diet_nutrition',
        'lifestyle',
        'flare_up_management',

        'treatment_given',
        'response_to_treatment',
        'next_session_plan',

        'blood_pressure',
        'temperature',
        'respiration_rate',
        'heart_rate',
        'weight',
        'height',
        'bmi',
    ];

    protected $casts = [
        'date_of_control' => 'date',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function creatorTherapist()
    {
        return $this->belongsTo(Therapist::class, 'created_by_therapist_id');
    }

    public function updaterTherapist()
    {
        return $this->belongsTo(Therapist::class, 'updated_by_therapist_id');
    }

    public function histories()
    {
        return $this->hasMany(MedicalRecordHistory::class);
    }

    public function comorbidities()
    {
        return $this->hasMany(MedicalRecordComorbidity::class);
    }

    public function supportingData()
    {
        return $this->hasMany(MedicalRecordSupportingData::class);
    }

    public function homeExercises()
    {
        return $this->hasMany(MedicalRecordHomeExercise::class);
    }
}