<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive: invoice pengganti (koreksi) menunjuk invoice void yang digantikannya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (! Schema::hasColumn('billings', 'replaces_billing_id')) {
                $table->unsignedBigInteger('replaces_billing_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (Schema::hasColumn('billings', 'replaces_billing_id')) {
                // Lepas index lebih dulu agar aman di SQLite maupun MySQL/MariaDB.
                $table->dropIndex(['replaces_billing_id']);
                $table->dropColumn('replaces_billing_id');
            }
        });
    }
};
