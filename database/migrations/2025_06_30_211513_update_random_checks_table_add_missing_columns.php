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
        Schema::table('random_checks', function (Blueprint $table) {
            // Add missing columns
            $table->foreignId('insulin_type_id')->nullable()->after('bg_value')->constrained('insulin_types');
            $table->decimal('insulin_units', 5, 2)->nullable()->after('insulin_type_id');
            $table->timestamp('insulin_injected_at')->nullable()->after('insulin_units');
            
            // Add an index for the foreign key
            $table->index('insulin_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('random_checks', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['insulin_type_id']);
            
            // Drop the index
            $table->dropIndex(['insulin_type_id']);
            
            // Drop the columns
            $table->dropColumn(['insulin_type_id', 'insulin_units', 'insulin_injected_at']);
        });
    }
};
