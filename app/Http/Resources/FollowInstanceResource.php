<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FollowInstanceResource extends JsonResource
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
            "id"                    => $this->id,
            "follower_user_id"      => $this->follower_user_id,
            "follower"              => ( $this->follower_user_id ) ? new EntityDisplayCardResource( $this->follower ) : null,
            "followed_datetime"     => $this->created_at,
            "followed_store_id"     => $this->followed_store_id,
            "followed_store"        => ( $this->followed_store_id ) ? new EntityDisplayCardResource( $this->followed_store ) : null,
        ];
    }
}
