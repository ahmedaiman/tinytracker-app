<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BasalDose extends Model
{
    use HasFactory, SoftDeletes;

    // Common injection sites
    public const SITE_ABDOMEN = 'abdomen';
    public const SITE_THIGH = 'thigh';
    public const SITE_ARM = 'arm';
    public const SITE_BUTTOCK = 'buttock';
    public const SITE_OTHER = 'other';

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
        'injected_at',
        'injection_site',
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
        'injected_at' => 'datetime',
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
     * Get all available injection site options.
     *
     * @return array
     */
    public static function getInjectionSiteOptions(): array
    {
        return [
            self::SITE_ABDOMEN => 'Abdomen',
            self::SITE_THIGH => 'Thigh',
            self::SITE_ARM => 'Arm',
            self::SITE_BUTTOCK => 'Buttock',
            self::SITE_OTHER => 'Other',
        ];
    }

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
     */
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    /**
     * Scope a query to only include doses for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return $query->whereDate('injected_at', $date);
    }

    /**
     * Scope a query to only include correction doses.
     */
    public function scopeCorrectionDoses($query)
    {
        return $query->where('is_correction_dose', true);
    }

    /**
     * Get the formatted injection time.
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->injected_at->format('h:i A');
    }

    /**
     * Get the human-readable injection site.
     */
    public function getInjectionSiteNameAttribute(): string
    {
        return self::getInjectionSiteOptions()[$this->injection_site] ?? ucfirst($this->injection_site);
    }

    /**
     * Get the dose amount with units.
     */
    public function getDoseWithUnitsAttribute(): string
    {
        return "{$this->units} units";
    }

    /**
     * Get the time since the dose was administered.
     *
     * @return string|null
     */
    public function getTimeSinceInjectionAttribute(): ?string
    {
        if (!$this->injected_at) {
            return null;
        }

        return $this->injected_at->diffForHumans(['parts' => 2, 'short' => true]);
    }
}
