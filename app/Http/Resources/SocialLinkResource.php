<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SocialLinkResource extends JsonResource
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
            'id'                => $this->id,
            'sitename'          => $this->sitename,
            'username_url'      => $this->username_url,
            'owner_table'       => $this->owner_table,
            'owner_id'          => $this->owner_id,
            'adder_user_id'     => $this->adder_user_id,
            'added_datetime_id' => $this->created_at,
        ];
    }
}
