<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Child extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $appends = ['age', 'age_in_months'];

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
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user that owns the child.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photos for the child.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class)->orderBy('taken_at', 'desc');
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
