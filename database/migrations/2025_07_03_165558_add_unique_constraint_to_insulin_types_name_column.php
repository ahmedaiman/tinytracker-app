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
        // Remove any existing duplicate names first to avoid constraint violations
        // This is a simple approach - in production, you might want to handle duplicates more carefully
        $duplicates = \DB::table('insulin_types')
            ->select('name', \DB::raw('COUNT(*) as count'))
            ->groupBy('name')
            ->having('count', '>', 1)
            ->get();
            
        foreach ($duplicates as $duplicate) {
            // Keep one record and delete the rest
            $ids = \DB::table('insulin_types')
                ->where('name', $duplicate->name)
                ->orderBy('id')
                ->skip(1)
                ->pluck('id');
                
            if ($ids->isNotEmpty()) {
                \DB::table('insulin_types')->whereIn('id', $ids)->delete();
            }
        }
        
        // Now add the unique constraint
        Schema::table('insulin_types', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insulin_types', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
