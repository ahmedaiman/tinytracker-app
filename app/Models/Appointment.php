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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'child_id',
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
        'is_all_day',
        'meeting_link',
        'notes',
        'created_by',
        'updated_by',
    ];

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
            'is_all_day' => 'boolean',
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
                    ->whereNotIn('status', [self::STATUS_CANCELLED])
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
        return $query->where('notification_preference', '!=', self::NOTIFICATION_NONE)
                    ->where('status', self::STATUS_SCHEDULED)
                    ->where('start_time', '>', now())
                    ->where(function($q) {
                        $q->doesntHave('reminders')
                          ->orWhereHas('reminders', function($q) {
                              $q->where('status', AppointmentReminder::STATUS_FAILED);
                          });
                    });
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
     * Cancel the appointment.
     */
    public function cancel(string $reason = null): void
    {
        $this->status = self::STATUS_CANCELLED;
        
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n\n" : '') . "Cancelled: " . $reason;
        }
        
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
     * Get the next occurrence of a recurring appointment.
     * Returns null if there are no more occurrences.
     */
    public function getNextOccurrence(): ?self
    {
        if (!$this->recurrence_rule) {
            return null;
        }

        try {
            // Parse the RRULE
            $rrule = new RRule($this->recurrence_rule);
            
            // Get the next occurrence after now
            $nextDate = $rrule->getOccurrencesAfter(now(), true, 1);
            
            if (empty($nextDate)) {
                return null;
            }
            
            $nextDate = $nextDate[0];
            
            // Create a new appointment instance for the next occurrence
            $nextAppointment = $this->replicate();
            $nextAppointment->recurrence_parent_id = $this->id;
            
            // Calculate the duration in minutes between start and end times
            $durationInMinutes = $this->start_time->diffInMinutes($this->end_time);
            
            // Set the start time to the next occurrence
            $nextAppointment->start_time = $nextDate;
            
            // Set the end time by adding the duration to the new start time
            $nextAppointment->end_time = (clone $nextDate)->addMinutes($durationInMinutes);
            
            return $nextAppointment;
            
        } catch (\Exception $e) {
            \Log::error('Error calculating next occurrence: ' . $e->getMessage());
            return null;
        }
    }
}


