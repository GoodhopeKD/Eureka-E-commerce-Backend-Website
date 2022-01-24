<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerServiceChat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_user_id',
        'corresponding_admin_user_id',
        'closed_datetime',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'closed_datetime' => 'datetime',
    ];
}
