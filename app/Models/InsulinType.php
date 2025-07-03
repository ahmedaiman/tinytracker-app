<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsulinType extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'description',
        'onset',
        'peak',
        'duration',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active insulin types.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive insulin types.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get the basal doses associated with this insulin type.
     */
    public function basalDoses()
    {
        return $this->hasMany(\App\Models\BasalDose::class);
    }

    /**
     * Get the meals associated with this insulin type.
     */
    public function meals()
    {
        return $this->hasMany(\App\Models\Meal::class);
    }

    /**
     * Get the snacks associated with this insulin type.
     */
    public function snacks()
    {
        return $this->hasMany(\App\Models\Snack::class);
    }

    /**
     * Get the random checks associated with this insulin type.
     */
    public function randomChecks()
    {
        return $this->hasMany(\App\Models\RandomCheck::class);
    }
}
