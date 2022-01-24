<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category_id',
        'details',
        'entry_type',
        'price',
        'condition',
        'commune',
        'wilaya',
        'seller_table',
        'seller_id',
        'stock_available',
        'reference',
        'adder_user_id',
        'intermediary_admin_user_id',
        'confirmation_datetime',
        'is_seller_pinned',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'confirmation_datetime' => 'datetime',
    ];

    /*
    *   belongsTo : This->key_shown = Related->id
    *   hasOne : Related->key_shown = This->id
    */

    /**
     * Get the category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo( ProductCategory::class, 'category_id' );
    }

    /**
     * Get the seller associated with the product.
     */
    public function seller_user()
    {
        return $this->belongsTo( User::class, 'seller_id' );
    }
    public function seller_store()
    {
        return $this->belongsTo( Store::class, 'seller_id' );
    }

    /**
     * Get the adder associated with the product.
     */
    public function adder()
    {
        return $this->belongsTo( User::class, 'adder_user_id' );
    }

    /**
     * Get the intermediary_admin associated with the product.
     */
    public function intermediary_admin()
    {
        return $this->belongsTo( User::class, 'intermediary_admin_user_id' );
    }

    /**
     * Get the reviews associated with the product.
     */
    public function reviews()
    {
        return $this->hasMany( Review::class, 'reviewed_thing_id')->where('reviewed_thing_table','products');
    }

    /**
     * Get the images associated with the product.
     */
    public function images()
    {
        return $this->hasMany( Image::class, 'owner_id')->where('owner_table','products');
    }

    /**
     * Get the variations associated with the product.
     */
    public function variations()
    {
        return $this->hasMany( ProductVariation::class, 'owner_id')->where('owner_table','products');
    }

    /**
     * Get the log_items associated with the product.
     */
    public function log_items()
    {
        return $this->hasMany( LogItem::class, 'thing_id' )->where('thing_table','products');
    }
}
