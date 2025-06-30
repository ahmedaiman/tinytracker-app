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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Note content
            $table->string('title');
            $table->text('content');
            
            // Note metadata
            $table->enum('type', ['general', 'medical', 'diet', 'exercise', 'symptoms', 'other'])->default('general');
            $table->timestamp('noted_at');
            
            // Flags and additional data
            $table->boolean('is_important')->default(false);
            $table->json('tags')->nullable();
            $table->json('related_data')->nullable()->comment('Structured data related to the note (e.g., blood glucose, insulin dose)');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for common queries
            $table->index(['child_id', 'noted_at']);
            $table->index(['user_id', 'noted_at']);
            $table->index(['type', 'noted_at']);
            $table->index('is_important');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
