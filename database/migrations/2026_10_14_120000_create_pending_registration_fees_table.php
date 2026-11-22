<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. Kewajiban biaya administrasi pasien baru yang BELUM dapat ditagihkan karena periode berjalan sudah tutup
 * buku saat registrasi. Dibuat dalam transaction yang sama dengan pasien; satu baris per pasien (unique index), sehingga
 * kewajiban tidak hilang dan tidak ganda. Diterbitkan menjadi invoice (tanggal hari penerbitan, tidak pernah mundur)
 * lewat daftar "Biaya Admin Tertunda". Tidak mengubah data lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pending_registration_fees')) {
            return;
        }

        Schema::create('pending_registration_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('status', 10)->default('pending')->index();      // pending | issued
            $table->string('closed_month', 7);                              // periode tertutup yang menghalangi (Y-m)
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('billing_id')->nullable()->index();
            $table->string('created_by')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->string('issued_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registration_fees');
    }
};
