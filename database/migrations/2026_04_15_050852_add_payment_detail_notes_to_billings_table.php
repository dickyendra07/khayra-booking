<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (!Schema::hasColumn('billings', 'payment_detail_notes')) {
                $table->text('payment_detail_notes')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (Schema::hasColumn('billings', 'payment_detail_notes')) {
                $table->dropColumn('payment_detail_notes');
            }
        });
    }
};