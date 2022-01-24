<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $item = null;

        switch ( $this->item_table ) {
            case 'products':
                $item = json_decode(( new ProductResourceCollection( [$this->product] ))->toJson(),true)['data'][0];
                break;
            
            case 'events':
                $item = json_decode(( new EventResourceCollection( [$this->event] ))->toJson(),true)['data'][0];
                break;
        }

        return [
            'id'                => $this->id,
            'item_table'        => $this->item_table,
            'item_id'           => $this->item_id,
            'item'              => $item,
            'item_cart_count'   => $this->item_cart_count,
            //'item_cart_variations'=> ProductVariationResource::collection( $this->item_cart_variations ),
            'pin_type'          => $this->pin_type,
            'adder_user_id'     => $this->adder_user_id,
            'pin_datetime'      => $this->created_at,
        ];
    }
}
