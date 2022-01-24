<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Product;

use App\Models\Pin;
use App\Models\Order;

class ProductResource extends JsonResource
{
    protected $related_products;

    public function related_products($value){
        $this->related_products = $value;
        return $this;
    }

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

        $reviews = $this->reviews;

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
            'id'                    => $this->id,
            'reference'             => $this->reference,
            'name'                  => $this->name,
            'price'                 => $this->price,
            'details'               => $this->details,
            'commune'               => $this->commune,
            'wilaya'                => $this->wilaya,
            'stock_available'       => $this->stock_available,
            'category_id'           => $this->category_id,
            'category'              => ($this->category_id) ? new ProductCategoryResource( $this->category ) : null,
            'seller_table'          => $this->seller_table,
            'seller_id'             => $this->seller_id,
            'seller'                => $seller,
            'added_datetime'        => $this->created_at,
            'adder_user_id'         => $this->adder_user_id,
            'adder'                 => ( $this->adder_user_id ) ? new EntityDisplayCardResource( $this->adder ) : null,
            'updated_datetime'      => $this->updated_at,
            'status'                => $this->status,
            'condition'             => $this->condition,
            'confirmation_datetime'      => $this->confirmation_datetime,
            'intermediary_admin_user_id' => $this->intermediary_admin_user_id,
            'intermediary_admin'    => ( $this->intermediary_admin_user_id ) ? new EntityDisplayCardResource( $this->intermediary_admin ) : null,
            'views'                 => new VisitViewFollowCountResource( $this->log_items()->where( "action", "product_viewed" )->get() ),
            //'variations'            => ProductVariationResource::collection( $this->variations ),
            'images'                => ImageResource::collection( $this->images ),
            'reviews'               => ReviewResource::collection( $reviews ),
            'rating'                => $rating,
            'related_products'      => $this->related_products,
            'added_by_auth_user'    => $auth_user ? $this->adder_user_id == $auth_user->id : false,
            'pinning'               => [
                'favourite'         => $auth_user ? Pin::where([ 'item_table' => 'products', 'item_id' => $this->id, 'pin_type' => 'favourite', 'adder_user_id' =>$auth_user->id ])->exists() : false,
                'cart'              => $auth_user ? Pin::where([ 'item_table' => 'products', 'item_id' => $this->id, 'pin_type' => 'cart', 'adder_user_id' =>$auth_user->id ])->exists() : false,
                'order'             => $auth_user ? Order::where([ 'product_id' => $this->id, 'placer_user_id' =>$auth_user->id ])->exists() : false,
            ],
        ];
    }
}
