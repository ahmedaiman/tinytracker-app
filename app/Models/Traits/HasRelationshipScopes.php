<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Provides relationship-based query scopes for models.
 */
trait HasRelationshipScopes
{
    /**
     * Eager load common relationships.
     * Override this in your model to specify common relationships.
     */
    public function scopeWithCommon(Builder $query): Builder
    {
        // Default implementation - override in specific models
        $with = [];
        
        // Auto-detect common relationships
        foreach (['user', 'child', 'children', 'appointment'] as $relation) {
            if (method_exists($this, $relation)) {
                $with[] = $relation;
            }
        }
        
        return $with ? $query->with($with) : $query;
    }

    /**
     * Eager load counts for common relationships.
     */
    public function scopeWithCounts(Builder $query, array $relations = null): Builder
    {
        $relations = $relations ?? $this->getCountableRelations();
        return $query->withCount($relations);
    }

    /**
     * Get the default countable relationships for the model.
     */
    protected function getCountableRelations(): array
    {
        $countable = [];
        $relationships = [
            'notes', 'appointments', 'reminders', 'photos',
            'meals', 'snacks', 'randomChecks', 'basalDoses'
        ];
        
        foreach ($relationships as $relation) {
            if (method_exists($this, $relation)) {
                $countable[] = $relation;
            }
        }
        
        return $countable;
    }

    /**
     * Filter by a related model's attribute.
     */
    public function scopeWhereHasRelation(
        Builder $query, 
        string $relation, 
        string $column, 
        $operator = null, 
        $value = null
    ): Builder {
        if (func_num_args() === 3) {
            $value = $operator;
            $operator = '=';
        }
        
        return $query->whereHas($relation, function ($q) use ($column, $operator, $value) {
            $q->where($column, $operator, $value);
        });
    }

    /**
     * Filter by the existence of a relationship.
     */
    public function scopeHasRelation(Builder $query, string $relation, string $operator = '>=', int $count = 1): Builder
    {
        return $query->has($relation, $operator, $count);
    }

    /**
     * Filter by the absence of a relationship.
     */
    public function scopeDoesntHaveRelation(Builder $query, string $relation): Builder
    {
        return $query->doesntHave($relation);
    }

    /**
     * Filter by a morph relationship.
     */
    public function scopeWhereHasMorph(
        Builder $query,
        string $relation,
        $types,
        string $column = null,
        string $operator = null,
        $value = null
    ): Builder {
        if (func_num_args() === 4) {
            $value = $column;
            $column = null;
        }
        
        return $query->whereHasMorph($relation, $types, function ($q) use ($column, $operator, $value) {
            if ($column !== null) {
                $q->where($column, $operator, $value);
            }
        });
    }

    /**
     * Order by a related model's column.
     */
    public function scopeOrderByRelation(
        Builder $query,
        string $relation,
        string $column,
        string $direction = 'asc'
    ): Builder {
        $related = $this->{$relation}();
        
        if ($related instanceof Relation) {
            $relatedTable = $related->getRelated()->getTable();
            $foreignKey = $related->getQualifiedForeignKeyName();
            $ownerKey = $related->getQualifiedOwnerKeyName();
            
            return $query->select($this->getTable().'.*')
                        ->leftJoin($relatedTable, $foreignKey, '=', $ownerKey)
                        ->orderBy("$relatedTable.$column", $direction);
        }
        
        return $query;
    }
}
