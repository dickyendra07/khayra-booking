<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('promos')) {
            Schema::create('promos', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('discount_type')->default('nominal');
                $table->decimal('discount_value', 12, 2)->default(0);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->decimal('minimum_purchase', 12, 2)->default(0);
                $table->decimal('maximum_discount', 12, 2)->default(0);
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('billings', function (Blueprint $table) {
            if (!Schema::hasColumn('billings', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('remaining_amount');
            }

            if (!Schema::hasColumn('billings', 'void_reason')) {
                $table->text('void_reason')->nullable()->after('voided_at');
            }

            if (!Schema::hasColumn('billings', 'original_payment_status')) {
                $table->string('original_payment_status')->nullable()->after('void_reason');
            }
        });

        Schema::table('inventory_stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_stock_movements', 'billing_id')) {
                $table->unsignedBigInteger('billing_id')->nullable()->after('inventory_item_id');
            }

            if (!Schema::hasColumn('inventory_stock_movements', 'voided_billing_id')) {
                $table->unsignedBigInteger('voided_billing_id')->nullable()->after('billing_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_stock_movements', function (Blueprint $table) {
            foreach (['billing_id', 'voided_billing_id'] as $column) {
                if (Schema::hasColumn('inventory_stock_movements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('billings', function (Blueprint $table) {
            foreach (['voided_at', 'void_reason', 'original_payment_status'] as $column) {
                if (Schema::hasColumn('billings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('promos');
    }
};
