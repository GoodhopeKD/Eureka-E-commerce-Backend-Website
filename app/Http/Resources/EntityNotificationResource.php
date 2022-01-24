<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntityNotificationResource extends JsonResource
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
            'message_title'     => $this->message_title,
            'message_body'      => $this->message_body,
            'entity_table'      => $this->entity_table,
            'entity_id'         => $this->entity_id,
            'created_datetime'  => $this->created_at,
            'opened_datetime'   => $this->opened_datetime,
        ];
    }
}
