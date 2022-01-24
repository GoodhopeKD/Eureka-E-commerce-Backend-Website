<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Admin;
use App\Models\User;

class EventResource extends JsonResource
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
            'reference'         => $this->reference,
            'title'             => $this->title,
            'description'       => $this->description,
            'venue'             => $this->venue,
            'contact_details'   => $this->contact_details,
            'other_details'     => $this->other_details,
            'event_datetime'    => $this->event_datetime,
            'utc_offset'        => $this->utc_offset,
            'event_poster'      => new ImageResource( $this->event_poster ), 
            'social_link'       => new SocialLinkResource( $this->social_link ), 
            'added_datetime'    => $this->updated_at,
            'adder_admin_user_id'=> $this->adder_admin_user_id,
            'adder_admin'       => ( $this->adder_admin_user_id ) ? new EntityDisplayCardResource( $this->adder_admin ) : null,
        ];
    }
}
