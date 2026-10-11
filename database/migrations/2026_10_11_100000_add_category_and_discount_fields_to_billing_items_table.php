<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive only. Existing billing_items rows keep NULL / 0 in the new columns,
 * so historical invoices are neither recalculated nor reinterpreted.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            if (! Schema::hasColumn('billing_items', 'item_category')) {
                $table->string('item_category', 30)->nullable()->after('item_type')->index();
            }

            if (! Schema::hasColumn('billing_items', 'is_discount_eligible')) {
                $table->boolean('is_discount_eligible')->default(false)->after('line_total');
            }

            if (! Schema::hasColumn('billing_items', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('is_discount_eligible');
            }

            if (! Schema::hasColumn('billing_items', 'clinic_service_id')) {
                $table->unsignedBigInteger('clinic_service_id')->nullable()->after('inventory_item_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('billing_items', function (Blueprint $table) {
            foreach (['item_category', 'is_discount_eligible', 'discount_amount', 'clinic_service_id'] as $column) {
                if (Schema::hasColumn('billing_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
