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
        'pain_body_intensity',
        'pain_body_type',
        'pain_body_side',
        'pain_body_area',
        'pain_easing_activity',
        'pain_aggravating_activity',
        'pain_quality_tags',
        'pain_body_areas',

        'subjective_examination',
        'objective_examination',
        'severity_level',
        'irritability_level',
        'nature_type',
        'easing_factors',
        'aggravating_factors',
        'special_test_notes',

        'physiotherapy_diagnosis',
        'icf_environmental_factors',
        'icf_personal_factors',
        'icf_activities_participation',
        'icf_body_structure',
        'icf_body_function',
        'icd_diagnosis',
        'icd_code',
        'impairment',
        'functional_limitation_clinical',
        'patient_goal',
        'phase_3_goal',
        'phase_2_goal',
        'phase_1_goal',
        'goal_phase',
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
        'session_homework_status',
        'activity_tolerance',
        'functional_score',
        'rom_shoulder_elevation',
        'rom_cervical_rotation',
        'session_pain_after',
        'session_progress_note',
        'session_focus',

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