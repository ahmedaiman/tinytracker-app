<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AppointmentReminderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AppointmentReminder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $appointment = Appointment::factory()->create();
        $scheduledAt = $this->faker->dateTimeBetween('now', '+1 month');
        
        return [
            'appointment_id' => $appointment->id,
            'type' => $this->faker->randomElement([
                AppointmentReminder::TYPE_EMAIL,
                AppointmentReminder::TYPE_SMS,
                AppointmentReminder::TYPE_TELEGRAM,
                AppointmentReminder::TYPE_PUSH,
            ]),
            'recipient_type' => $this->faker->randomElement([
                AppointmentReminder::RECIPIENT_USER,
                AppointmentReminder::RECIPIENT_DOCTOR,
                AppointmentReminder::RECIPIENT_GUARDIAN,
            ]),
            'recipient_contact' => $this->faker->safeEmail,
            'message' => $this->faker->sentence,
            'scheduled_at' => $scheduledAt,
            'status' => $this->faker->randomElement([
                AppointmentReminder::STATUS_PENDING,
                AppointmentReminder::STATUS_SENT,
                AppointmentReminder::STATUS_FAILED,
            ]),
            'status_message' => $this->faker->optional(0.3)->sentence,
            'attempts' => $this->faker->numberBetween(0, 3),
            'last_attempt_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 week', 'now'),
            'is_acknowledged' => $this->faker->boolean(30),
            'acknowledged_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 week', 'now'),
            'acknowledgment_data' => $this->faker->optional(0.2)->randomElements([
                'read' => true,
                'device' => $this->faker->randomElement(['iPhone', 'Android', 'Web']),
                'ip' => $this->faker->ipv4,
            ]),
            'metadata' => $this->faker->optional(0.4)->randomElements([
                'template_id' => $this->faker->uuid,
                'campaign_id' => $this->faker->uuid,
                'user_agent' => $this->faker->userAgent,
            ]),
        ];
    }

    /**
     * Indicate that the reminder is pending.
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AppointmentReminder::STATUS_PENDING,
                'sent_at' => null,
                'attempts' => 0,
                'last_attempt_at' => null,
            ];
        });
    }

    /**
     * Indicate that the reminder is sent.
     */
    public function sent()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AppointmentReminder::STATUS_SENT,
                'sent_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
                'attempts' => $this->faker->numberBetween(1, 3),
                'last_attempt_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            ];
        });
    }

    /**
     * Indicate that the reminder is failed.
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AppointmentReminder::STATUS_FAILED,
                'sent_at' => null,
                'attempts' => $this->faker->numberBetween(1, 5),
                'last_attempt_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
                'status_message' => $this->faker->sentence,
            ];
        });
    }

    /**
     * Indicate that the reminder is for an email.
     */
    public function email()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AppointmentReminder::TYPE_EMAIL,
                'recipient_contact' => $this->faker->safeEmail,
            ];
        });
    }

    /**
     * Indicate that the reminder is for an SMS.
     */
    public function sms()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AppointmentReminder::TYPE_SMS,
                'recipient_contact' => $this->faker->phoneNumber,
            ];
        });
    }

    /**
     * Indicate that the reminder is for a Telegram message.
     */
    public function telegram()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AppointmentReminder::TYPE_TELEGRAM,
                'recipient_contact' => (string) $this->faker->randomNumber(9, true),
            ];
        });
    }

    /**
     * Indicate that the reminder is for a push notification.
     */
    public function push()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => AppointmentReminder::TYPE_PUSH,
                'recipient_contact' => $this->faker->uuid,
            ];
        });
    }

    /**
     * Indicate that the reminder is for a specific appointment.
     */
    public function forAppointment(Appointment $appointment)
    {
        return $this->state(function (array $attributes) use ($appointment) {
            return [
                'appointment_id' => $appointment->id,
            ];
        });
    }

    /**
     * Indicate that the reminder is scheduled for a specific date and time.
     */
    public function scheduledFor($dateTime)
    {
        return $this->state(function (array $attributes) use ($dateTime) {
            return [
                'scheduled_at' => $dateTime instanceof \DateTime 
                    ? $dateTime 
                    : Carbon::parse($dateTime),
            ];
        });
    }
}
