<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'name'              => $this->name,
            'desctiption'       => $this->desctiption,
            'created_datetime'  => $this->created_at,
            'creator_admin_user_id'  => $this->creator_admin_user_id,
        ];
    }
}
