<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentReminder extends Model
{
    use HasFactory;

    // Reminder types
    public const TYPE_EMAIL = 'email';
    public const TYPE_SMS = 'sms';
    public const TYPE_PUSH = 'push';
    public const TYPE_TELEGRAM = 'telegram';

    // Recipient types
    public const RECIPIENT_USER = 'user';
    public const RECIPIENT_DOCTOR = 'doctor';
    public const RECIPIENT_GUARDIAN = 'guardian';
    public const RECIPIENT_OTHER = 'other';

    // Reminder statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appointment_id',
        'type',
        'recipient_type',
        'recipient_contact',
        'message',
        'scheduled_at',
        'sent_at',
        'status',
        'status_message',
        'attempts',
        'last_attempt_at',
        'is_acknowledged',
        'acknowledged_at',
        'acknowledgment_data',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'is_acknowledged' => 'boolean',
        'attempts' => 'integer',
        'metadata' => 'array',
        'acknowledgment_data' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => self::TYPE_EMAIL,
        'status' => self::STATUS_PENDING,
        'attempts' => 0,
        'is_acknowledged' => false,
    ];

    /**
     * Get the appointment that owns the reminder.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Scope a query to only include pending reminders.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include sent reminders.
     */
    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope a query to only include failed reminders.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope a query to only include reminders scheduled before a given time.
     */
    public function scopeDue($query, $time = null)
    {
        $time = $time ?: now();
        return $query->where('scheduled_at', '<=', $time);
    }

    /**
     * Mark the reminder as sent.
     */
    public function markAsSent(string $message = null): void
    {
        $this->status = self::STATUS_SENT;
        $this->sent_at = now();
        $this->attempts++;
        $this->last_attempt_at = now();
        
        if ($message) {
            $this->status_message = $message;
        }
        
        $this->save();
    }

    /**
     * Mark the reminder as failed.
     */
    public function markAsFailed(string $message = null): void
    {
        $this->status = self::STATUS_FAILED;
        $this->attempts++;
        $this->last_attempt_at = now();
        
        if ($message) {
            $this->status_message = $message;
        }
        
        $this->save();
    }

    /**
     * Mark the reminder as acknowledged.
     */
    public function markAsAcknowledged(array $data = null): void
    {
        $this->is_acknowledged = true;
        $this->acknowledged_at = now();
        
        if ($data) {
            $this->acknowledgment_data = $data;
        }
        
        $this->save();
    }

    /**
     * Check if the reminder is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the reminder is sent.
     */
    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Check if the reminder is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if the reminder is due.
     */
    public function isDue(Carbon $time = null): bool
    {
        $time = $time ?: now();
        return $this->scheduled_at->lte($time);
    }

    /**
     * Get the reminder type options.
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_EMAIL => 'Email',
            self::TYPE_SMS => 'SMS',
            self::TYPE_PUSH => 'Push Notification',
            self::TYPE_TELEGRAM => 'Telegram',
        ];
    }

    /**
     * Get the recipient type options.
     */
    public static function getRecipientTypeOptions(): array
    {
        return [
            self::RECIPIENT_USER => 'User',
            self::RECIPIENT_DOCTOR => 'Doctor',
            self::RECIPIENT_GUARDIAN => 'Guardian',
            self::RECIPIENT_OTHER => 'Other',
        ];
    }

    /**
     * Get the status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SENT => 'Sent',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get the next scheduled reminder for an appointment.
     */
    public static function getNextScheduledForAppointment(int $appointmentId, string $type = null): ?self
    {
        $query = self::where('appointment_id', $appointmentId)
            ->where('status', self::STATUS_PENDING)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at', 'asc');
            
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->first();
    }

    /**
     * Get the delivery time (when it was sent or scheduled).
     */
    public function getDeliveryTime(): Carbon
    {
        return $this->sent_at ?? $this->scheduled_at;
    }
}
