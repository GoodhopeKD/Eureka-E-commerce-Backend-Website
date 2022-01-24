<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Models\Pin;
use App\Models\Order;

class ProductResourceCollection extends ResourceCollection
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

                $reviews = $_this->reviews;

                $rating = 0;
                if ( count( $reviews ) ){
                    $total = 0;
                    foreach ($reviews as $review) {
                        $total += $review->rating;
                    }
                    $rating = round( $total/count($reviews) , 2 );
                }

                $auth_user = auth('api')->user();

                return [
                    'id'                    => $_this->id,
                    'reference'             => $_this->reference,
                    'name'                  => $_this->name,
                    'price'                 => $_this->price,
                    'category_id'           => $_this->category_id,
                    'seller_table'          => $_this->seller_table,
                    'seller_id'             => $_this->seller_id,
                    'added_datetime'        => $_this->created_at,
                    'adder_user_id'         => $_this->adder_user_id,
                    'updated_datetime'      => $_this->updated_at,
                    'status'                => $_this->status,
                    'views'                 => new VisitViewFollowCountResource( $_this->log_items()->where( "action", "product_viewed" )->get() ),
                    'images'                => ImageResource::collection( $_this->images->take(1) ),
                    'rating'                => $rating,
                    'added_by_auth_user'    => $auth_user ? $_this->adder_user_id == $auth_user->id : false,
                    'pinning'               => [
                        'favourite'         => $auth_user ? Pin::where([ 'item_table' => 'products', 'item_id' => $_this->id, 'pin_type' => 'favourite', 'adder_user_id' =>$auth_user->id ])->exists() : false,
                        'cart'              => $auth_user ? Pin::where([ 'item_table' => 'products', 'item_id' => $_this->id, 'pin_type' => 'cart', 'adder_user_id' =>$auth_user->id ])->exists() : false,
                        'order'             => $auth_user ? Order::where([ 'product_id' => $_this->id, 'placer_user_id' =>$auth_user->id ])->exists() : false,
                    ],
                ];
            }),
        ];
    }
}
