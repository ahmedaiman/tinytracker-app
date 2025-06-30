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
        Schema::create('basal_doses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('insulin_type_id')->constrained('insulin_types');
            
            // Dose information
            $table->decimal('units', 5, 2)->comment('Insulin units');
            $table->timestamp('injected_at');
            
            // Additional details
            $table->string('injection_site', 50)->nullable()->comment('e.g., abdomen, thigh, arm');
            $table->text('notes')->nullable();
            
            // Flags
            $table->boolean('is_manual_entry')->default(true)->comment('Whether the entry was manually recorded');
            $table->boolean('is_correction_dose')->default(false)->comment('If this was an additional correction dose');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for common queries
            $table->index(['child_id', 'injected_at']);
            $table->index(['user_id', 'injected_at']);
            $table->index(['insulin_type_id', 'injected_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basal_doses');
    }
};
