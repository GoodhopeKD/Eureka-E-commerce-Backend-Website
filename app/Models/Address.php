<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'surname',
        'name_s',
        'address_line_one',
        'address_line_two',
        'postal_code',
        'commune',
        'wilaya',
        'owner_table',
        'owner_id',
        'adder_user_id',
    ];
}
