<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (!Schema::hasColumn('billings', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 12, 2)->default(0)->after('invoice_date');
            }

            if (!Schema::hasColumn('billings', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('subtotal_amount');
            }

            if (!Schema::hasColumn('billings', 'discount_value')) {
                $table->decimal('discount_value', 12, 2)->default(0)->after('discount_type');
            }

            if (!Schema::hasColumn('billings', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_value');
            }

            if (!Schema::hasColumn('billings', 'promo_code')) {
                $table->string('promo_code')->nullable()->after('discount_amount');
            }

            if (!Schema::hasColumn('billings', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0)->after('amount');
            }

            if (!Schema::hasColumn('billings', 'change_amount')) {
                $table->decimal('change_amount', 12, 2)->default(0)->after('paid_amount');
            }

            if (!Schema::hasColumn('billings', 'remaining_amount')) {
                $table->decimal('remaining_amount', 12, 2)->default(0)->after('change_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            foreach ([
                'subtotal_amount',
                'discount_type',
                'discount_value',
                'discount_amount',
                'promo_code',
                'paid_amount',
                'change_amount',
                'remaining_amount',
            ] as $column) {
                if (Schema::hasColumn('billings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
