<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountInstance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'details',
        'amount',
        'order_id',
    ];
}
