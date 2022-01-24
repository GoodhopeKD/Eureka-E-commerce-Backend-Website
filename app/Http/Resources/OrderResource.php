<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $seller = null;
        switch ( $this->seller_table ) {
            case 'users':
                $seller = ( $this->seller_id ) ? new EntityDisplayCardResource( $this->seller_user ) : null;
                break;

            case 'stores':
                $seller = ( $this->seller_id ) ? new EntityDisplayCardResource( $this->seller_store ) : null;
                break;
        }

        return [
            'id'                        => $this->id,
            'reference'                 => $this->reference,
            'placed_datetime'           => $this->created_at,
            'placer_user_id'            => $this->placer_user_id,
            "placer"                    => ( $this->placer_user_id ) ? new EntityDisplayCardResource( $this->placer ) : null,
            'seller_table'              => $this->seller_table,
            'seller_id'                 => $this->seller_id,
            'seller'                    => $seller,
            'product_id'                => $this->product_id,
            "product"                   => new ProductResource( $this->product ), 
            'product_count'             => $this->product_count,
            //'product_variations'        => ProductVariationResource::collection( $this->product_variations ),
            'delivery_fee'              => $this->delivery_fee,
            'delivery_fee_set_datetime' => $this->delivery_fee_set_datetime,
            'estimated_delivery_datetime'=> $this->estimated_delivery_datetime,
            'amount_due_provisional'    => $this->amount_due_provisional,
            'discount_code'             => $this->discount_code,
            'discount_instance'         => new DiscountInstanceResource( $this->discount_instance ),
            'discount_amount'           => $this->discount_amount,
            'amount_due_final'          => $this->amount_due_final,
            'payment_method'            => $this->payment_method,
            'payment_made_datetime'     => $this->payment_made_datetime,
            'payment_confirmation_datetime' => $this->payment_confirmation_datetime,
            'intermediary_admin_user_id'=> $this->intermediary_admin_user_id,
            "intermediary_admin"        => ( $this->intermediary_admin ) ? new EntityDisplayCardResource( $this->intermediary_admin ) : null,
            'status'                    => $this->status,
            "delivery_address"          => new AddressResource( $this->delivery_address ),
            "delivery_phone"            => new PhoneResource( $this->delivery_phone ),
            'delivered_datetime'        => $this->delivered_datetime,
            'completed_datetime'        => $this->completed_datetime,
            'visible_to_seller'         => $this->visible_to_seller,
            'visible_to_placer'         => $this->visible_to_placer,
            'visible_to_admin'          => $this->visible_to_admin,
            'updated_datetime'          => $this->updated_at,
        ];
    }
}
