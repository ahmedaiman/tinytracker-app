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
        Schema::table('notes', function (Blueprint $table) {
            // Rename note_text to content to match the expected column name
            $table->renameColumn('note_text', 'content');
            
            // Add missing columns
            $table->json('tags')->nullable()->after('content');
            $table->json('related_data')->nullable()->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Revert the column name back to note_text
            $table->renameColumn('content', 'note_text');
            
            // Drop the added columns
            $table->dropColumn(['tags', 'related_data']);
        });
    }
};
