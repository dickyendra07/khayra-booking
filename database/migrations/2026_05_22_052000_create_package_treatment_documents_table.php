<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_treatment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('billing_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('therapist_id')->nullable()->constrained()->nullOnDelete();
            $table->string('document_number')->nullable()->index();
            $table->date('document_date')->nullable();
            $table->string('package_name')->nullable();
            $table->string('package_type')->nullable();
            $table->unsignedInteger('total_sessions')->default(3);
            $table->decimal('package_price', 15, 2)->default(0);
            $table->date('buying_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_treatment_documents');
    }
};
