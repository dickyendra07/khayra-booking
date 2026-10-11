<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. payment_model: 'legacy' untuk SEMUA invoice yang sudah ada (default), 'ledger' untuk
 * invoice baru yang pembayarannya dicatat di billing_payments. Invoice legacy tidak pernah dihitung
 * ulang. checkout_token: kunci anti-klik-ganda pada checkout (unik, boleh kosong).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (! Schema::hasColumn('billings', 'payment_model')) {
                $table->string('payment_model', 10)->default('legacy');
            }

            if (! Schema::hasColumn('billings', 'checkout_token')) {
                $table->string('checkout_token', 64)->nullable()->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (Schema::hasColumn('billings', 'checkout_token')) {
                $table->dropUnique(['checkout_token']);
                $table->dropColumn('checkout_token');
            }

            if (Schema::hasColumn('billings', 'payment_model')) {
                $table->dropColumn('payment_model');
            }
        });
    }
};
