<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Provides health and safety related query scopes.
 */
trait HasHealthScopes
{
    /**
     * Scope a query to only include abnormal blood glucose readings.
     */
    public function scopeAbnormalReadings(Builder $query, ?float $minNormal = null, ?float $maxNormal = null): Builder
    {
        $minNormal = $minNormal ?? config('diabetes.thresholds.bg.min_normal', 70);
        $maxNormal = $maxNormal ?? config('diabetes.thresholds.bg.max_normal', 180);
        
        return $query->where(function($q) use ($minNormal, $maxNormal) {
            $q->where('glucose_value', '<', $minNormal)
              ->orWhere('glucose_value', '>', $maxNormal);
        });
    }

    /**
     * Scope a query to only include critical high readings.
     */
    public function scopeCriticalHigh(Builder $query, ?float $threshold = null): Builder
    {
        $threshold = $threshold ?? config('diabetes.thresholds.bg.critical_high', 250);
        return $query->where('glucose_value', '>', $threshold);
    }

    /**
     * Scope a query to only include critical low readings.
     */
    public function scopeCriticalLow(Builder $query, ?float $threshold = null): Builder
    {
        $threshold = $threshold ?? config('diabetes.thresholds.bg.critical_low', 54);
        return $query->where('glucose_value', '<', $threshold);
    }

    /**
     * Scope a query to only include readings that may indicate hypoglycemia.
     */
    public function scopeHypoglycemic(Builder $query, ?float $threshold = null): Builder
    {
        $threshold = $threshold ?? config('diabetes.thresholds.bg.hypo', 70);
        return $query->where('glucose_value', '<', $threshold);
    }

    /**
     * Scope a query to only include readings that may indicate hyperglycemia.
     */
    public function scopeHyperglycemic(Builder $query, ?float $threshold = null): Builder
    {
        $threshold = $threshold ?? config('diabetes.thresholds.bg.hyper', 180);
        return $query->where('glucose_value', '>', $threshold);
    }

    /**
     * Scope a query to only include readings within target range.
     */
    public function scopeInTargetRange(Builder $query, ?float $min = null, ?float $max = null): Builder
    {
        $min = $min ?? config('diabetes.thresholds.bg.target_min', 80);
        $max = $max ?? config('diabetes.thresholds.bg.target_max', 150);
        
        return $query->whereBetween('glucose_value', [$min, $max]);
    }

    /**
     * Scope a query to only include records with missing required data.
     */
    public function scopeMissingRequiredData(Builder $query): Builder
    {
        $requiredFields = $this->getRequiredFields();
        
        foreach ($requiredFields as $field) {
            $query->whereNull($field);
        }
        
        return $query;
    }

    /**
     * Get the list of required fields for the model.
     * Override this in your model to specify required fields.
     */
    protected function getRequiredFields(): array
    {
        // Default required fields - override in specific models
        return [];
    }

    /**
     * Scope a query to only include records with expired items.
     */
    public function scopeExpired(Builder $query): Builder
    {
        if (in_array('expires_at', $this->getFillable())) {
            return $query->where('expires_at', '<', now());
        }
        
        return $query;
    }

    /**
     * Scope a query to only include records expiring soon.
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        if (in_array('expires_at', $this->getFillable())) {
            return $query->whereBetween('expires_at', [
                now(),
                now()->addDays($days)
            ]);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include records needing refill.
     */
    public function scopeNeedsRefill(Builder $query, float $threshold = 0.2): Builder
    {
        if (in_array('current_quantity', $this->getFillable()) && 
            in_array('initial_quantity', $this->getFillable())) {
            return $query->whereRaw('(current_quantity / initial_quantity) <= ?', [$threshold]);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include records with medication doses outside typical ranges.
     */
    public function scopeAtypicalDosage(Builder $query): Builder
    {
        if (in_array('dose', $this->getFillable()) && 
            in_array('insulin_type_id', $this->getFillable())) {
            // This would need to be customized based on your insulin type configurations
            return $query->where(function($q) {
                $q->where('dose', '>', 10) // Example threshold
                  ->orWhere('dose', '<', 1); // Example threshold
            });
        }
        
        return $query;
    }
}
