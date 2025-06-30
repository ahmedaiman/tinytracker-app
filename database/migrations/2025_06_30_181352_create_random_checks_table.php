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
        Schema::create('random_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('check_time');
            
            // Blood glucose level in mg/dL
            $table->unsignedSmallInteger('blood_glucose')->comment('mg/dL');
            
            // Optional fields
            $table->foreignId('insulin_type_id')->nullable()->constrained('insulin_types');
            $table->decimal('insulin_units', 5, 2)->nullable();
            $table->timestamp('insulin_injected_at')->nullable();
            
            // Context and notes
            $table->enum('context', ['fasting', 'before_meal', 'after_meal', 'before_sleep', 'night', 'other'])->default('other');
            $table->text('notes')->nullable();
            
            // Flags
            $table->boolean('is_high_alert')->default(false)->comment('Flag for high blood glucose');
            $table->boolean('is_low_alert')->default(false)->comment('Flag for low blood glucose');
            $table->boolean('is_manual_entry')->default(true)->comment('Whether the entry was manually recorded');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for common queries
            $table->index(['child_id', 'check_time']);
            $table->index(['user_id', 'check_time']);
            $table->index(['check_time', 'is_high_alert', 'is_low_alert']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('random_checks');
    }
};
