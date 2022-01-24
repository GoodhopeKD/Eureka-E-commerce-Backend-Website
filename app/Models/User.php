<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'surname',
        'name_s',
        'username',
        'gender',
        'account_type',
        'account_level',
        'account_status',
        'account_account_verified_datetime',
        'loyalty_points',
        'referral_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_datetime' => 'datetime',
        'account_verified_datetime' => 'datetime',
    ];

    
    /**
     * Get the profile_image associated with the user.
     */
    public function profile_image()
    {
        return $this->hasOne(Image::class, 'owner_id' )->where('owner_table','users');
    }

    /**
     * Get the phones associated with the user.
     */
    public function phones()
    {
        return $this->hasMany(Phone::class, 'owner_id')->where('owner_table','users');
    }

    /**
     * Get the address associated with the user.
     */
    public function address()
    {
        return $this->hasOne(Address::class, 'owner_id' )->where('owner_table','users');
    }

    /**
     * Get the referral_code_use_instances associated with the user.
     */
    public function referral_code_use_instances()
    {
        return $this->hasMany(DiscountInstance::class , 'code', 'referral_code' );
    }

    /**
     * Get the admin_extension associated with the user.
     */
    public function admin_extension()
    {
        return $this->hasOne(Admin::class, 'user_id' );
    }

    /**
     * Get the pins associated with the user.
     */
    public function pins()
    {
        return $this->hasMany(Pin::class, 'adder_user_id');
    }

    /**
     * Get the search_terms associated with the user.
     */
    public function search_terms()
    {
        return $this->hasMany(SearchTerm::class, 'user_id');
    }

    /**
     * Get the follow_instances associated with the user.
     */
    public function follow_instances()
    {
        return $this->hasMany(FollowInstance::class, 'follower_user_id');
    }

    /**
     * Get the store_owned associated with the user.
     */
    public function store_owned()
    {
        return $this->hasOne(Store::class, 'owner_user_id' );
    }

    /**
     * Get the notifications associated with the user.
     */
    public function notifications()
    {
        return $this->hasMany(EntityNotification::class , 'entity_id' )->where('entity_table','users');
    }

    /**
     * Get the preferences associated with the user.
     */
    public function preferences()
    {
        return $this->hasMany(EntityPreference::class , 'entity_id' )->where('entity_table','users');
    }

    /**
     * Get the connect_instances associated with the user.
     */
    public function connect_instances()
    {
        return $this->hasMany(ConnectInstance::class);
    }

    /**
     * Get the log_items associated with the user.
     */
    public function log_items()
    {
        return $this->hasMany(LogItem::class, 'thing_id')->where('thing_table','users');
    }

    /**
     * Get the placed_orders associated with the user.
     */
    public function placed_orders()
    {
        return $this->hasMany(Order::class, 'placer_user_id');
    }

    /**
     * Get the seller_received_orders associated with the user.
     */
    public function seller_received_orders()
    {
        return $this->hasMany(Order::class, 'seller_id')->where('seller_table','users');
    }

    /**
     * Get the seller_products associated with the user.
     */
    public function seller_products()
    {
        return $this->hasMany(Product::class, 'seller_id')->where('seller_table','users');
    }

    /**
     * Get the seller_reviews associated with the user.
     */
    public function seller_reviews()
    {
        return $this->hasMany(Review::class, 'reviewed_thing_id')->where('reviewed_thing_table','users');
    }
}
