<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'therapist_id')) {
                $table->foreignId('therapist_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('therapists')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'therapist_id')) {
                $table->dropConstrainedForeignId('therapist_id');
            }
        });
    }
};
