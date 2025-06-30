<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    // Appointment types
    public const TYPE_CHECKUP = 'checkup';
    public const TYPE_CONSULTATION = 'consultation';
    public const TYPE_TEST = 'test';
    public const TYPE_FOLLOW_UP = 'follow_up';
    public const TYPE_OTHER = 'other';

    // Appointment statuses
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    // Recurrence patterns
    public const RECURRENCE_NONE = null;
    public const RECURRENCE_DAILY = 'daily';
    public const RECURRENCE_WEEKLY = 'weekly';
    public const RECURRENCE_MONTHLY = 'monthly';
    public const RECURRENCE_YEARLY = 'yearly';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'child_id',
        'user_id',
        'doctor_id',
        'title',
        'description',
        'location',
        'type',
        'status',
        'start_time',
        'end_time',
        'all_day',
        'recurrence_pattern',
        'recurrence_interval',
        'recurrence_days',
        'recurrence_end_date',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'all_day' => 'boolean',
        'reminder_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
        'telegram_notification_sent' => 'boolean',
        'telegram_notification_sent_at' => 'datetime',
        'recurrence_days' => 'array',
        'recurrence_end_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => self::TYPE_CHECKUP,
        'status' => self::STATUS_SCHEDULED,
        'all_day' => false,
        'reminder_sent' => false,
        'telegram_notification_sent' => false,
    ];

    /**
     * Get the child associated with the appointment.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user who created the appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the doctor associated with the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get all of the appointment reminders.
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(AppointmentReminder::class);
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now())
                    ->where('status', '!=', self::STATUS_CANCELLED)
                    ->where('status', '!=', self::STATUS_COMPLETED)
                    ->orderBy('start_time', 'asc');
    }

    /**
     * Scope a query to only include past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('end_time', '<', now())
                    ->orWhere('status', self::STATUS_COMPLETED)
                    ->orWhere('status', self::STATUS_CANCELLED)
                    ->orderBy('start_time', 'desc');
    }

    /**
     * Scope a query to only include appointments for a specific child.
     */
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    /**
     * Scope a query to only include appointments for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include appointments with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include appointments within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate = null)
    {
        $endDate = $endDate ?: $startDate;
        
        return $query->whereBetween('start_time', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    /**
     * Check if the appointment is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->start_time->isFuture() && 
               !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED]);
    }

    /**
     * Check if the appointment is recurring.
     */
    public function isRecurring(): bool
    {
        return !is_null($this->recurrence_pattern);
    }

    /**
     * Mark the appointment as confirmed.
     */
    public function confirm(): void
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->save();
    }

    /**
     * Mark the appointment as completed.
     */
    public function complete(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    /**
     * Cancel the appointment.
     */
    public function cancel(string $reason = null): void
    {
        $this->status = self::STATUS_CANCELLED;
        
        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['cancellation_reason'] = $reason;
            $this->metadata = $metadata;
        }
        
        $this->save();
    }

    /**
     * Mark the reminder as sent.
     */
    public function markReminderAsSent(): void
    {
        $this->reminder_sent = true;
        $this->reminder_sent_at = now();
        $this->save();
    }

    /**
     * Mark the Telegram notification as sent.
     */
    public function markTelegramNotificationAsSent(): void
    {
        $this->telegram_notification_sent = true;
        $this->telegram_notification_sent_at = now();
        $this->save();
    }

    /**
     * Get the duration of the appointment in minutes.
     */
    public function getDurationInMinutes(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Get the appointment type options.
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_CHECKUP => 'Regular Checkup',
            self::TYPE_CONSULTATION => 'Doctor Consultation',
            self::TYPE_TEST => 'Medical Test',
            self::TYPE_FOLLOW_UP => 'Follow-up Visit',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get the status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_NO_SHOW => 'No Show',
        ];
    }

    /**
     * Get the recurrence pattern options.
     */
    public static function getRecurrenceOptions(): array
    {
        return [
            self::RECURRENCE_NONE => 'Does not repeat',
            self::RECURRENCE_DAILY => 'Daily',
            self::RECURRENCE_WEEKLY => 'Weekly',
            self::RECURRENCE_MONTHLY => 'Monthly',
            self::RECURRENCE_YEARLY => 'Yearly',
        ];
    }

    /**
     * Get the days of the week options for weekly recurrence.
     */
    public static function getDaysOfWeekOptions(): array
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }

    /**
     * Get the next occurrence of a recurring appointment.
     */
    public function getNextOccurrence()
    {
        if (!$this->isRecurring()) {
            return null;
        }
        
        $now = now();
        $nextDate = Carbon::parse($this->start_time);
        
        // If the next occurrence is in the past, calculate the next one
        while ($nextDate->lt($now) || $nextDate->eq($this->start_time)) {
            $nextDate = $this->calculateNextOccurrenceDate($nextDate);
            
            // Safety check to prevent infinite loops
            if ($nextDate === null || $nextDate->gt(now()->addYears(2))) {
                return null;
            }
        }
        
        // Create a new appointment instance for the next occurrence
        $nextAppointment = $this->replicate();
        $nextAppointment->start_time = $nextDate;
        
        // Calculate the duration of the original appointment
        $durationInMinutes = $this->start_time->diffInMinutes($this->end_time);
        
        // Set the end time by adding the duration to the new start time
        $nextAppointment->end_time = (clone $nextDate)->addMinutes($durationInMinutes);
        
        return $nextAppointment;
    }
    
    /**
     * Calculate the next occurrence date based on the recurrence pattern.
     */
    protected function calculateNextOccurrenceDate(Carbon $date)
    {
        $nextDate = clone $date;
        
        switch ($this->recurrence_pattern) {
            case self::RECURRENCE_DAILY:
                $nextDate->addDays($this->recurrence_interval ?? 1);
                break;
                
            case self::RECURRENCE_WEEKLY:
                // Get recurrence days as an array (it might be JSON encoded)
                $recurrenceDays = is_string($this->recurrence_days) 
                    ? json_decode($this->recurrence_days, true) 
                    : (is_array($this->recurrence_days) ? $this->recurrence_days : []);
                
                if (!empty($recurrenceDays)) {
                    $currentDay = $nextDate->dayOfWeek;
                    $nextDay = null;
                    
                    // Sort the days to ensure proper order
                    sort($recurrenceDays);
                    
                    // Find the next day in the same week
                    foreach ($recurrenceDays as $day) {
                        if ($day > $currentDay) {
                            $nextDay = $day;
                            break;
                        }
                    }
                    
                    if ($nextDay !== null) {
                        // Next occurrence is in the same week
                        $nextDate->addDays($nextDay - $currentDay);
                    } else {
                        // Move to the first day of the next week
                        $weeksToAdd = ($this->recurrence_interval ?? 1) - 1;
                        $daysToAdd = (7 - $currentDay) + ($recurrenceDays[0] ?? 0) + ($weeksToAdd * 7);
                        $nextDate->addDays($daysToAdd);
                    }
                } else {
                    // If no specific days are set, just add the interval in weeks
                    $nextDate->addWeeks($this->recurrence_interval ?? 1);
                }
                break;
                
            case self::RECURRENCE_MONTHLY:
                $nextDate->addMonthsNoOverflow($this->recurrence_interval ?? 1);
                break;
                
            case self::RECURRENCE_YEARLY:
                $nextDate->addYears($this->recurrence_interval ?? 1);
                break;
        }
        
        // If we've passed the end date, return null
        if ($this->recurrence_end_date && $nextDate->gt($this->recurrence_end_date)) {
            return null;
        }
        
        return $nextDate;
    }
}
