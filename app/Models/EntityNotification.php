<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityNotification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_title',
        'message_body',
        'entity_table',
        'entity_id',
        'opened_datetime',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'opened_datetime' => 'datetime',
    ];
}
