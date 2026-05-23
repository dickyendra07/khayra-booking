<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'pain_body_areas')) {
                $table->text('pain_body_areas')->nullable()->after('pain_body_area');
            }

            if (!Schema::hasColumn('medical_records', 'pain_quality_tags')) {
                $table->text('pain_quality_tags')->nullable()->after('pain_body_type');
            }

            if (!Schema::hasColumn('medical_records', 'pain_aggravating_activity')) {
                $table->text('pain_aggravating_activity')->nullable()->after('pain_body_intensity');
            }

            if (!Schema::hasColumn('medical_records', 'pain_easing_activity')) {
                $table->text('pain_easing_activity')->nullable()->after('pain_aggravating_activity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            foreach ([
                'pain_body_areas',
                'pain_quality_tags',
                'pain_aggravating_activity',
                'pain_easing_activity',
            ] as $column) {
                if (Schema::hasColumn('medical_records', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
