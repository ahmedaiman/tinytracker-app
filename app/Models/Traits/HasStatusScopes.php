<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Provides status-based query scopes for models with status fields.
 */
trait HasStatusScopes
{
    /**
     * Scope a query to only include records that need attention.
     * Override this in your model to implement specific attention logic.
     */
    public function scopeNeedsAttention(Builder $query): Builder
    {
        // Default implementation - override in specific models
        if (in_array('needs_attention', $this->getFillable())) {
            return $query->where('needs_attention', true);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include pending records.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed records.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include active records.
     */
    public function scopeActive(Builder $query): Builder
    {
        if (in_array('is_active', $this->getFillable())) {
            return $query->where('is_active', true);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include inactive records.
     */
    public function scopeInactive(Builder $query): Builder
    {
        if (in_array('is_active', $this->getFillable())) {
            return $query->where('is_active', false);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include overdue records.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        if (in_array('due_date', $this->getFillable())) {
            return $query->where('due_date', '<', now())
                        ->where(function($q) {
                            $q->whereNull('completed_at')
                              ->orWhereColumn('completed_at', '>', 'due_date');
                        });
        }
        
        return $query;
    }

    /**
     * Scope a query to only include records due soon.
     */
    public function scopeDueSoon(Builder $query, int $days = 3): Builder
    {
        if (in_array('due_date', $this->getFillable())) {
            return $query->whereBetween('due_date', [
                now(),
                now()->addDays($days)
            ])->whereNull('completed_at');
        }
        
        return $query;
    }

    /**
     * Scope a query to only include records with a specific status.
     */
    public function scopeWithStatus(Builder $query, $status): Builder
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        
        return $query->where('status', $status);
    }

    /**
     * Scope a query to exclude records with a specific status.
     */
    public function scopeWithoutStatus(Builder $query, $status): Builder
    {
        if (is_array($status)) {
            return $query->whereNotIn('status', $status);
        }
        
        return $query->where('status', '!=', $status);
    }
}
