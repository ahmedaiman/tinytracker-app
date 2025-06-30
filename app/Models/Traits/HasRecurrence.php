<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasRecurrence
 * 
 * Provides functionality for handling recurring appointments
 */
trait HasRecurrence
{
    /**
     * Boot the trait
     */
    protected static function bootHasRecurrence()
    {
        static::saving(function ($model) {
            // Set recurrence parent ID for child instances
            if ($model->is_recurring && !$model->recurrence_parent_id) {
                $model->recurrence_parent_id = $model->id;
            }
        });
    }

    /**
     * Check if the appointment is recurring
     *
     * @return bool
     */
    public function getIsRecurringAttribute()
    {
        return !is_null($this->recurrence_pattern) && !is_null($this->recurrence_ends_at);
    }

    /**
     * Get the next occurrence of the recurring appointment
     *
     * @return self|null
     */
    public function getNextOccurrence()
    {
        if (!$this->is_recurring) {
            return null;
        }

        $lastOccurrence = $this->instances()->latest('start_time')->first() ?? $this;
        $nextDate = $this->calculateNextOccurrence($lastOccurrence->start_time);

        if (!$nextDate || ($this->recurrence_ends_at && $nextDate->gt($this->recurrence_ends_at))) {
            return null;
        }

        $nextAppointment = $this->replicate();
        $nextAppointment->recurrence_parent_id = $this->id;
        $nextAppointment->start_time = $nextDate;
        $nextAppointment->end_time = $nextDate->copy()->addMinutes(
            $this->start_time->diffInMinutes($this->end_time)
        );

        return $nextAppointment;
    }

    /**
     * Calculate the next occurrence date based on the recurrence pattern
     *
     * @param Carbon $lastDate
     * @return Carbon|null
     */
    protected function calculateNextOccurrence(Carbon $lastDate)
    {
        $nextDate = clone $lastDate;

        switch ($this->recurrence_pattern) {
            case 'daily':
                $nextDate->addDay();
                break;
            case 'weekly':
                $nextDate->addWeek();
                break;
            case 'biweekly':
                $nextDate->addWeeks(2);
                break;
            case 'monthly':
                $nextDate->addMonth();
                break;
            default:
                return null;
        }

        return $nextDate;
    }

    /**
     * Scope a query to only include recurring appointments
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecurring(Builder $query)
    {
        return $query->whereNotNull('recurrence_pattern')
                    ->whereNotNull('recurrence_ends_at');
    }

    /**
     * Scope a query to only include non-recurring appointments
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotRecurring(Builder $query)
    {
        return $query->whereNull('recurrence_pattern')
                    ->whereNull('recurrence_ends_at');
    }

    /**
     * Get all instances of a recurring appointment
     */
    public function instances()
    {
        return $this->hasMany(self::class, 'recurrence_parent_id')
                   ->where('id', '!=', $this->id);
    }

    /**
     * Get the parent appointment if this is a recurring instance
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'recurrence_parent_id');
    }
}
