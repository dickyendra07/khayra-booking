<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('id');
            $table->date('invoice_date')->nullable()->after('visit_id');
            $table->string('payment_method')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number',
                'invoice_date',
                'payment_method',
            ]);
        });
    }
};