<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'pain_body_area')) {
                $table->string('pain_body_area')->nullable()->after('pain_body_chart_note');
            }

            if (!Schema::hasColumn('medical_records', 'pain_body_side')) {
                $table->string('pain_body_side')->nullable()->after('pain_body_area');
            }

            if (!Schema::hasColumn('medical_records', 'pain_body_type')) {
                $table->string('pain_body_type')->nullable()->after('pain_body_side');
            }

            if (!Schema::hasColumn('medical_records', 'pain_body_intensity')) {
                $table->unsignedTinyInteger('pain_body_intensity')->nullable()->after('pain_body_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            foreach (['pain_body_area', 'pain_body_side', 'pain_body_type', 'pain_body_intensity'] as $column) {
                if (Schema::hasColumn('medical_records', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
