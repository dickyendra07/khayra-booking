<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. reference_type/reference_id menautkan baris tagihan ke sumbernya (mis. biaya ruangan
 * untuk visit, biaya admin untuk pasien) sebagai kunci pencegah duplikasi; rate_unit menyimpan
 * snapshot satuan tarif saat transaksi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            if (! Schema::hasColumn('billing_items', 'reference_type')) {
                $table->string('reference_type', 30)->nullable();
            }

            if (! Schema::hasColumn('billing_items', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable();
            }

            if (! Schema::hasColumn('billing_items', 'rate_unit')) {
                $table->string('rate_unit', 20)->nullable();
            }
        });

        Schema::table('billing_items', function (Blueprint $table) {
            if (! Schema::hasIndex('billing_items', 'billing_items_reference_index')) {
                $table->index(['reference_type', 'reference_id'], 'billing_items_reference_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            if (Schema::hasIndex('billing_items', 'billing_items_reference_index')) {
                $table->dropIndex('billing_items_reference_index');
            }
        });

        Schema::table('billing_items', function (Blueprint $table) {
            foreach (['reference_type', 'reference_id', 'rate_unit'] as $column) {
                if (Schema::hasColumn('billing_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
