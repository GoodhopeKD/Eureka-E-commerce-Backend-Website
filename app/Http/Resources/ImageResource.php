<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'type'          => $this->type,
            'uri'           => $this->uri,
            'height'        => $this->height,
            'width'         => $this->width,
            'title'         => $this->title,
            'alt'           => $this->alt,
            'tag'           => $this->tag,
            //'order'         => $this->order,
            'owner_table'   => $this->owner_table,
            'owner_id'      => $this->owner_id,
            'adder_user_id' => $this->adder_user_id,
            'added_datetime'=> $this->created_at,
            'updated_datetime'=> $this->updated_at,
        ];
    }
}
