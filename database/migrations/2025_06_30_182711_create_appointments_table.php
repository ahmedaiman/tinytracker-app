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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Appointment details
            $table->enum('doctor_type', ['endocrinologist', 'diabetes_educator', 'dietitian', 'primary_care', 'other']);
            $table->string('doctor_name');
            $table->string('location');
            $table->timestamp('start_at');
            $table->text('note')->nullable();
            
            // Reminder flags
            $table->boolean('reminder_24h_sent')->default(false);
            $table->boolean('reminder_2h_sent')->default(false);
            
            // Status
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for common queries
            $table->index(['child_id', 'start_at']);
            $table->index(['user_id', 'start_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
