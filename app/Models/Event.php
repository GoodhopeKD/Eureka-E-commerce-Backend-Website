<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference',
        'title',
        'description',
        'venue',
        'event_datetime',
        'utc_offset',
        'contact_details',
        'other_details',
        'adder_admin_user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_datetime' => 'datetime',
    ];

    /**
     * Get the user associated with the admin.
     */
    public function event_poster()
    {
        return $this->hasOne(Image::class, 'owner_id' )->where(['tag'=>'event_poster','owner_table'=>'events']);
    }

     /**
     * Get the user associated with the admin.
     */
    public function adder_admin()
    {
        return $this->belongsTo(User::class, 'adder_admin_user_id' );
    }

    /**
     * Get the user associated with the admin.
     */
    public function social_link()
    {
        return $this->hasOne(SocialLink::class, 'owner_id' )->where('owner_table','events');
    }
}
