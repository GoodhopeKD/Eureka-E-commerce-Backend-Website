<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'action',
        'action_user_id',
        'connect_instance_id',
        'thing_table',
        'thing_id',
        'thing_column',
        'update_initial_value',
        'update_final_value',
        'multistep_operation_hash',
        'request_location',
    ];
}
