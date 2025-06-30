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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'other']);
            $table->timestamp('meal_time');
            
            // Blood glucose levels
            $table->unsignedSmallInteger('pre_meal_bg')->nullable()->comment('mg/dL');
            $table->unsignedSmallInteger('post_meal_bg')->nullable()->comment('mg/dL');
            $table->timestamp('post_meal_bg_time')->nullable();
            
            // Insulin details
            $table->foreignId('insulin_type_id')->nullable()->constrained('insulin_types');
            $table->decimal('insulin_units', 5, 2)->nullable();
            $table->timestamp('insulin_injected_at')->nullable();
            
            // Food details
            $table->text('food_description')->nullable();
            $table->unsignedSmallInteger('carbs_grams')->nullable();
            $table->unsignedSmallInteger('sugars_grams')->nullable();
            
            // Status and flags
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->boolean('is_override')->default(false);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['child_id', 'meal_time']);
            $table->index(['user_id', 'meal_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
