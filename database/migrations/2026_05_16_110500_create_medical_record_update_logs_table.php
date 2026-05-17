<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('medical_record_update_logs')) {
            Schema::create('medical_record_update_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('medical_record_id')->nullable()->index();
                $table->unsignedBigInteger('visit_id')->nullable()->index();
                $table->unsignedBigInteger('patient_id')->nullable()->index();
                $table->unsignedBigInteger('therapist_id')->nullable()->index();
                $table->string('updated_by_name')->nullable();
                $table->timestamp('snapshot_date')->nullable();

                $table->text('complaint')->nullable();
                $table->unsignedTinyInteger('pain_scale')->nullable();
                $table->text('assessment')->nullable();
                $table->text('treatment_given')->nullable();
                $table->text('response_to_treatment')->nullable();
                $table->text('next_session_plan')->nullable();

                $table->date('date_of_control')->nullable();
                $table->string('frequency_per_week')->nullable();
                $table->unsignedInteger('total_session')->nullable();
                $table->text('control_plan')->nullable();

                $table->text('summary')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_record_update_logs');
    }
};
