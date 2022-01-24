<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'owner_table',
        'status',
        'description',
        'wilaya',
        'commune',
        'owner_user_id',
    ];

    /**
     * Get the phones associated with the user.
     */
    public function phones()
    {
        return $this->hasMany(Phone::class, 'owner_id')->where('owner_table','stores');
    }

    /**
     * Get the banner_image associated with the user.
     */
    public function banner_image()
    {
        return $this->hasOne(Image::class, 'owner_id' )->where(['owner_table'=>'stores','tag'=>'store_banner']);
    }

    /**
     * Get the owner associated with the product.
     */
    public function owner()
    {
        return $this->belongsTo( User::class, 'owner_user_id' );
    }

    /**
     * Get the follow_instances associated with the user.
     */
    public function follow_instances()
    {
        return $this->hasMany(FollowInstance::class, 'followed_store_id');
    }

    /**
     * Get the log_items associated with the user.
     */
    public function log_items()
    {
        return $this->hasMany(LogItem::class, 'thing_id')->where('thing_table','stores');
    }

    /**
     * Get the reviews associated with the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewed_thing_id')->where('reviewed_thing_table','stores');
    }

    /**
     * Get the log_items associated with the user.
     */
    public function social_links()
    {
        return $this->hasMany(SocialLink::class, 'owner_id')->where('owner_table','stores');
    }
    
    /**
     * Get the notifications associated with the user.
     */
    public function notifications()
    {
        return $this->hasMany(EntityNotification::class , 'entity_id' )->where('entity_table','stores');
    }

    /**
     * Get the preferences associated with the user.
     */
    public function preferences()
    {
        return $this->hasMany(EntityPreference::class , 'entity_id' )->where('entity_table','stores');
    }
}
