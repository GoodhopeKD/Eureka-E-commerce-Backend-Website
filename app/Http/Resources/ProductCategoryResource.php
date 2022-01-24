<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ProductCategoryResource extends JsonResource
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
            'name'                  => $this->name,
            'keywords'              => $this->keywords,
            'adder_user_id'         => $this->adder_user_id,
            'added_datetime'        => $this->created_at,
            'updated_datetime'      => $this->updated_at,
        ];
    }
}
