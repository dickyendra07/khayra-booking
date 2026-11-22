<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapists', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('whatsapp')->nullable();
            $table->string('specialty')->nullable();
            $table->string('password');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Lihat catatan di add_therapist_id_to_visits_table: pada database baru kolom ini dibuat di sini.
        if (!Schema::hasColumn('visits', 'therapist_id')) {
            Schema::table('visits', function (Blueprint $table) {
                $table->foreignId('therapist_id')->nullable()->after('patient_id')->constrained('therapists')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('therapists');
    }
};