<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('documentation_consent')->nullable()->after('referral_source_other');
            $table->text('documentation_consent_notes')->nullable()->after('documentation_consent');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'documentation_consent',
                'documentation_consent_notes',
            ]);
        });
    }
};
