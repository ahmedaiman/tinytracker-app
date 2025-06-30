<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 month');
        $endTime = (clone $startTime)->modify('+1 hour');
        
        // Ensure we have a valid user with the guardian role
        $user = User::where('role', 'guardian')->first() ?? 
            User::factory()->create(['role' => 'guardian']);
            
        // Ensure we have a doctor user
        $doctor = User::where('role', 'doctor')->first() ?? 
            User::factory()->create(['role' => 'doctor']);
            
        // Create a child for the user if none exists
        $child = Child::where('user_id', $user->id)->first() ?? 
            Child::factory()->create(['user_id' => $user->id]);
        
        return [
            'child_id' => $child->id,
            'user_id' => $user->id,
            'doctor_id' => $this->faker->boolean(80) ? $doctor->id : null,
            'title' => $this->faker->randomElement([
                'Regular Checkup',
                'Pediatric Consultation',
                'Blood Test',
                'Follow-up Visit',
                'Vaccination',
                'Nutritional Counseling',
                'Diabetes Management',
                'Growth Monitoring',
            ]),
            'description' => $this->faker->optional(0.8)->paragraph,
            'location' => $this->faker->randomElement([
                'Main Hospital - Room 101',
                'Children\'s Wing - Floor 2',
                'Pediatric Clinic',
                'Endocrinology Department',
                $this->faker->company . ' Medical Center',
            ]),
            'type' => $this->faker->randomElement([
                Appointment::TYPE_CHECKUP,
                Appointment::TYPE_CONSULTATION,
                Appointment::TYPE_TEST,
                Appointment::TYPE_FOLLOW_UP,
                Appointment::TYPE_OTHER,
            ]),
            'status' => $this->faker->randomElement([
                Appointment::STATUS_SCHEDULED,
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_NOSHOW,
            ]),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'all_day' => $this->faker->boolean(10), // 10% chance of being an all-day event
            'recurrence_pattern' => $this->faker->optional(0.3)->randomElement([
                Appointment::RECURRENCE_DAILY,
                Appointment::RECURRENCE_WEEKLY,
                Appointment::RECURRENCE_MONTHLY,
                Appointment::RECURRENCE_YEARLY,
            ]),
            'recurrence_interval' => $this->faker->optional(0.3)->numberBetween(1, 4),
            'recurrence_days' => $this->faker->optional(0.2)->randomElements(
                [0, 1, 2, 3, 4, 5, 6],
                $this->faker->numberBetween(1, 3)
            ),
            'recurrence_end_date' => $this->faker->optional(0.3)->dateTimeBetween('+1 month', '+1 year'),
            'reminder_sent' => $this->faker->boolean(30),
            'reminder_sent_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 week', 'now'),
            'telegram_notification_sent' => $this->faker->boolean(20),
            'telegram_notification_sent_at' => $this->faker->optional(0.2)->dateTimeBetween('-1 week', 'now'),
            'metadata' => $this->faker->optional(0.5)->randomElements([
                'doctor_notes' => $this->faker->optional()->sentence,
                'special_instructions' => $this->faker->optional()->sentence,
                'insurance_provider' => $this->faker->optional(0.7)->company,
                'insurance_policy' => $this->faker->optional(0.5)->bothify('POL-#######'),
                'referral' => $this->faker->optional(0.3)->boolean,
                'preparation_required' => $this->faker->optional(0.4)->sentence,
            ]),
        ];
    }

    /**
     * Indicate that the appointment is a checkup.
     */
    public function checkup()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Appointment::TYPE_CHECKUP,
                'title' => 'Regular Checkup',
            ];
        });
    }

    /**
     * Indicate that the appointment is a consultation.
     */
    public function consultation()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Appointment::TYPE_CONSULTATION,
                'title' => 'Pediatric Consultation',
            ];
        });
    }

    /**
     * Indicate that the appointment is a test.
     */
    public function test()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Appointment::TYPE_TEST,
                'title' => $this->faker->randomElement([
                    'Blood Test',
                    'Urine Test',
                    'Glucose Tolerance Test',
                    'HbA1c Test',
                ]),
            ];
        });
    }

    /**
     * Indicate that the appointment is a follow-up.
     */
    public function followUp()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Appointment::TYPE_FOLLOW_UP,
                'title' => 'Follow-up Visit',
            ];
        });
    }

    /**
     * Indicate that the appointment is scheduled.
     */
    public function scheduled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Appointment::STATUS_SCHEDULED,
            ];
        });
    }

    /**
     * Indicate that the appointment is confirmed.
     */
    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Appointment::STATUS_CONFIRMED,
            ];
        });
    }

    /**
     * Indicate that the appointment is completed.
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Appointment::STATUS_COMPLETED,
                'start_time' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
                'end_time' => fn (array $attributes) => 
                    Carbon::parse($attributes['start_time'])->addHour(),
            ];
        });
    }

    /**
     * Indicate that the appointment is cancelled.
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Appointment::STATUS_CANCELLED,
                'metadata' => [
                    'cancellation_reason' => $this->faker->randomElement([
                        'Patient rescheduled',
                        'Doctor unavailable',
                        'Emergency',
                        'No show',
                    ]),
                    'cancelled_by' => $this->faker->randomElement(['patient', 'doctor', 'admin']),
                    'cancelled_at' => now()->toDateTimeString(),
                ],
            ];
        });
    }

    /**
     * Indicate that the appointment is a no-show.
     */
    public function noShow()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Appointment::STATUS_NO_SHOW,
                'metadata' => [
                    'no_show_reason' => $this->faker->optional()->sentence,
                    'notified' => $this->faker->boolean,
                ],
            ];
        });
    }

    /**
     * Indicate that the appointment is recurring.
     */
    public function recurring()
    {
        return $this->state(function (array $attributes) {
            $pattern = $this->faker->randomElement([
                Appointment::RECURRENCE_WEEKLY,
                Appointment::RECURRENCE_MONTHLY,
                Appointment::RECURRENCE_YEARLY,
            ]);
            
            $state = [
                'recurrence_pattern' => $pattern,
                'recurrence_interval' => $this->faker->numberBetween(1, 4),
                'recurrence_end_date' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            ];
            
            if ($pattern === Appointment::RECURRENCE_WEEKLY) {
                $state['recurrence_days'] = $this->faker->randomElements(
                    [0, 1, 2, 3, 4, 5, 6],
                    $this->faker->numberBetween(1, 3)
                );
                sort($state['recurrence_days']);
            }
            
            return $state;
        });
    }

    /**
     * Indicate that the appointment has a reminder.
     */
    public function withReminder()
    {
        return $this->state(function (array $attributes) {
            return [
                'reminder_sent' => false,
                'reminder_sent_at' => null,
            ];
        });
    }

    /**
     * Indicate that the appointment has a Telegram notification.
     */
    public function withTelegramNotification()
    {
        return $this->state(function (array $attributes) {
            return [
                'telegram_notification_sent' => false,
                'telegram_notification_sent_at' => null,
            ];
        });
    }
}
