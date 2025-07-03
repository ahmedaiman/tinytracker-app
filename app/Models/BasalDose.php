<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BasalDose extends Model
{
    use HasFactory, SoftDeletes;

    // Injection site constants have been removed as the column was dropped

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'child_id',
        'user_id',
        'insulin_type_id',
        'units',
        'injection_date',
        'notes',
        'is_manual_entry',
        'is_correction_dose',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'injection_date' => 'date',
        'units' => 'decimal:2',
        'is_manual_entry' => 'boolean',
        'is_correction_dose' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_manual_entry' => true,
        'is_correction_dose' => false,
    ];

    /**
     * Get the child that received the basal dose.
     */
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user who administered the dose.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the insulin type used for this dose.
     */
    public function insulinType()
    {
        return $this->belongsTo(InsulinType::class);
    }

    /**
     * Scope a query to only include doses for a specific child.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $childId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    /**
     * Scope a query to only include doses for a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|\Carbon\Carbon  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, $date)
    {
        if (! $date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        return $query->whereDate('injection_date', $date->toDateString());
    }

    /**
     * Scope a query to only include correction doses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCorrectionDoses($query)
    {
        return $query->where('is_correction_dose', true);
    }

    /**
     * Get the dose amount with units.
     */
    public function getDoseWithUnitsAttribute(): string
    {
        return number_format($this->units, 2) . ' units';
    }

    /**
     * Get the formatted date of the injection.
     *
     * @return string
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->injection_date->format('M j, Y');
    }

    /**
     * Get the time since the dose was administered.
     *
     * @return string|null
     */
    public function getDaysSinceInjectionAttribute(): ?string
    {
        if (!$this->injection_date) {
            return null;
        }

        return $this->injection_date->diffForHumans(['parts' => 1]);
    }
}
