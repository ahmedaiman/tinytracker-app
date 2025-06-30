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
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Appointment details
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->default('checkup'); // checkup, consultation, test, follow_up, other
            $table->string('status')->default('scheduled'); // scheduled, confirmed, completed, cancelled, no_show
            
            // Date and time
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('all_day')->default(false);
            
            // Reminders and notifications
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();
            $table->boolean('telegram_notification_sent')->default(false);
            $table->timestamp('telegram_notification_sent_at')->nullable();
            
            // Recurring appointments
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly, yearly
            $table->string('recurrence_interval')->nullable(); // e.g., '1' for every 1 week/month
            $table->json('recurrence_days')->nullable(); // For weekly patterns, e.g., [1,3,5] for Mon, Wed, Fri
            $table->date('recurrence_end_date')->nullable();
            
            // Additional metadata
            $table->json('metadata')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['start_time', 'end_time']);
            $table->index('status');
            $table->index('type');
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
