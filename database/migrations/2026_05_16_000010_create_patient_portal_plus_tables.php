<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('home_exercise_templates')) {
            Schema::create('home_exercise_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category')->nullable();
                $table->string('target_area')->nullable();
                $table->string('difficulty')->default('easy');
                $table->text('instructions');
                $table->string('dosage')->nullable();
                $table->string('video_url')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('patient_progress_entries')) {
            Schema::create('patient_progress_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('visit_id')->nullable();
                $table->date('entry_date');
                $table->integer('pain_scale')->nullable();
                $table->text('rom_notes')->nullable();
                $table->text('functional_goal')->nullable();
                $table->text('progress_notes')->nullable();
                $table->timestamps();

                $table->index('patient_id');
                $table->index('visit_id');
                $table->index('entry_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_progress_entries');
        Schema::dropIfExists('home_exercise_templates');
    }
};
