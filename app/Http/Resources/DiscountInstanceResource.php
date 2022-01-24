<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountInstanceResource extends JsonResource
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
            'code'              => $this->code,
            'details'           => $this->details,
            'amount'            => $this->amount,
            'order_id'          => $this->order_id,
            'claimed_datetime'  => $this->created_at,
        ];
    }
}
