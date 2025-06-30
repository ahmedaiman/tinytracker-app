<?php

namespace App\Models;

use App\Models\Traits\HasHealthScopes;
use App\Models\Traits\HasPerformanceScopes;
use App\Models\Traits\HasRelationshipScopes;
use App\Models\Traits\HasSecurityScopes;
use App\Models\Traits\HasStatusScopes;
use App\Models\Traits\HasTimeScopes;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use HasHealthScopes,
        HasPerformanceScopes,
        HasRelationshipScopes,
        HasSecurityScopes,
        HasStatusScopes,
        HasTimeScopes;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? str_plural(snake_case(class_basename($this)));
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return snake_case(class_basename($this)) . '_id';
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        return $this->fillable ?? [];
    }
}
