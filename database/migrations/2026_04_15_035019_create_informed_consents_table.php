<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informed_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();

            $table->date('consent_date')->nullable();
            $table->string('status')->default('pending');

            $table->string('physiotherapy_name')->nullable();
            $table->string('treatment_location')->nullable();

            $table->string('representative_name')->nullable();
            $table->string('relationship_to_patient')->nullable();

            $table->text('agreement_text')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informed_consents');
    }
};