<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'nik')) {
                $table->string('nik')->nullable()->after('full_name');
            }

            if (!Schema::hasColumn('patients', 'religion')) {
                $table->string('religion')->nullable()->after('address');
            }

            if (!Schema::hasColumn('patients', 'occupation')) {
                $table->string('occupation')->nullable()->after('religion');
            }

            if (!Schema::hasColumn('patients', 'education')) {
                $table->string('education')->nullable()->after('occupation');
            }

            if (!Schema::hasColumn('patients', 'marital_status')) {
                $table->string('marital_status')->nullable()->after('education');
            }

            if (!Schema::hasColumn('patients', 'medical_record_number')) {
                $table->string('medical_record_number')->nullable()->unique()->after('marital_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $columns = [
                'nik',
                'religion',
                'occupation',
                'education',
                'marital_status',
                'medical_record_number',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('patients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};