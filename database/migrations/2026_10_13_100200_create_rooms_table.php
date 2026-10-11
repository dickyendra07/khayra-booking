<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Master ruangan. Semua ruangan DEFAULT non-billable dan tanpa tarif: tidak ada penagihan
 * sebelum klinik menyetujui tarif dan satuannya. Nama ruangan yang sudah dipakai pada
 * bookings/visits tetap berupa teks (tidak diubah).
 */
return new class extends Migration
{
    private const DEFAULT_ROOMS = ['VIP 1', 'VIP 2', 'R.1.1', 'R.2.1', 'R.2.2', 'R.2.3', 'R.2.4', 'R.2.5', 'R. GYM', 'R. Studio Pilates'];

    public function up(): void
    {
        if (! Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('status', 10)->default('active');
                $table->boolean('is_billable')->default(false);
                $table->decimal('rate', 12, 2)->nullable();
                $table->string('rate_unit', 20)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        foreach (self::DEFAULT_ROOMS as $name) {
            if (! DB::table('rooms')->where('name', $name)->exists()) {
                DB::table('rooms')->insert(['name' => $name, 'status' => 'active', 'is_billable' => false, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
