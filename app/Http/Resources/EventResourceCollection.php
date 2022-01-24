<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($_this){
                return [
                    'id'                => $_this->id,
                    'reference'         => $_this->reference,
                    'title'             => $_this->title,
                    'venue'             => $_this->venue,
                    'event_datetime'    => $_this->event_datetime,
                    'utc_offset'        => $_this->utc_offset,
                    'event_poster'      => new ImageResource( $_this->event_poster ),
                    'adder_admin_user_id'=> $_this->adder_admin_user_id,
                ];
            }),
        ];
    }
}
