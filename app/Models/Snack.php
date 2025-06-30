<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Snack extends Model
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
        'snack_time',
        'food_description',
        'carbs_grams',
        'sugars_grams',
        'pre_snack_bg',
        'post_snack_bg',
        'post_snack_bg_time',
        'insulin_type_id',
        'insulin_units',
        'insulin_injected_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'snack_time' => 'datetime',
        'post_snack_bg_time' => 'datetime',
        'insulin_injected_at' => 'datetime',
        'pre_snack_bg' => 'integer',
        'post_snack_bg' => 'integer',
        'carbs_grams' => 'integer',
        'sugars_grams' => 'integer',
        'insulin_units' => 'decimal:2',
    ];

    /**
     * Get the child that owns the snack.
     */
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user that created the snack record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the insulin type associated with the snack.
     */
    public function insulinType()
    {
        return $this->belongsTo(InsulinType::class);
    }

    /**
     * Scope a query to only include snacks for a specific child.
     */
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    /**
     * Scope a query to only include snacks for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return $query->whereDate('snack_time', $date);
    }

    /**
     * Get the formatted snack time.
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->snack_time->format('h:i A');
    }

    /**
     * Check if the snack has blood glucose data.
     */
    public function hasBloodGlucoseData(): bool
    {
        return !is_null($this->pre_snack_bg) || !is_null($this->post_snack_bg);
    }

    /**
     * Check if the snack has insulin data.
     */
    public function hasInsulinData(): bool
    {
        return !is_null($this->insulin_type_id) && !is_null($this->insulin_units);
    }

    /**
     * Get the time difference between snack and post-snack BG check.
     *
     * @return int|null Time difference in minutes or null if post-snack BG not set
     */
    public function getPostSnackBgTimeDifferenceAttribute(): ?int
    {
        if (!$this->post_snack_bg_time || !$this->snack_time) {
            return null;
        }

        return $this->snack_time->diffInMinutes($this->post_snack_bg_time);
    }
}
