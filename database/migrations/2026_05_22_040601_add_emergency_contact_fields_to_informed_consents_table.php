<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informed_consents', function (Blueprint $table) {
            if (!Schema::hasColumn('informed_consents', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('relationship_to_patient');
            }

            if (!Schema::hasColumn('informed_consents', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }

            if (!Schema::hasColumn('informed_consents', 'emergency_contact_relation')) {
                $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('informed_consents', function (Blueprint $table) {
            if (Schema::hasColumn('informed_consents', 'emergency_contact_relation')) {
                $table->dropColumn('emergency_contact_relation');
            }

            if (Schema::hasColumn('informed_consents', 'emergency_contact_phone')) {
                $table->dropColumn('emergency_contact_phone');
            }

            if (Schema::hasColumn('informed_consents', 'emergency_contact_name')) {
                $table->dropColumn('emergency_contact_name');
            }
        });
    }
};
