<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migration ini dan create_therapists memiliki timestamp yang sama; urutan alfabet membuat
        // file ini berjalan lebih dulu. Di database baru (MySQL) tabel therapists belum ada sehingga
        // foreign key gagal. Pada kasus itu kolom dibuat oleh create_therapists. Database yang sudah
        // ada tidak terpengaruh (migration ini sudah tercatat selesai).
        if (Schema::hasColumn('visits', 'therapist_id') || !Schema::hasTable('therapists')) {
            return;
        }

        Schema::table('visits', function (Blueprint $table) {
            $table->foreignId('therapist_id')->nullable()->after('patient_id')->constrained('therapists')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('therapist_id');
        });
    }
};