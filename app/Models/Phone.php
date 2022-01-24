<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_code',
        'number',
        'tag',
        'owner_table',
        'owner_id',
        'adder_user_id',
    ];
}
