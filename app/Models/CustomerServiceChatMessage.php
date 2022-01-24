<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerServiceChatMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chat_id',
        'message_body',
        'sender_user_id',
    ];
}
