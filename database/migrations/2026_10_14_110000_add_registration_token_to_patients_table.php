<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. registration_token: token satu-kali-pakai per pengiriman registrasi pasien. Unik di level
 * database (boleh kosong untuk seluruh pasien yang sudah ada), sehingga registrasi ganda (klik ganda,
 * Back, retry) tidak membuat pasien kedua maupun tagihan administrasi kedua. Tidak mengubah baris lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (! Schema::hasColumn('patients', 'registration_token')) {
                $table->string('registration_token', 64)->nullable();
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            if (! Schema::hasIndex('patients', 'patients_registration_token_unique')) {
                $table->unique('registration_token', 'patients_registration_token_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasIndex('patients', 'patients_registration_token_unique')) {
                $table->dropUnique('patients_registration_token_unique');
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'registration_token')) {
                $table->dropColumn('registration_token');
            }
        });
    }
};
