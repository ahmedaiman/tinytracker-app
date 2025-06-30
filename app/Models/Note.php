<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Note extends Model
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
        'title',
        'content',
        'type',
        'noted_at',
        'is_important',
        'tags',
        'related_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'noted_at' => 'datetime',
        'is_important' => 'boolean',
        'tags' => 'array',
        'related_data' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'general',
        'is_important' => false,
    ];

    /**
     * Available note types.
     *
     * @var array
     */
    public const TYPES = [
        'general' => 'General Note',
        'medical' => 'Medical Note',
        'diet' => 'Diet Note',
        'exercise' => 'Exercise Note',
        'symptoms' => 'Symptoms Note',
        'other' => 'Other',
    ];

    /**
     * Get the child that the note belongs to.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user who created the note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include notes of a given type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include important notes.
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Scope a query to only include notes for a specific child.
     */
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    /**
     * Scope a query to only include notes within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        $endDate = $endDate ?: $startDate;
        
        return $query->whereBetween('noted_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    /**
     * Add a tag to the note.
     */
    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        
        if (!in_array($tag, $tags, true)) {
            $tags[] = $tag;
            $this->tags = $tags;
        }
    }

    /**
     * Remove a tag from the note.
     */
    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        
        if (($key = array_search($tag, $tags, true)) !== false) {
            unset($tags[$key]);
            $this->tags = array_values($tags); // Reindex array
        }
    }

    /**
     * Check if the note has a specific tag.
     */
    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? [], true);
    }

    /**
     * Get the note's type as a human-readable string.
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get a short excerpt of the note content.
     */
    public function getExcerptAttribute(int $length = 100): string
    {
        $content = strip_tags($this->content);
        
        if (mb_strlen($content) <= $length) {
            return $content;
        }
        
        return mb_substr($content, 0, $length) . '...';
    }
}
