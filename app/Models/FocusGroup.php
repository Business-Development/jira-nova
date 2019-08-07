<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FocusGroup extends Model
{
    use SoftDeletes;

    //////////////////
    //* Attributes *//
    //////////////////
    /**
     * The table associated to this model.
     *
     * @var string
     */
    protected $table = 'focus_groups';

    /**
     * The attributes that should be casted.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'color' => 'json',
        'criteria' => 'json'
    ];

    /////////////////
    //* Relations *//
    /////////////////
    /**
     * Returns the allocations associated to this focus group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allocations()
    {
        return $this->hasMany(ScheduleFocusAllocation::class, 'focus_group_id');
    }
}