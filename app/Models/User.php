<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use HasApiTokens;

    // Role constants
    public const ROLE_GUARDIAN = 'guardian';
    public const ROLE_DOCTOR = 'doctor';
    public const ROLE_ADMIN = 'admin';
    
    // Available roles
    public static array $roles = [
        self::ROLE_GUARDIAN => 'Guardian',
        self::ROLE_DOCTOR => 'Doctor',
        self::ROLE_ADMIN => 'Administrator',
    ];

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];
    
    protected $attributes = [
        'role' => self::ROLE_GUARDIAN,
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'is_admin',
        'is_doctor',
        'is_guardian',
    ];

    /**
     * Get the children that belong to the user.
     */
    public function children()
    {
        return $this->hasMany(Child::class);
    }

    /**
     * Check if the user has the admin role.
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if the user has the doctor role.
     */
    public function getIsDoctorAttribute(): bool
    {
        return $this->role === self::ROLE_DOCTOR;
    }

    /**
     * Check if the user has the guardian role.
     */
    public function getIsGuardianAttribute(): bool
    {
        return $this->role === self::ROLE_GUARDIAN;
    }

    /**
     * Get the user's role name.
     */
    public function getRoleNameAttribute(): string
    {
        return self::$roles[$this->role] ?? 'Unknown';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
