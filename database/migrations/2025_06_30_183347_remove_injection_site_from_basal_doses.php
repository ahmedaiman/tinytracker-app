<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('basal_doses', function (Blueprint $table) {
            // Remove injection_site column as it's not required
            if (Schema::hasColumn('basal_doses', 'injection_site')) {
                $table->dropColumn('injection_site');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basal_doses', function (Blueprint $table) {
            // Add back the injection_site column if rolling back
            if (!Schema::hasColumn('basal_doses', 'injection_site')) {
                $table->string('injection_site', 50)->nullable();
            }
        });
    }
};
