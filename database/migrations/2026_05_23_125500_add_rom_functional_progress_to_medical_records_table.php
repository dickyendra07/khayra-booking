<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'rom_cervical_rotation')) {
                $table->string('rom_cervical_rotation')->nullable()->after('session_homework_status');
            }

            if (!Schema::hasColumn('medical_records', 'rom_shoulder_elevation')) {
                $table->string('rom_shoulder_elevation')->nullable()->after('rom_cervical_rotation');
            }

            if (!Schema::hasColumn('medical_records', 'functional_score')) {
                $table->unsignedTinyInteger('functional_score')->nullable()->after('rom_shoulder_elevation');
            }

            if (!Schema::hasColumn('medical_records', 'activity_tolerance')) {
                $table->string('activity_tolerance')->nullable()->after('functional_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            foreach ([
                'rom_cervical_rotation',
                'rom_shoulder_elevation',
                'functional_score',
                'activity_tolerance',
            ] as $column) {
                if (Schema::hasColumn('medical_records', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
