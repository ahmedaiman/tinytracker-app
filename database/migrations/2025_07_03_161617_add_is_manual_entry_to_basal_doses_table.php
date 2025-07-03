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
            if (!Schema::hasColumn('basal_doses', 'is_manual_entry')) {
                $table->boolean('is_manual_entry')->default(true)->after('notes')->comment('Whether the entry was manually recorded');
            }
            
            if (!Schema::hasColumn('basal_doses', 'is_correction_dose')) {
                $table->boolean('is_correction_dose')->default(false)->after('is_manual_entry')->comment('If this was an additional correction dose');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basal_doses', function (Blueprint $table) {
            if (Schema::hasColumn('basal_doses', 'is_manual_entry')) {
                $table->dropColumn('is_manual_entry');
            }
            
            if (Schema::hasColumn('basal_doses', 'is_correction_dose')) {
                $table->dropColumn('is_correction_dose');
            }
        });
    }
};
