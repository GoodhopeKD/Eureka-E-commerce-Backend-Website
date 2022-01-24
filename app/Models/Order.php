<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference',
        'placer_user_id',
        'seller_table',
        'seller_id',
        'product_id',
        'product_count',
        'delivery_fee',
        'delivery_fee_set_datetime',
        'estimated_delivery_datetime',
        'amount_due_provisional',
        'discount_code',
        'discount_amount',
        'amount_due_final',
        'payment_method',
        'payment_made_datetime',
        'payment_confirmation_datetime',
        'intermediary_admin_user_id',
        'status',
        'delivered_datetime',
        'completed_datetime',
        'visible_to_seller',
        'visible_to_placer',
        'visible_to_admin',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'delivery_fee_set_datetime' => 'datetime',
        'payment_made_datetime' => 'datetime',
        'payment_confirmation_datetime' => 'datetime',
        'closed_datetime' => 'datetime',
    ];

    /**
     * Get the placer associated with the order.
     */
    public function placer()
    {
        return $this->belongsTo( User::class, 'placer_user_id' );
    }

    /**
     * Get the intermediary_admin associated with the order.
     */
    public function intermediary_admin()
    {
        return $this->belongsTo( User::class, 'intermediary_admin_user_id' );
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
     * Get the product associated with the order.
     */
    public function product()
    {
        return $this->belongsTo( Product::class, 'product_id' );
    }

    /**
     * Get the variations associated with the product.
     */
    public function product_variations()
    {
        return $this->hasMany( ProductVariation::class, 'owner_id')->where('owner_table','orders');
    }

    /**
     * Get the discount_instance associated with the user.
     */
    public function discount_instance()
    {
        return $this->hasOne(DiscountInstance::class, 'order_id' );
    }

    /**
     * Get the post_payment_receipt associated with the user.
     */
    public function post_payment_receipt()
    {
        return $this->hasOne(Image::class, 'owner_id' )->where('owner_table','orders');
    }

    /**
     * Get the delivery_address associated with the user.
     */
    public function delivery_address()
    {
        return $this->hasOne(Address::class, 'owner_id' )->where('owner_table','orders');
    }

    /**
     * Get the delivery_phone associated with the user.
     */
    public function delivery_phone()
    {
        return $this->hasOne(Phone::class, 'owner_id' )->where('owner_table','orders');
    }
}
