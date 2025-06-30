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
        Schema::create('appointment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            
            // Reminder details
            $table->string('type')->default('email'); // email, sms, push, telegram
            $table->string('recipient_type'); // user, doctor, guardian, other
            $table->string('recipient_contact'); // email, phone number, etc.
            $table->text('message');
            
            // Scheduling
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            
            // Status tracking
            $table->string('status')->default('pending'); // pending, sent, failed, cancelled
            $table->text('status_message')->nullable();
            
            // For failed attempts
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            
            // Response/acknowledgment
            $table->boolean('is_acknowledged')->default(false);
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('acknowledgment_data')->nullable();
            
            // Additional metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['appointment_id', 'status']);
            $table->index(['scheduled_at', 'status']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_reminders');
    }
};
