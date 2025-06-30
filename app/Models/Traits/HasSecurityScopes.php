<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Provides security-related query scopes for models.
 */
trait HasSecurityScopes
{
    /**
     * Scope a query to only include records for the current user.
     */
    public function scopeForCurrentUser(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    /**
     * Scope a query to only include records for a specific user's children.
     */
    public function scopeForUserChildren(Builder $query, ?int $userId = null): Builder
    {
        $userId = $userId ?? auth()->id();
        
        // If the model has a direct user_id relationship
        if (in_array('user_id', $this->getFillable())) {
            return $query->where('user_id', $userId);
        }
        
        // For models that belong to a child
        if (method_exists($this, 'child') || in_array('child_id', $this->getFillable())) {
            return $query->whereHas('child', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        
        return $query;
    }

    /**
     * Scope a query to only include records visible to the current user.
     * This is a more comprehensive check that considers user roles.
     */
    public function scopeVisibleToCurrentUser(Builder $query): Builder
    {
        $user = auth()->user();
        
        if (!$user) {
            return $query->where('id', 0); // Return empty result if not authenticated
        }
        
        // Admins can see everything
        if ($user->is_admin) {
            return $query;
        }
        
        // For doctors, show records of their patients
        if ($user->is_doctor) {
            // This assumes there's a way to determine which patients a doctor has access to
            // You'll need to implement this based on your application's logic
            return $query->whereHas('child', function($q) use ($user) {
                $q->whereIn('user_id', $user->patients()->pluck('id'));
            });
        }
        
        // For guardians, only show their own children's records
        return $query->forUserChildren();
    }
}
