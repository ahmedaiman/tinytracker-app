<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsulinType extends Model
{
    use HasFactory;
    
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
}
