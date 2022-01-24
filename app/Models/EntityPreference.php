<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityPreference extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_table',
        'entity_id',
        'key',
        'value',
    ];
}
