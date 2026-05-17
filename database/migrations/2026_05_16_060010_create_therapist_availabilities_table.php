<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapist_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('therapist_id');
            $table->unsignedTinyInteger('day_of_week'); // 1 Monday - 7 Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('slot_duration_minutes')->default(60);
            $table->unsignedInteger('capacity')->default(1);
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['therapist_id', 'day_of_week', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapist_availabilities');
    }
};
