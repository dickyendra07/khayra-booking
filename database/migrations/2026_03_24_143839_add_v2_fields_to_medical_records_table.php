<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_therapist_id')->nullable()->after('visit_id');
            $table->unsignedBigInteger('updated_by_therapist_id')->nullable()->after('created_by_therapist_id');

            $table->string('onset')->nullable()->after('complaint');
            $table->text('condition_felt')->nullable()->after('onset');
            $table->unsignedTinyInteger('pain_scale')->nullable()->after('condition_felt');
            $table->string('pain_type')->nullable()->after('pain_scale');
            $table->text('functional_limitation_initial')->nullable()->after('pain_type');
            $table->text('pain_body_chart_note')->nullable()->after('functional_limitation_initial');

            $table->text('subjective_examination')->nullable()->after('pain_body_chart_note');
            $table->text('objective_examination')->nullable()->after('subjective_examination');
            $table->string('severity_level')->nullable()->after('objective_examination');
            $table->string('irritability_level')->nullable()->after('severity_level');
            $table->string('nature_type')->nullable()->after('irritability_level');
            $table->text('easing_factors')->nullable()->after('nature_type');
            $table->text('aggravating_factors')->nullable()->after('easing_factors');
            $table->text('special_test_notes')->nullable()->after('aggravating_factors');

            $table->text('physiotherapy_diagnosis')->nullable()->after('special_test_notes');
            $table->text('impairment')->nullable()->after('physiotherapy_diagnosis');
            $table->text('functional_limitation_clinical')->nullable()->after('impairment');
            $table->text('patient_goal')->nullable()->after('functional_limitation_clinical');
            $table->text('referral')->nullable()->after('patient_goal');

            $table->text('program_patient')->nullable()->after('referral');
            $table->date('date_of_control')->nullable()->after('program_patient');
            $table->unsignedInteger('total_session')->nullable()->after('date_of_control');
            $table->string('frequency_per_week')->nullable()->after('total_session');
            $table->text('control_plan')->nullable()->after('frequency_per_week');

            $table->text('diet_nutrition')->nullable()->after('control_plan');
            $table->text('lifestyle')->nullable()->after('diet_nutrition');
            $table->text('flare_up_management')->nullable()->after('lifestyle');

            $table->text('treatment_given')->nullable()->after('flare_up_management');
            $table->text('response_to_treatment')->nullable()->after('treatment_given');
            $table->text('next_session_plan')->nullable()->after('response_to_treatment');

            $table->string('blood_pressure')->nullable()->after('next_session_plan');
            $table->string('temperature')->nullable()->after('blood_pressure');
            $table->string('respiration_rate')->nullable()->after('temperature');
            $table->string('heart_rate')->nullable()->after('respiration_rate');
            $table->string('weight')->nullable()->after('heart_rate');
            $table->string('height')->nullable()->after('weight');
            $table->string('bmi')->nullable()->after('height');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn([
                'created_by_therapist_id',
                'updated_by_therapist_id',
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
            ]);
        });
    }
};