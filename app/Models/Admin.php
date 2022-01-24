<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'adder_admin_user_id',
    ];

    /**
     * Get the user associated with the admin.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id' );
    }

    /**
     * Get the user associated with the admin.
     */
    public function adder_admin()
    {
        return $this->belongsTo(User::class, 'adder_admin_user_id' );
    }

    /**
     * Get the follow_instances associated with the user.
     */
    public function events_added()
    {
        return $this->hasMany(Event::class, 'adder_admin_user_id');
    }

    /**
     * Get the follow_instances associated with the user.
     */
    public function permissions()
    {
        return $this->hasMany(PermissionInstance::class, 'admin_id');
    }

    /**
     * Get the notifications associated with the user.
     */
    public function notifications()
    {
        return $this->hasMany( EntityNotification::class , 'entity_id' )->where('entity_table','admins');
    }

    /**
     * Get the preferences associated with the user.
     */
    public function preferences()
    {
        return $this->hasMany(EntityPreference::class , 'entity_id' )->where('entity_table','admins');
    }
}
