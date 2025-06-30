<?php

namespace Tests\Feature\Models;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentReminderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_reminder()
    {
        $appointment = Appointment::factory()->create();
        
        $reminder = AppointmentReminder::create([
            'appointment_id' => $appointment->id,
            'type' => AppointmentReminder::TYPE_EMAIL,
            'recipient_type' => AppointmentReminder::RECIPIENT_USER,
            'recipient_contact' => 'user@example.com',
            'message' => 'Your appointment is coming up soon!',
            'scheduled_at' => now()->addDay(),
            'status' => AppointmentReminder::STATUS_PENDING,
            'metadata' => [
                'template' => 'appointment_reminder',
                'variables' => ['name' => 'John Doe'],
            ],
        ]);

        $this->assertInstanceOf(AppointmentReminder::class, $reminder);
        $this->assertEquals(AppointmentReminder::TYPE_EMAIL, $reminder->type);
        $this->assertEquals(AppointmentReminder::RECIPIENT_USER, $reminder->recipient_type);
        $this->assertEquals('user@example.com', $reminder->recipient_contact);
        $this->assertEquals('appointment_reminder', $reminder->metadata['template']);
        $this->assertTrue($reminder->isPending());
    }

    /** @test */
    public function it_belongs_to_an_appointment()
    {
        $appointment = Appointment::factory()->create();
        $reminder = AppointmentReminder::factory()->create([
            'appointment_id' => $appointment->id,
        ]);
        
        $this->assertInstanceOf(Appointment::class, $reminder->appointment);
        $this->assertEquals($appointment->id, $reminder->appointment->id);
    }

    /** @test */
    public function it_can_be_marked_as_sent()
    {
        $reminder = AppointmentReminder::factory()->create([
            'status' => AppointmentReminder::STATUS_PENDING,
            'sent_at' => null,
        ]);
        
        $reminder->markAsSent('Successfully sent');
        
        $this->assertEquals(AppointmentReminder::STATUS_SENT, $reminder->status);
        $this->assertNotNull($reminder->sent_at);
        $this->assertGreaterThan(0, $reminder->attempts);
        $this->assertNotNull($reminder->last_attempt_at);
        $this->assertEquals('Successfully sent', $reminder->status_message);
    }

    /** @test */
    public function it_can_be_marked_as_failed()
    {
        $reminder = AppointmentReminder::factory()->create([
            'status' => AppointmentReminder::STATUS_PENDING,
            'attempts' => 0,
        ]);
        
        $reminder->markAsFailed('Failed to send');
        
        $this->assertEquals(AppointmentReminder::STATUS_FAILED, $reminder->status);
        $this->assertEquals(1, $reminder->attempts);
        $this->assertNotNull($reminder->last_attempt_at);
        $this->assertEquals('Failed to send', $reminder->status_message);
    }

    /** @test */
    public function it_can_be_marked_as_acknowledged()
    {
        $reminder = AppointmentReminder::factory()->create([
            'is_acknowledged' => false,
            'acknowledged_at' => null,
        ]);
        
        $ackData = [
            'read_at' => now()->toDateTimeString(),
            'device' => 'iPhone',
            'ip' => '192.168.1.1',
        ];
        
        $reminder->markAsAcknowledged($ackData);
        
        $this->assertTrue($reminder->is_acknowledged);
        $this->assertNotNull($reminder->acknowledged_at);
        $this->assertEquals($ackData, $reminder->acknowledgment_data);
    }

    /** @test */
    public function it_can_check_if_due()
    {
        $dueReminder = AppointmentReminder::factory()->create([
            'scheduled_at' => now()->subHour(),
        ]);
        
        $futureReminder = AppointmentReminder::factory()->create([
            'scheduled_at' => now()->addHour(),
        ]);
        
        $this->assertTrue($dueReminder->isDue());
        $this->assertFalse($futureReminder->isDue());
    }

    /** @test */
    public function it_can_scope_pending_reminders()
    {
        $pending = AppointmentReminder::factory()->count(3)->create([
            'status' => AppointmentReminder::STATUS_PENDING,
        ]);
        
        // Create some non-pending reminders
        AppointmentReminder::factory()->count(2)->create([
            'status' => AppointmentReminder::STATUS_SENT,
        ]);
        
        $results = AppointmentReminder::pending()->get();
        
        $this->assertCount(3, $results);
        $this->assertTrue($results->every(function ($reminder) {
            return $reminder->status === AppointmentReminder::STATUS_PENDING;
        }));
    }

    /** @test */
    public function it_can_scope_due_reminders()
    {
        $dueReminder1 = AppointmentReminder::factory()->create([
            'scheduled_at' => now()->subHour(),
            'status' => AppointmentReminder::STATUS_PENDING,
        ]);
        
        $dueReminder2 = AppointmentReminder::factory()->create([
            'scheduled_at' => now()->subMinutes(30),
            'status' => AppointmentReminder::STATUS_PENDING,
        ]);
        
        $futureReminder = AppointmentReminder::factory()->create([
            'scheduled_at' => now()->addHour(),
            'status' => AppointmentReminder::STATUS_PENDING,
        ]);
        
        $results = AppointmentReminder::due()->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($dueReminder1));
        $this->assertTrue($results->contains($dueReminder2));
        $this->assertFalse($results->contains($futureReminder));
    }

    /** @test */
    public function it_can_get_next_scheduled_reminder()
    {
        $appointment = Appointment::factory()->create();
        
        // Create reminders in reverse order
        $reminder2 = AppointmentReminder::factory()->create([
            'appointment_id' => $appointment->id,
            'scheduled_at' => now()->addDays(2),
            'status' => AppointmentReminder::STATUS_PENDING,
        ]);
        
        $reminder1 = AppointmentReminder::factory()->create([
            'appointment_id' => $appointment->id,
            'scheduled_at' => now()->addDay(),
            'status' => AppointmentReminder::STATUS_PENDING,
        ]);
        
        // Create a sent reminder (should be ignored)
        AppointmentReminder::factory()->create([
            'appointment_id' => $appointment->id,
            'scheduled_at' => now()->addHours(2),
            'status' => AppointmentReminder::STATUS_SENT,
        ]);
        
        $nextReminder = AppointmentReminder::getNextScheduledForAppointment($appointment->id);
        
        $this->assertNotNull($nextReminder);
        $this->assertEquals($reminder1->id, $nextReminder->id);
    }

    /** @test */
    public function it_has_type_and_status_options()
    {
        $typeOptions = AppointmentReminder::getTypeOptions();
        $statusOptions = AppointmentReminder::getStatusOptions();
        
        $this->assertIsArray($typeOptions);
        $this->assertArrayHasKey(AppointmentReminder::TYPE_EMAIL, $typeOptions);
        $this->assertArrayHasKey(AppointmentReminder::TYPE_SMS, $typeOptions);
        $this->assertArrayHasKey(AppointmentReminder::TYPE_TELEGRAM, $typeOptions);
        $this->assertArrayHasKey(AppointmentReminder::TYPE_PUSH, $typeOptions);
        
        $this->assertIsArray($statusOptions);
        $this->assertArrayHasKey(AppointmentReminder::STATUS_PENDING, $statusOptions);
        $this->assertArrayHasKey(AppointmentReminder::STATUS_SENT, $statusOptions);
        $this->assertArrayHasKey(AppointmentReminder::STATUS_FAILED, $statusOptions);
        $this->assertArrayHasKey(AppointmentReminder::STATUS_CANCELLED, $statusOptions);
    }
    
    /** @test */
    public function it_has_recipient_type_options()
    {
        $options = AppointmentReminder::getRecipientTypeOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey(AppointmentReminder::RECIPIENT_USER, $options);
        $this->assertArrayHasKey(AppointmentReminder::RECIPIENT_DOCTOR, $options);
        $this->assertArrayHasKey(AppointmentReminder::RECIPIENT_GUARDIAN, $options);
        $this->assertArrayHasKey(AppointmentReminder::RECIPIENT_OTHER, $options);
    }
}
