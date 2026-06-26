<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_monthly_closings', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7)->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('billing_income', 15, 2)->default(0);
            $table->decimal('manual_income', 15, 2)->default(0);
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('net_cashflow', 15, 2)->default(0);
            $table->unsignedInteger('transaction_count')->default(0);
            $table->string('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_monthly_closings');
    }
};
