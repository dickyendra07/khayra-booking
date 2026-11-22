<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Buku pembayaran (append-only). Tabel baru; tidak mengubah data invoice yang ada.
 *
 * type: payment (kas masuk), refund (kas keluar), credit_out / credit_in (pemindahan kredit antar
 * invoice, BUKAN pergerakan kas).
 * amount = penerimaan bersih (tendered - change). paid_at = UTC; paid_on = tanggal bisnis klinik (WIB).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('billing_payments')) {
            return;
        }

        Schema::create('billing_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billing_id')->index();
            $table->string('type', 20)->index();
            $table->string('method', 30)->nullable();
            $table->decimal('tendered_amount', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->date('paid_on')->index();
            $table->string('reference')->nullable();
            $table->text('reason')->nullable();
            $table->string('recorded_by')->nullable();
            $table->unsignedBigInteger('linked_billing_id')->nullable();
            $table->unsignedBigInteger('reverses_payment_id')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_payments');
    }
};
