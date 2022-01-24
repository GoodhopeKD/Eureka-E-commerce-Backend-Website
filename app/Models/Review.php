<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rating',
        'comment',
        'reviewer_user_id',
        'reviewed_thing_table',
        'reviewed_thing_id',
    ];

    /**
     * Get the reviewer associated with the product.
     */
    public function reviewer()
    {
        return $this->belongsTo( User::class, 'reviewer_user_id' );
    }
}
