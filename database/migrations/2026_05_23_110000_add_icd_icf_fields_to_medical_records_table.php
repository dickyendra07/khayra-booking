<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'icd_code')) {
                $table->string('icd_code')->nullable()->after('physiotherapy_diagnosis');
            }

            if (!Schema::hasColumn('medical_records', 'icd_diagnosis')) {
                $table->string('icd_diagnosis')->nullable()->after('icd_code');
            }

            if (!Schema::hasColumn('medical_records', 'icf_body_function')) {
                $table->text('icf_body_function')->nullable()->after('functional_limitation_clinical');
            }

            if (!Schema::hasColumn('medical_records', 'icf_body_structure')) {
                $table->text('icf_body_structure')->nullable()->after('icf_body_function');
            }

            if (!Schema::hasColumn('medical_records', 'icf_activities_participation')) {
                $table->text('icf_activities_participation')->nullable()->after('icf_body_structure');
            }

            if (!Schema::hasColumn('medical_records', 'icf_personal_factors')) {
                $table->text('icf_personal_factors')->nullable()->after('icf_activities_participation');
            }

            if (!Schema::hasColumn('medical_records', 'icf_environmental_factors')) {
                $table->text('icf_environmental_factors')->nullable()->after('icf_personal_factors');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            foreach ([
                'icd_code',
                'icd_diagnosis',
                'icf_body_function',
                'icf_body_structure',
                'icf_activities_participation',
                'icf_personal_factors',
                'icf_environmental_factors',
            ] as $column) {
                if (Schema::hasColumn('medical_records', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
