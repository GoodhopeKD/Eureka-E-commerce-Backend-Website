<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'uri',
        'height',
        'width',
        'title',
        'alt',
        'tag',
        'owner_table',
        'owner_id',
        'adder_user_id',
    ];
}
