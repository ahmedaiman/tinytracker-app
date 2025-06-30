<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;
use RRule\RRule;
use RRule\RRuleInterface;
use App\Models\Model as BaseModel;
use App\Models\Traits\HasRecurrence;

class Appointment extends BaseModel
{
    use HasFactory, SoftDeletes, HasRecurrence;

    // Appointment types
    public const TYPE_CHECKUP = 'checkup';
    public const TYPE_CONSULTATION = 'consultation';
    public const TYPE_TEST = 'test';
    public const TYPE_FOLLOW_UP = 'follow_up';
    public const TYPE_OTHER = 'other';

    // Appointment status constants
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NOSHOW = 'no_show';

    // Appointment type constants
    public const TYPE_EMERGENCY = 'emergency';
    public const TYPE_FOLLOWUP = 'followup';
    public const TYPE_ROUTINE = 'routine';
    public const TYPE_VACCINATION = 'vaccination';

    // Notification preference constants
    public const NOTIFICATION_EMAIL = 'email';
    public const NOTIFICATION_SMS = 'sms';
    public const NOTIFICATION_PUSH = 'push';
    public const NOTIFICATION_NONE = 'none';

    // Recurrence patterns
    public const RECURRENCE_NONE = null;
    public const RECURRENCE_DAILY = 'daily';
    public const RECURRENCE_WEEKLY = 'weekly';
    public const RECURRENCE_MONTHLY = 'monthly';
    public const RECURRENCE_YEARLY = 'yearly';

    /**
     * Get all available appointment types with their display names.
     *
     * @return array
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_CHECKUP => 'Checkup',
            self::TYPE_CONSULTATION => 'Consultation',
            self::TYPE_TEST => 'Test',
            self::TYPE_FOLLOW_UP => 'Follow Up',
            self::TYPE_OTHER => 'Other',
            self::TYPE_EMERGENCY => 'Emergency',
            self::TYPE_FOLLOWUP => 'Follow-up',
            self::TYPE_ROUTINE => 'Routine',
            self::TYPE_VACCINATION => 'Vaccination',
        ];
    }

    /**
     * Get all available appointment statuses with their display names.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_NOSHOW => 'No Show',
        ];
    }

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
        'start_time',
        'end_time',
        'location',
        'type',
        'status',
        'recurrence_rule',
        'recurrence_end_date',
        'recurrence_parent_id',
        'recurrence_exdates',
        'notification_preference',
        'reminder_minutes_before',
        'color',
        'all_day',
        'meeting_link',
        'notes',
        'created_by',
        'updated_by',
        'metadata',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure metadata is always an array when setting it
        static::saving(function ($model) {
            if (is_null($model->metadata)) {
                $model->metadata = [];
            } elseif (is_string($model->metadata)) {
                $model->metadata = json_decode($model->metadata, true) ?: [];
            } elseif (!is_array($model->metadata)) {
                $model->metadata = (array) $model->metadata;
            }
        });

        // Ensure metadata is properly cast to array after retrieving
        static::retrieved(function ($model) {
            if (is_string($model->metadata)) {
                $model->metadata = json_decode($model->metadata, true) ?: [];
            } elseif (is_null($model->metadata)) {
                $model->metadata = [];
            }
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'recurrence_end_date' => 'date',
            'recurrence_exdates' => 'array',
            'recurrence_days' => 'array',
            'metadata' => 'array',
            'is_all_day' => 'boolean',
            'reminder_sent' => 'boolean',
            'telegram_notification_sent' => 'boolean',
        ]);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'is_recurring',
        'is_cancelled',
        'is_completed',
        'duration_minutes',
        'is_upcoming',
    ];

    /**
     * Get the child that owns the appointment.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class)->withDefault();
    }
    
    /**
     * Get the user (guardian) that owns the appointment.
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
     * Get the user who created the appointment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the appointment.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the reminders for the appointment.
     */
    public function reminders()
    {
        return $this->hasMany(AppointmentReminder::class);
    }

    /**
     * Get the parent appointment for recurring instances.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'recurrence_parent_id');
    }

    /**
     * Get the child appointments for recurring instances.
     */
    public function instances()
    {
        return $this->hasMany(self::class, 'recurrence_parent_id');
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('start_time', '>=', now())
                    ->where('start_time', '<=', now()->addDays($days))
                    ->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_COMPLETED])
                    ->orderBy('start_time');
    }

    /**
     * Scope a query to only include past appointments.
     */
    public function scopePast($query, $days = 30)
    {
        return $query->where('start_time', '<', now())
                    ->where('start_time', '>=', now()->subDays($days))
                    ->orderBy('start_time', 'desc');
    }

    /**
     * Scope a query to only include appointments for today.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereDate('start_time', today())
                    ->whereNotIn('status', [self::STATUS_CANCELLED])
                    ->orderBy('start_time');
    }

    /**
     * Scope a query to only include appointments for a specific child.
     */
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    /**
     * Scope a query to only include appointments of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include appointments with a specific status.
     */
    /**
     * Scope a query to only include appointments with a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|array  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus(\Illuminate\Database\Eloquent\Builder $query, $status): \Illuminate\Database\Eloquent\Builder
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include recurring appointments.
     */
    public function scopeRecurring($query)
    {
        return $query->whereNotNull('recurrence_rule');
    }

    /**
     * Scope a query to only include non-recurring appointments.
     */
    public function scopeNotRecurring($query)
    {
        return $query->whereNull('recurrence_rule');
    }

    /**
     * Scope a query to only include appointments that need reminders.
     */
    public function scopeNeedsReminder($query)
    {
        return $query->where('reminder_sent', false)
                    ->where('status', self::STATUS_CONFIRMED)
                    ->where('start_time', '>', now())
                    ->where('start_time', '<=', now()->addHours(24));
    }
    
    /**
     * Scope a query to only include appointments between the given dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|\Carbon\Carbon  $startDate
     * @param  string|\Carbon\Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        $startDate = $startDate instanceof \Carbon\Carbon ? $startDate : \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDate = $endDate instanceof \Carbon\Carbon ? $endDate : \Carbon\Carbon::parse($endDate)->endOfDay();
        
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }
    
    /**
     * Check if the appointment is upcoming.
     * Alias for is_upcoming attribute for test compatibility.
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return $this->is_upcoming;
    }

    /**
     * Get the is_recurring attribute.
     */
    public function getIsRecurringAttribute(): bool
    {
        return !empty($this->recurrence_rule);
    }

    /**
     * Get the is_cancelled attribute.
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Get the is_completed attribute.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get the duration_minutes attribute.
     */
    public function getDurationMinutesAttribute(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Get the is_upcoming attribute.
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_time->isFuture() && 
               !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED]);
    }

    /**
     * Check if the appointment is happening now.
     */
    public function isHappeningNow(): bool
    {
        $now = now();
        return $now->between($this->start_time, $this->end_time);
    }

    /**
     * Check if the appointment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->end_time->isPast() && 
               !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED]);
    }

    /**
     * Confirm the appointment.
     *
     * @return $this
     */
    public function confirm()
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->save();
        
        return $this;
    }

    /**
     * Cancel the appointment.
     *
     * @param string|null $reason
     * @return $this
     */
    public function cancel(?string $reason = null)
    {
        $this->status = self::STATUS_CANCELLED;
        
        // Ensure metadata is an array before merging
        $metadata = is_array($this->metadata) ? $this->metadata : [];
        $this->metadata = array_merge($metadata, [
            'cancellation_reason' => $reason,
            'cancelled_at' => now()->toDateTimeString(),
        ]);
        
        $this->save();
        
        // Cancel any pending reminders
        $this->reminders()
             ->where('status', AppointmentReminder::STATUS_PENDING)
             ->update(['status' => AppointmentReminder::STATUS_CANCELLED]);
    }

    /**
     * Mark the appointment as completed.
     */
    public function markAsCompleted(string $notes = null): void
    {
        $this->status = self::STATUS_COMPLETED;
        
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n\n" : '') . $notes;
        }
        
        $this->save();
    }

    /**
     * Alias for markAsCompleted for backward compatibility with tests.
     */
    public function complete(string $notes = null): void
    {
        $this->markAsCompleted($notes);
    }

    /**
     * Get all instances of a recurring appointment within a date range.
     */
    public function getInstancesBetween(Carbon $start, Carbon $end): array
    {
        if (!$this->is_recurring) {
            return [$this];
        }

        $instances = [];
        $current = $this;

        while ($current && $current->start_time < $end) {
            if ($current->start_time >= $start) {
                $instances[] = $current;
            }
            $current = $current->getNextOccurrence();
        }

        return $instances;
    }

    /**
     * Create reminders based on the appointment's notification preferences.
     */
    public function createReminders(): void
    {
        if ($this->notification_preference === self::NOTIFICATION_NONE || 
            $this->status === self::STATUS_CANCELLED ||
            $this->start_time->isPast()) {
            return;
        }

        $reminderTimes = $this->calculateReminderTimes();
        
        foreach ($reminderTimes as $minutesBefore => $reminderTime) {
            $this->reminders()->create([
                'reminder_time' => $reminderTime,
                'minutes_before' => $minutesBefore,
                'status' => AppointmentReminder::STATUS_PENDING,
            ]);
        }
    }

    /**
     * Calculate the reminder times based on the notification preference.
     */
    protected function calculateReminderTimes(): array
    {
        $reminderTimes = [];
        
        // Default reminder times in minutes before the appointment
        $defaultReminders = [1440, 60, 15]; // 1 day, 1 hour, 15 minutes
        
        // If specific minutes_before is set, use that
        if ($this->reminder_minutes_before) {
            $defaultReminders = [$this->reminder_minutes_before];
        }
        
        foreach ($defaultReminders as $minutes) {
            $reminderTime = $this->start_time->copy()->subMinutes($minutes);
            
            // Only add future reminders
            if ($reminderTime->isFuture()) {
                $reminderTimes[$minutes] = $reminderTime;
            }
        }
        
        return $reminderTimes;
    }

    /**
     * Check if the appointment is recurring (alias for is_recurring attribute for test compatibility)
     *
     * @return bool
     */
    public function isRecurring(): bool
    {
        return $this->is_recurring;
    }

    /**
     * Mark the telegram notification as sent
     * 
     * @return $this
     */
    public function markTelegramNotificationAsSent()
    {
        $this->telegram_notification_sent = true;
        $this->telegram_notification_sent_at = now();
        $this->save();
        
        // Refresh the model to ensure we have the latest data
        $this->refresh();
        
        return $this;
    }
    
    /**
     * Get the next occurrence of a recurring appointment.
     * Returns null if there are no more occurrences.
     */
    public function getNextOccurrence(): ?self
    {
        if (!$this->recurrence_pattern) {
            return null;
        }

        try {
            $now = now();
            $startTime = $this->start_time;
            $endTime = $this->end_time;
            $durationInMinutes = $startTime->diffInMinutes($endTime);
            
            // Handle different recurrence patterns
            switch ($this->recurrence_pattern) {
                case 'weekly':
                    $nextDate = $this->calculateNextWeeklyOccurrence($now, $startTime);
                    break;
                // Add other recurrence patterns as needed
                default:
                    return null;
            }
            
            // If no next date found or it's after the recurrence end date, return null
            if (!$nextDate || ($this->recurrence_end_date && $nextDate->gt($this->recurrence_end_date))) {
                return null;
            }
            
            // Create a clone of the appointment for the next occurrence
            $nextAppointment = clone $this;
            
            // Set the start and end times for the next occurrence
            $nextAppointment->start_time = $nextDate;
            $nextAppointment->end_time = $nextDate->copy()->addMinutes($durationInMinutes);
            
            return $nextAppointment;
            
        } catch (\Exception $e) {
            \Log::error('Error calculating next occurrence: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calculate the next weekly occurrence based on the current time and start time.
     *
     * @param  \Carbon\Carbon  $now
     * @param  \Carbon\Carbon  $startTime
     * @return \Carbon\Carbon|null
     */
    protected function calculateNextWeeklyOccurrence($now, $startTime)
    {
        $currentDayOfWeek = $now->dayOfWeek;
        $targetDayOfWeek = $startTime->dayOfWeek;
        $nextDate = $now->copy()->setTimeFrom($startTime);
        
        // If today is the target day but the time has passed, or if it's a different day
        if ($currentDayOfWeek === $targetDayOfWeek) {
            if ($nextDate->gt($now)) {
                return $nextDate;
            }
            // Move to next week
            $nextDate->addWeek();
        } else {
            // Move to the next occurrence of the target day
            $daysToAdd = ($targetDayOfWeek - $currentDayOfWeek + 7) % 7;
            $nextDate->addDays($daysToAdd);
        }
        
        // Ensure the time is set correctly
        $nextDate->setTimeFrom($startTime);
        
        return $nextDate;
    }
}
