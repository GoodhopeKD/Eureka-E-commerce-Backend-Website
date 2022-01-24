<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowInstance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'follower_user_id',
        'followed_store_id',
    ];

    /**
     * Get the owner associated with the product.
     */
    public function follower()
    {
        return $this->belongsTo( User::class, 'follower_user_id' );
    }

    /**
     * Get the owner associated with the product.
     */
    public function followed_store()
    {
        return $this->belongsTo( Store::class, 'followed_store_id' );
    }
}
