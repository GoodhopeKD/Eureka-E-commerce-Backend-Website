<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionInstance extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'permission_id',
        'admin_id',
        'status',
        'granter_admin_user_id',
    ];

    /**
     * Get the permission associated with the instance.
     */
    public function permission()
    {
        return $this->belongsTo( Permission::class, 'permission_id' );
    }
}
