<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'surname'           => $this->surname,
            'name_s'            => $this->name_s, 
            'address_line_one'  => $this->address_line_one,
            'address_line_two'  => $this->address_line_two,
            'postal_code'       => $this->postal_code,
            'commune'           => $this->commune,
            'wilaya'            => $this->wilaya,
            'owner_table'       => $this->owner_table,
            'owner_id'          => $this->owner_id,
            'added_datetime'    => $this->created_at,
            'adder_user_id'     => $this->adder_user_id,
        ];
    }
}
