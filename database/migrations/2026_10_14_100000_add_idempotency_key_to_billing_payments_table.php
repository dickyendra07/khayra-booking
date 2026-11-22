<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. idempotency_key: token satu-kali-pakai per pengiriman formulir pembayaran/refund. Unik di
 * level database (boleh kosong untuk baris lama dan baris sistem), sehingga pengiriman ulang request
 * yang sama (klik ganda, tombol back, retry jaringan) tidak pernah menghasilkan baris kedua.
 * Tidak mengubah baris yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('billing_payments', 'idempotency_key')) {
                $table->string('idempotency_key', 64)->nullable();
            }
        });

        Schema::table('billing_payments', function (Blueprint $table) {
            if (! Schema::hasIndex('billing_payments', 'billing_payments_idempotency_key_unique')) {
                $table->unique('idempotency_key', 'billing_payments_idempotency_key_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('billing_payments', function (Blueprint $table) {
            if (Schema::hasIndex('billing_payments', 'billing_payments_idempotency_key_unique')) {
                $table->dropUnique('billing_payments_idempotency_key_unique');
            }
        });

        Schema::table('billing_payments', function (Blueprint $table) {
            if (Schema::hasColumn('billing_payments', 'idempotency_key')) {
                $table->dropColumn('idempotency_key');
            }
        });
    }
};
