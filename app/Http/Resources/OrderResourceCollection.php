<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($_this){

                $product = new ProductResource( $_this->product );
                $auth_user = auth('api')->user();

                return [
                    'id'                        => $_this->id,
                    'reference'                 => $_this->reference,
                    'seller_table'              => $_this->seller_table,
                    'seller_id'                 => $_this->seller_id,
                    'product_id'                => $_this->product_id,
                    'product'                   => [
                        'id'                    => $_this->product_id,
                        'name'                  => $product->name,
                        'images'                => ImageResource::collection( [$product->images[0]] ),
                        'added_by_auth_user'    => $auth_user ? $product->adder_user_id == $auth_user->id : false,
                    ],
                    //"product"                   => json_decode((new ProductResourceCollection( [$_this->product] ))->toJson(),true)['data'][0],
                    'product_count'             => $_this->product_count,
                    'amount_due_provisional'    => $_this->amount_due_provisional,
                    'amount_due_final'          => $_this->amount_due_final,
                    'status'                    => $_this->status,
                ];
            }),
        ];
    }
}
