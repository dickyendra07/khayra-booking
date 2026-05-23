<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'goal_phase')) {
                $table->string('goal_phase')->nullable()->after('patient_goal');
            }

            if (!Schema::hasColumn('medical_records', 'phase_1_goal')) {
                $table->text('phase_1_goal')->nullable()->after('goal_phase');
            }

            if (!Schema::hasColumn('medical_records', 'phase_2_goal')) {
                $table->text('phase_2_goal')->nullable()->after('phase_1_goal');
            }

            if (!Schema::hasColumn('medical_records', 'phase_3_goal')) {
                $table->text('phase_3_goal')->nullable()->after('phase_2_goal');
            }

            if (!Schema::hasColumn('medical_records', 'session_focus')) {
                $table->text('session_focus')->nullable()->after('next_session_plan');
            }

            if (!Schema::hasColumn('medical_records', 'session_progress_note')) {
                $table->text('session_progress_note')->nullable()->after('session_focus');
            }

            if (!Schema::hasColumn('medical_records', 'session_pain_after')) {
                $table->unsignedTinyInteger('session_pain_after')->nullable()->after('session_progress_note');
            }

            if (!Schema::hasColumn('medical_records', 'session_homework_status')) {
                $table->string('session_homework_status')->nullable()->after('session_pain_after');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            foreach ([
                'goal_phase',
                'phase_1_goal',
                'phase_2_goal',
                'phase_3_goal',
                'session_focus',
                'session_progress_note',
                'session_pain_after',
                'session_homework_status',
            ] as $column) {
                if (Schema::hasColumn('medical_records', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
