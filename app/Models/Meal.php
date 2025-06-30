<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'child_id',
        'user_id',
        'meal_type',
        'meal_time',
        'pre_meal_bg',
        'post_meal_bg',
        'post_meal_bg_time',
        'insulin_type_id',
        'insulin_units',
        'insulin_injected_at',
        'food_description',
        'carbs_grams',
        'sugars_grams',
        'status',
        'is_override',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'meal_time' => 'datetime',
        'post_meal_bg_time' => 'datetime',
        'insulin_injected_at' => 'datetime',
        'pre_meal_bg' => 'integer',
        'post_meal_bg' => 'integer',
        'carbs_grams' => 'integer',
        'sugars_grams' => 'integer',
        'insulin_units' => 'decimal:2',
        'is_override' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending',
        'is_override' => false,
    ];

    /**
     * Get the child that owns the meal.
     */
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user that created the meal record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the insulin type associated with the meal.
     */
    public function insulinType()
    {
        return $this->belongsTo(InsulinType::class);
    }

    /**
     * Determine if the meal is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Determine if the meal is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get the meal type in a human-readable format.
     */
    public function getMealTypeNameAttribute(): string
    {
        return ucfirst($this->meal_type);
    }

    /**
     * Scope a query to only include meals for a specific child.
     */
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    /**
     * Scope a query to only include meals for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return $query->whereDate('meal_time', $date);
    }

    /**
     * Scope a query to only include meals of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('meal_type', $type);
    }

    /**
     * Scope a query to only include completed meals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Mark the meal as completed.
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Calculate the time difference between meal and post-meal BG check.
     *
     * @return int|null Time difference in minutes or null if post-meal BG not set
     */
    public function getPostMealBgTimeDifferenceAttribute(): ?int
    {
        if (!$this->post_meal_bg_time || !$this->meal_time) {
            return null;
        }

        return $this->meal_time->diffInMinutes($this->post_meal_bg_time);
    }
}
