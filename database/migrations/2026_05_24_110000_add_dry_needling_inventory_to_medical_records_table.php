<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'dry_needling_done')) {
                $table->boolean('dry_needling_done')->default(false)->after('treatment_given');
            }

            if (!Schema::hasColumn('medical_records', 'dry_needling_inventory_item_id')) {
                $table->foreignId('dry_needling_inventory_item_id')
                    ->nullable()
                    ->after('dry_needling_done')
                    ->constrained('inventory_items')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('medical_records', 'dry_needling_quantity')) {
                $table->unsignedInteger('dry_needling_quantity')->nullable()->after('dry_needling_inventory_item_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (Schema::hasColumn('medical_records', 'dry_needling_inventory_item_id')) {
                $table->dropConstrainedForeignId('dry_needling_inventory_item_id');
            }

            foreach (['dry_needling_done', 'dry_needling_quantity'] as $column) {
                if (Schema::hasColumn('medical_records', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
