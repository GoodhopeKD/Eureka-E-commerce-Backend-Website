<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchTermResource extends JsonResource
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
            'query_string'          => $this->query_string,
            'pool'                  => $this->pool,
            'user_id'               => $this->user_id,
            'connect_instance_id'   => $this->connect_instance_id,
            'search_datetime_id'    => $this->created_at,
        ];
    }
}
