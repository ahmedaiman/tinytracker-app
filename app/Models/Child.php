<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model as BaseModel;
use Illuminate\Support\Facades\Storage;

class Child extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'age',
        'age_in_months',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'date_of_birth',
        'gender',
        'notes',
        'current_profile_photo_path',
    ];

    /**
     * Calculate the child's current age.
     */
    public function getAgeAttribute(): array
    {
        if (!$this->date_of_birth) {
            return [
                'years' => null,
                'months' => null,
                'days' => null,
                'display' => 'N/A',
            ];
        }
        
        $birthday = Carbon::parse($this->date_of_birth);
        $now = Carbon::now();
        
        $years = $now->diffInYears($birthday);
        $birthday = $birthday->addYears($years);
        
        $months = $now->diffInMonths($birthday);
        $birthday = $birthday->addMonths($months);
        
        $days = $now->diffInDays($birthday);
        
        return [
            'years' => $years,
            'months' => $months % 12,
            'days' => $days,
            'display' => "{$years}y {$months}m {$days}d",
        ];
    }

    /**
     * Get the child's age in months.
     */
    public function getAgeInMonthsAttribute(): ?int
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->diffInMonths(now()) : null;
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'date_of_birth' => 'date',
        ]);
    }

    /**
     * Get the user that owns the child.
     */
    /**
     * Get the user that owns the child.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the child's upcoming appointments.
     */
    public function upcomingAppointments()
    {
        return $this->hasMany(Appointment::class)
            ->where('start_time', '>=', now())
            ->orderBy('start_time');
    }

    /**
     * Get the child's recent blood glucose readings.
     */
    public function recentReadings($limit = 10)
    {
        return $this->hasMany(RandomCheck::class)
            ->latest()
            ->limit($limit);
    }

    /**
     * Get the child's recent meals.
     */
    public function recentMeals($limit = 10)
    {
        return $this->hasMany(Meal::class)
            ->latest()
            ->limit($limit);
    }

    /**
     * Get the child's recent insulin doses.
     */
    public function recentInsulinDoses($limit = 10)
    {
        return $this->hasMany(BasalDose::class)
            ->with('insulinType')
            ->latest()
            ->limit($limit);
    }

    /**
     * Scope a query to only include children with upcoming appointments.
     */
    public function scopeWithUpcomingAppointments($query, $days = 7)
    {
        return $query->whereHas('appointments', function($q) use ($days) {
            $q->whereBetween('start_time', [now(), now()->addDays($days)]);
        });
    }

    /**
     * Scope a query to only include children with recent abnormal readings.
     */
    public function scopeWithAbnormalReadings($query, $days = 7)
    {
        return $query->whereHas('randomChecks', function($q) use ($days) {
            $q->where('created_at', '>=', now()->subDays($days))
              ->where(function($q) {
                  $q->where('glucose_value', '<', config('diabetes.thresholds.bg.hypo', 70))
                    ->orWhere('glucose_value', '>', config('diabetes.thresholds.bg.hyper', 180));
              });
        });
    }

    /**
     * Scope a query to only include children with recent notes.
     */
    public function scopeWithRecentNotes($query, $days = 7)
    {
        return $query->whereHas('notes', function($q) use ($days) {
            $q->where('created_at', '>=', now()->subDays($days));
        });
    }

    /**
     * Scope a query to order children by most recently active.
     */
    public function scopeMostRecentlyActive($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * Get the photos for the child.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class)->orderBy('taken_at', 'desc');
    }

    /**
     * Get the appointments for the child.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class)->orderBy('start_time');
    }

    /**
     * Get the random blood glucose checks for the child.
     */
    public function randomChecks()
    {
        return $this->hasMany(RandomCheck::class)->orderBy('checked_at', 'desc');
    }

    /**
     * Get the meals for the child.
     */
    public function meals()
    {
        return $this->hasMany(Meal::class)->orderBy('consumed_at', 'desc');
    }

    /**
     * Get the snacks for the child.
     */
    public function snacks()
    {
        return $this->hasMany(Snack::class)->orderBy('consumed_at', 'desc');
    }

    /**
     * Get the basal insulin doses for the child.
     */
    public function basalDoses()
    {
        return $this->hasMany(BasalDose::class)->orderBy('administered_at', 'desc');
    }

    /**
     * Get the notes for the child.
     */
    public function notes()
    {
        return $this->hasMany(Note::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the child's profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->current_profile_photo_path
            ? Storage::url($this->current_profile_photo_path)
            : $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }



    /**
     * Scope a query to only include children for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
