<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('price_per_visit')->default(0);
            $table->unsignedInteger('package_3x_price')->nullable();
            $table->unsignedInteger('package_6x_price')->nullable();
            $table->unsignedInteger('package_12x_price')->nullable();
            $table->string('category')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        DB::table('clinic_services')->insert([
            [
                'name' => 'Pain Management Program',
                'price_per_visit' => 250000,
                'package_3x_price' => null,
                'package_6x_price' => null,
                'package_12x_price' => null,
                'category' => 'Program',
                'notes' => 'Hanya untuk kondisi akut',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sport Recovery Program',
                'price_per_visit' => 275000,
                'package_3x_price' => 775000,
                'package_6x_price' => 1550000,
                'package_12x_price' => 3100000,
                'category' => 'Program',
                'notes' => 'Sport massage / preventive',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '+ Recovery Pump',
                'price_per_visit' => 100000,
                'package_3x_price' => null,
                'package_6x_price' => null,
                'package_12x_price' => null,
                'category' => 'Add-on',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Musculoskeletal Program',
                'price_per_visit' => 350000,
                'package_3x_price' => 1000000,
                'package_6x_price' => 2000000,
                'package_12x_price' => 4000000,
                'category' => 'Program',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sport Physiotherapy and Rehabilitation',
                'price_per_visit' => 350000,
                'package_3x_price' => 1000000,
                'package_6x_price' => 2000000,
                'package_12x_price' => 4000000,
                'category' => 'Program',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '+ ESWT',
                'price_per_visit' => 75000,
                'package_3x_price' => null,
                'package_6x_price' => null,
                'package_12x_price' => null,
                'category' => 'Add-on',
                'notes' => 'Satu titik',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Specialist Physiotherapist Program',
                'price_per_visit' => 425000,
                'package_3x_price' => 1225000,
                'package_6x_price' => 2450000,
                'package_12x_price' => 4900000,
                'category' => 'Specialist',
                'notes' => 'Complexity',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stroke & Neuro Rehabilitation',
                'price_per_visit' => 425000,
                'package_3x_price' => 1225000,
                'package_6x_price' => 2450000,
                'package_12x_price' => 4900000,
                'category' => 'Specialist',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Active 50s Program',
                'price_per_visit' => 300000,
                'package_3x_price' => 800000,
                'package_6x_price' => 1590000,
                'package_12x_price' => 3120000,
                'category' => 'Program',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pilates in Rehabilitation',
                'price_per_visit' => 400000,
                'package_3x_price' => null,
                'package_6x_price' => null,
                'package_12x_price' => null,
                'category' => 'Program',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Consultation',
                'price_per_visit' => 50000,
                'package_3x_price' => null,
                'package_6x_price' => null,
                'package_12x_price' => null,
                'category' => 'Consultation',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '+ USG',
                'price_per_visit' => 150000,
                'package_3x_price' => null,
                'package_6x_price' => null,
                'package_12x_price' => null,
                'category' => 'Add-on',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '+ VVIP',
                'price_per_visit' => 70000,
                'package_3x_price' => null,
                'package_6x_price' => null,
                'package_12x_price' => null,
                'category' => 'Add-on',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ESWT Only',
                'price_per_visit' => 125000,
                'package_3x_price' => null,
                'package_6x_price' => null,
                'package_12x_price' => null,
                'category' => 'Add-on',
                'notes' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_services');
    }
};
