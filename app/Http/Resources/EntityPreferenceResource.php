<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntityPreferenceResource extends JsonResource
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
            'entity_table'  => $this->entity_table,
            'entity_id'     => $this->entity_id,
            'key'           => $this->key,
            'value'         => $this->value,
            'updated_datetime'=> $this->updated_at,
        ];
    }
}
