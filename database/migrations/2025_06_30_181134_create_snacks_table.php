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
        Schema::create('snacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('snack_time');
            
            // Food details
            $table->text('food_description');
            $table->unsignedSmallInteger('carbs_grams')->nullable();
            $table->unsignedSmallInteger('sugars_grams')->nullable();
            
            // Blood glucose levels (optional)
            $table->unsignedSmallInteger('pre_snack_bg')->nullable()->comment('mg/dL');
            $table->unsignedSmallInteger('post_snack_bg')->nullable()->comment('mg/dL');
            $table->timestamp('post_snack_bg_time')->nullable();
            
            // Insulin details (optional)
            $table->foreignId('insulin_type_id')->nullable()->constrained('insulin_types');
            $table->decimal('insulin_units', 5, 2)->nullable();
            $table->timestamp('insulin_injected_at')->nullable();
            
            // Additional notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['child_id', 'snack_time']);
            $table->index(['user_id', 'snack_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snacks');
    }
};
