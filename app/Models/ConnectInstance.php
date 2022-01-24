<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConnectInstance extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'signin_datetime' => 'datetime',
        'last_active_datetime' => 'datetime',
        'signout_datetime' => 'datetime',
    ];

    protected $fillable = [
        'app_access_token',
        'user_id',
        'signin_datetime',
        'last_active_datetime',
        'signout_datetime',
        'utc_offset',
        'status',
        'device_info',
        'agent_app_info',
        'request_location',
    ];

    /**
     * Get the log_items associated with the connect_instance.
     */
    public function log_items()
    {
        return $this->hasMany(LogItem::class, 'thing_id')->where('thing_table','connect_instances');
    }

    /**
     * Get the seller associated with the product.
     */
    public function user()
    {
        return $this->belongsTo( User::class );
    }
}
