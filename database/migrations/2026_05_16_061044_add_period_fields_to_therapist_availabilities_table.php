<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('therapist_availabilities', function (Blueprint $table) {
            if (!Schema::hasColumn('therapist_availabilities', 'valid_from')) {
                $table->date('valid_from')->nullable()->after('therapist_id');
            }

            if (!Schema::hasColumn('therapist_availabilities', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('valid_from');
            }
        });
    }

    public function down(): void
    {
        Schema::table('therapist_availabilities', function (Blueprint $table) {
            if (Schema::hasColumn('therapist_availabilities', 'valid_from')) {
                $table->dropColumn('valid_from');
            }

            if (Schema::hasColumn('therapist_availabilities', 'valid_until')) {
                $table->dropColumn('valid_until');
            }
        });
    }
};
