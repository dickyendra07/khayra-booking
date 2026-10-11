<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel baru, append-only. Dipakai untuk jejak audit perubahan invoice dan
 * tutup/buka buku bulanan (billing_id kosong, finance_month terisi).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('billing_audit_logs')) {
            return;
        }

        Schema::create('billing_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billing_id')->nullable()->index();
            $table->string('finance_month', 7)->nullable()->index();
            $table->string('action', 40)->index();
            $table->string('actor')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('reason')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_audit_logs');
    }
};
