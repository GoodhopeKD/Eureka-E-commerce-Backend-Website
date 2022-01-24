<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'rating'                => $this->rating,
            'comment'               => $this->comment,
            'reviewed_datetime'     => $this->updated_at,
            'reviewer_user_id'      => $this->reviewer_user_id,
            'reviewer'              => ( $this->reviewer_user_id ) ? new EntityDisplayCardResource( $this->reviewer ) : null,
            'reviewed_thing_table'  => $this->reviewed_thing_table,
            'reviewed_thing_id'     => $this->reviewed_thing_id,
        ];
    }
}
