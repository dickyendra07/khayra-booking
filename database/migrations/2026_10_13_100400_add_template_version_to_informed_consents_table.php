<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. Seluruh consent yang ada otomatis template_version = 1 dan tetap tercetak persis
 * seperti sekarang. recorded_by menyimpan identitas staf pencatat untuk jejak audit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informed_consents', function (Blueprint $table) {
            if (! Schema::hasColumn('informed_consents', 'template_version')) {
                $table->unsignedTinyInteger('template_version')->default(1);
            }

            if (! Schema::hasColumn('informed_consents', 'recorded_by')) {
                $table->string('recorded_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('informed_consents', function (Blueprint $table) {
            foreach (['template_version', 'recorded_by'] as $column) {
                if (Schema::hasColumn('informed_consents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
