<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RandomCheck extends Model
{
    use HasFactory, SoftDeletes;

    // Context constants
    public const CONTEXT_FASTING = 'fasting';
    public const CONTEXT_BEFORE_MEAL = 'before_meal';
    public const CONTEXT_AFTER_MEAL = 'after_meal';
    public const CONTEXT_BEFORE_SLEEP = 'before_sleep';
    public const CONTEXT_NIGHT = 'night';
    public const CONTEXT_OTHER = 'other';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'child_id',
        'user_id',
        'check_time',
        'bg_value',
        'insulin_type_id',
        'insulin_units',
        'insulin_injected_at',
        'context',
        'notes',
        'is_manual_entry',
        'is_high_alert',
        'is_low_alert',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_time' => 'datetime',
        'insulin_injected_at' => 'datetime',
        'bg_value' => 'integer',
        'insulin_units' => 'decimal:2',
        'is_high_alert' => 'boolean',
        'is_low_alert' => 'boolean',
        'is_manual_entry' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'context' => self::CONTEXT_OTHER,
        'is_high_alert' => false,
        'is_low_alert' => false,
        'is_manual_entry' => true,
    ];

    /**
     * Get all available context options.
     *
     * @return array
     */
    public static function getContextOptions(): array
    {
        return [
            self::CONTEXT_FASTING => 'Fasting',
            self::CONTEXT_BEFORE_MEAL => 'Before Meal',
            self::CONTEXT_AFTER_MEAL => 'After Meal',
            self::CONTEXT_BEFORE_SLEEP => 'Before Sleep',
            self::CONTEXT_NIGHT => 'Night',
            self::CONTEXT_OTHER => 'Other',
        ];
    }

    /**
     * Get the child that owns the random check.
     */
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user that created the random check.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the insulin type associated with the random check.
     */
    public function insulinType()
    {
        return $this->belongsTo(InsulinType::class);
    }

    /**
     * Scope a query to only include checks for a specific child.
     */
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    /**
     * Scope a query to only include checks for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return $query->whereDate('check_time', $date);
    }

    /**
     * Scope a query to only include high alerts.
     */
    public function scopeHighAlerts($query)
    {
        return $query->where('is_high_alert', true);
    }

    /**
     * Scope a query to only include low alerts.
     */
    public function scopeLowAlerts($query)
    {
        return $query->where('is_low_alert', true);
    }

    /**
     * Get the formatted check time.
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->check_time->format('h:i A');
    }

    /**
     * Get the human-readable context.
     */
    public function getContextNameAttribute(): string
    {
        return self::getContextOptions()[$this->context] ?? ucfirst($this->context);
    }

    /**
     * Check if the check has insulin data.
     */
    public function hasInsulinData(): bool
    {
        return !is_null($this->insulin_type_id) && !is_null($this->insulin_units);
    }

    /**
     * Determine if the blood glucose level is in the target range.
     * This is a simplified example - adjust the ranges as needed.
     */
    public function isInTargetRange(): bool
    {
        // These are example ranges - adjust based on medical guidelines
        $targetMin = 80;  // mg/dL
        $targetMax = 150; // mg/dL
        
        return $this->blood_glucose >= $targetMin && $this->blood_glucose <= $targetMax;
    }
}
