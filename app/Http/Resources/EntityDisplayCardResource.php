<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Store;

class EntityDisplayCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $name = "";
        $profile_image = null;

        switch ( $this->getTable() ) {
            case 'users':
                $name = $this->name_s . " " . $this->surname;
                $profile_image = new ImageResource( User::find($this->id)->profile_image );
                break;
            
            case 'stores':
                $name = $this->name;
                $profile_image = new ImageResource( Store::find($this->id)->banner_image );
                break;
        }

        return [
            "id"                => $this->id,
            "name"              => $name,
            "profile_image"     => $profile_image,
        ];
    }
}
