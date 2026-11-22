<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. Gerbang sinkronisasi per periode (Y-m) untuk tutup buku: setiap transaksi yang dapat mengubah angka sebuah
 * periode mengambil kunci BERSAMA (FOR SHARE) pada baris periodenya di dalam transaction-nya, sedangkan tutup buku dan
 * buka-kembali mengambil kunci EKSKLUSIF. Baris dibuat on-demand (tidak ada data historis yang diubah) dan tidak memuat
 * data keuangan. Lihat App\Services\FinancePeriodLock.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('finance_period_locks')) {
            return;
        }

        Schema::create('finance_period_locks', function (Blueprint $table) {
            $table->string('month', 7)->primary();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_period_locks');
    }
};
