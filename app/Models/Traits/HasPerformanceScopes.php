<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Provides performance-optimized query scopes.
 */
trait HasPerformanceScopes
{
    /**
     * Select only the essential columns for list views.
     */
    public function scopeLightweight(Builder $query): Builder
    {
        $columns = $this->getLightweightColumns();
        return $query->select($columns);
    }

    /**
     * Get the list of lightweight columns for the model.
     */
    protected function getLightweightColumns(): array
    {
        // Default lightweight columns - override in specific models
        $defaults = ['id', 'name', 'created_at', 'updated_at'];
        
        // Only include columns that exist in the table
        return array_intersect($defaults, $this->getFillable());
    }

    /**
     * Use a more efficient query for counting.
     */
    public function scopeFastCount(Builder $query): int
    {
        // For large tables, use an approximate count when available
        if (config('database.default') === 'mysql') {
            $result = DB::select(
                "EXPLAIN SELECT COUNT(*) as count FROM `{$this->getTable()}`"
            );
            
            if (!empty($result) && isset($result[0]->rows)) {
                return (int) $result[0]->rows;
            }
        }
        
        // Fall back to regular count
        return $query->count();
    }

    /**
     * Use cursor for memory-efficient iteration.
     */
    public function scopeCursorForOptimizedIteration(Builder $query, int $chunkSize = 1000): \Generator
    {
        $query->chunk($chunkSize, function ($records) {
            foreach ($records as $record) {
                yield $record;
            }
        });
    }

    /**
     * Eager load only the most recent related record.
     */
    public function scopeWithLatest(Builder $query, string $relation): Builder
    {
        return $query->with([$relation => function ($q) {
            $q->latest()->limit(1);
        }]);
    }

    /**
     * Eager load only the oldest related record.
     */
    public function scopeWithOldest(Builder $query, string $relation): Builder
    {
        return $query->with([$relation => function ($q) {
            $q->oldest()->limit(1);
        }]);
    }

    /**
     * Disable eager loading for the query.
     */
    public function scopeWithoutEagerLoads(Builder $query): Builder
    {
        return $query->setEagerLoads([]);
    }

    /**
     * Force index hint for the query.
     */
    public function scopeForceIndex(Builder $query, string $index): Builder
    {
        if (config('database.default') === 'mysql') {
            $table = $this->getTable();
            return $query->from(DB::raw("`{$table}` FORCE INDEX (`{$index}`)"));
        }
        
        return $query;
    }

    /**
     * Use a covering index for the query.
     */
    public function scopeCoveringIndex(Builder $query, array $columns): Builder
    {
        $table = $this->getTable();
        $select = [];
        
        foreach ($columns as $column) {
            $select[] = "`{$table}`.`{$column}`";
        }
        
        return $query->select(DB::raw(implode(', ', $select)));
    }

    /**
     * Optimize the query for a large dataset.
     */
    public function scopeOptimizeForLargeDataset(Builder $query): Builder
    {
        if (config('database.default') === 'mysql') {
            return $query->from(DB::raw(
                "`{$this->getTable()}` STRAIGHT_JOIN"
            ));
        }
        
        return $query;
    }
}
