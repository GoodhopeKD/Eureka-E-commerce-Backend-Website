<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_table',
        'item_id',
        'item_cart_count',
        'pin_type',
        'adder_user_id',
    ];

    /**
     * Get the product associated with the pin.
     */
    public function product()
    {
        return $this->belongsTo( Product::class, 'item_id' );
    }

    /**
     * Get the product associated with the pin.
     */
    public function event()
    {
        return $this->belongsTo( Event::class, 'item_id' );
    }

    /**
     * Get the variations associated with the product.
     */
    public function item_cart_variations()
    {
        return $this->hasMany( ProductVariation::class, 'owner_id')->where('owner_table','pins');
    }
}
