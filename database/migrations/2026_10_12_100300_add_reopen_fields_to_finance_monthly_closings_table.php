<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. Baris closing lama tidak berubah (kolom baru NULL = masih tertutup).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_monthly_closings', function (Blueprint $table) {
            if (! Schema::hasColumn('finance_monthly_closings', 'reopened_at')) {
                $table->timestamp('reopened_at')->nullable();
            }

            if (! Schema::hasColumn('finance_monthly_closings', 'reopened_by')) {
                $table->string('reopened_by')->nullable();
            }

            if (! Schema::hasColumn('finance_monthly_closings', 'reopen_reason')) {
                $table->text('reopen_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('finance_monthly_closings', function (Blueprint $table) {
            foreach (['reopened_at', 'reopened_by', 'reopen_reason'] as $column) {
                if (Schema::hasColumn('finance_monthly_closings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
