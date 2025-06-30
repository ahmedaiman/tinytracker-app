<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Provides time-based query scopes for models with timestamp fields.
 */
trait HasTimeScopes
{
    /**
     * Scope a query to only include records from the last N days.
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include records from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope a query to only include records within a date range.
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate = null): Builder
    {
        $endDate = $endDate ?? $startDate;
        
        return $query->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    /**
     * Scope a query to only include records from the current week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include records from the current month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    /**
     * Scope a query to only include records from the current year.
     */
    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfYear(),
            now()->endOfYear()
        ]);
    }

    /**
     * Scope a query to only include records older than the given days.
     */
    public function scopeOlderThanDays(Builder $query, int $days): Builder
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope a query to only include records newer than the given days.
     */
    public function scopeNewerThanDays(Builder $query, int $days): Builder
    {
        return $query->where('created_at', '>', now()->subDays($days));
    }

    /**
     * Scope a query to only include records from a specific hour of the day.
     * Useful for analyzing daily patterns.
     */
    public function scopeByTimeOfDay(Builder $query, int $hour, int $toleranceMinutes = 30): Builder
    {
        $startTime = now()->setTime($hour, 0, 0);
        $endTime = (clone $startTime)->addMinutes($toleranceMinutes);
        
        return $query->whereTime('created_at', '>=', $startTime->toTimeString())
                    ->whereTime('created_at', '<=', $endTime->toTimeString());
    }
}
