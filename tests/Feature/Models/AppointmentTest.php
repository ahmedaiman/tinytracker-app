<?php

namespace Tests\Feature\Models;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use App\Models\Child;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_appointment()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        
        $appointment = Appointment::create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'title' => 'Regular Checkup',
            'description' => 'Annual pediatric checkup',
            'location' => 'Children\'s Hospital',
            'type' => Appointment::TYPE_CHECKUP,
            'status' => Appointment::STATUS_SCHEDULED,
            'start_time' => $start = now()->addDays(7)->setTime(14, 0),
            'end_time' => $start->copy()->addHour(),
            'all_day' => false,
            'metadata' => [
                'doctor_name' => 'Dr. Smith',
                'special_instructions' => 'Bring insurance card',
            ],
        ]);

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertEquals('Regular Checkup', $appointment->title);
        $this->assertEquals(Appointment::TYPE_CHECKUP, $appointment->type);
        $this->assertEquals(Appointment::STATUS_SCHEDULED, $appointment->status);
        
        // Debug output
        echo "\n\n--- DEBUG METADATA ---\n";
        echo "Metadata type: " . gettype($appointment->metadata) . "\n";
        echo "Metadata value: " . json_encode($appointment->metadata) . "\n";
        echo "Metadata keys: " . (is_array($appointment->metadata) ? json_encode(array_keys($appointment->metadata)) : 'N/A') . "\n";
        
        // Check if metadata is accessible as an array
        $metadata = $appointment->metadata;
        if (is_array($metadata) || is_object($metadata)) {
            $metadata = (array) $metadata;
            echo "Doctor name in metadata: " . ($metadata['doctor_name'] ?? 'NOT FOUND') . "\n";
        }
        
        echo "--- END DEBUG ---\n\n";
        
        // Assert the doctor_name exists in metadata
        $this->assertArrayHasKey('doctor_name', (array)$appointment->metadata, 'doctor_name key not found in metadata');
        $this->assertEquals('Dr. Smith', $appointment->metadata['doctor_name']);
        $this->assertFalse($appointment->all_day);
    }

    /** @test */
    public function it_has_relationships()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        
        $appointment = Appointment::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
        ]);
        
        $this->assertInstanceOf(Child::class, $appointment->child);
        $this->assertInstanceOf(User::class, $appointment->user);
        $this->assertInstanceOf(User::class, $appointment->doctor);
        $this->assertEquals($child->id, $appointment->child->id);
        $this->assertEquals($user->id, $appointment->user->id);
        $this->assertEquals($doctor->id, $appointment->doctor->id);
    }

    /** @test */
    public function it_can_have_reminders()
    {
        $appointment = Appointment::factory()->create();
        
        $reminder1 = AppointmentReminder::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => AppointmentReminder::TYPE_EMAIL,
        ]);
        
        $reminder2 = AppointmentReminder::factory()->create([
            'appointment_id' => $appointment->id,
            'type' => AppointmentReminder::TYPE_SMS,
        ]);
        
        $this->assertCount(2, $appointment->reminders);
        $this->assertTrue($appointment->reminders->contains($reminder1));
        $this->assertTrue($appointment->reminders->contains($reminder2));
    }

    /** @test */
    public function it_can_scope_upcoming_appointments()
    {
        $upcoming = Appointment::factory()->create([
            'start_time' => now()->addDays(2),
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        
        $past = Appointment::factory()->create([
            'start_time' => now()->subDays(2),
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        
        $cancelled = Appointment::factory()->create([
            'start_time' => now()->addDays(1),
            'status' => Appointment::STATUS_CANCELLED,
        ]);
        
        $upcomingAppointments = Appointment::upcoming()->get();
        
        $this->assertTrue($upcomingAppointments->contains($upcoming));
        $this->assertFalse($upcomingAppointments->contains($past));
        $this->assertFalse($upcomingAppointments->contains($cancelled));
    }

    /** @test */
    public function it_can_scope_past_appointments()
    {
        $past = Appointment::factory()->create([
            'start_time' => now()->subDays(2),
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        
        $cancelled = Appointment::factory()->create([
            'start_time' => now()->subDays(1),
            'status' => Appointment::STATUS_CANCELLED,
        ]);
        
        $upcoming = Appointment::factory()->create([
            'start_time' => now()->addDays(2),
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        
        $pastAppointments = Appointment::past()->get();
        
        $this->assertTrue($pastAppointments->contains($past));
        $this->assertTrue($pastAppointments->contains($cancelled));
        $this->assertFalse($pastAppointments->contains($upcoming));
    }

    /** @test */
    public function it_can_scope_appointments_between_dates()
    {
        $inRange1 = Appointment::factory()->create([
            'start_time' => '2023-06-15 10:00:00',
        ]);
        
        $inRange2 = Appointment::factory()->create([
            'start_time' => '2023-06-20 14:00:00',
        ]);
        
        $outOfRange = Appointment::factory()->create([
            'start_time' => '2023-07-01 09:00:00',
        ]);
        
        $results = Appointment::betweenDates('2023-06-10', '2023-06-25')->get();
        
        $this->assertTrue($results->contains($inRange1));
        $this->assertTrue($results->contains($inRange2));
        $this->assertFalse($results->contains($outOfRange));
    }

    /** @test */
    public function it_can_check_if_appointment_is_upcoming()
    {
        $upcoming = Appointment::factory()->create([
            'start_time' => now()->addDay(),
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        
        $past = Appointment::factory()->create([
            'start_time' => now()->subDay(),
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        
        $cancelled = Appointment::factory()->create([
            'start_time' => now()->addDay(),
            'status' => Appointment::STATUS_CANCELLED,
        ]);
        
        $this->assertTrue($upcoming->isUpcoming());
        $this->assertFalse($past->isUpcoming());
        $this->assertFalse($cancelled->isUpcoming());
    }

    /** @test */
    public function it_can_check_if_appointment_is_recurring()
    {
        $recurring = Appointment::factory()->create([
            'recurrence_rule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO,WE,FR',
            'recurrence_days' => [1, 3, 5], // Mon, Wed, Fri
            'recurrence_end_date' => now()->addMonth(),
        ]);
        
        $oneTime = Appointment::factory()->create([
            'recurrence_rule' => null,
        ]);
        
        $this->assertTrue($recurring->isRecurring());
        $this->assertFalse($oneTime->isRecurring());
    }

    /** @test */
    public function it_can_calculate_next_occurrence_for_recurring_appointments()
    {
        $now = now();
        $startTime = $now->copy()->subWeek()->setTime(10, 0); // Last week
        
        $appointment = Appointment::factory()->create([
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addHour(),
            'recurrence_pattern' => 'weekly',
            'recurrence_interval' => 1,
            'recurrence_days' => [$startTime->dayOfWeek],
            'recurrence_end_date' => $now->copy()->addMonth(),
        ]);
        
        $nextOccurrence = $appointment->getNextOccurrence();
        
        $this->assertNotNull($nextOccurrence);
        
        // The next occurrence should be on the same day of the week as the original start time
        $this->assertEquals($startTime->dayOfWeek, $nextOccurrence->start_time->dayOfWeek);
        
        // The next occurrence should be in the future
        $this->assertTrue($nextOccurrence->start_time->isFuture());
        
        // The next occurrence should be after the original start time
        $this->assertTrue($nextOccurrence->start_time->gt($startTime));
        
        // The next occurrence should be within the recurrence end date
        $this->assertTrue($nextOccurrence->start_time->lte($appointment->recurrence_end_date));
        
        // The duration should be 1 hour (60 minutes)
        $this->assertEquals(60, $nextOccurrence->start_time->diffInMinutes($nextOccurrence->end_time));
    }

    /** @test */
    public function it_can_mark_appointment_as_confirmed()
    {
        $appointment = Appointment::factory()->create([
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        
        $appointment->confirm();
        
        $this->assertEquals(Appointment::STATUS_CONFIRMED, $appointment->status);
    }

    /** @test */
    public function it_can_mark_appointment_as_completed()
    {
        $appointment = Appointment::factory()->create([
            'status' => Appointment::STATUS_CONFIRMED,
        ]);
        
        $appointment->complete();
        
        $this->assertEquals(Appointment::STATUS_COMPLETED, $appointment->status);
    }

    /** @test */
    public function it_can_cancel_appointment_with_reason()
    {
        $appointment = Appointment::factory()->create([
            'status' => Appointment::STATUS_CONFIRMED,
        ]);
        
        $reason = 'Patient rescheduled';
        $appointment->cancel($reason);
        
        $this->assertEquals(Appointment::STATUS_CANCELLED, $appointment->status);
        $this->assertEquals($reason, $appointment->metadata['cancellation_reason']);
    }

    /** @test */
    public function it_can_mark_telegram_notification_as_sent()
    {
        $appointment = Appointment::factory()->create();
        
        $appointment->markTelegramNotificationAsSent();
        
        $this->assertTrue($appointment->telegram_notification_sent);
        $this->assertNotNull($appointment->telegram_notification_sent_at);
    }

    /** @test */
    public function it_has_type_and_status_options()
    {
        $typeOptions = Appointment::getTypeOptions();
        $statusOptions = Appointment::getStatusOptions();
        
        $this->assertIsArray($typeOptions);
        $this->assertArrayHasKey(Appointment::TYPE_CHECKUP, $typeOptions);
        $this->assertArrayHasKey(Appointment::TYPE_CONSULTATION, $typeOptions);
        
        $this->assertIsArray($statusOptions);
        $this->assertArrayHasKey(Appointment::STATUS_SCHEDULED, $statusOptions);
        $this->assertArrayHasKey(Appointment::STATUS_CONFIRMED, $statusOptions);
    }
}
