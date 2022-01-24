<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StoreResourceCollection extends ResourceCollection
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

                return [
                    "id"                 => $_this->id,
                    "name"               => $_this->name,
                    "username"           => $_this->username,
                    "banner_image"       => new ImageResource( $_this->banner_image ), 
                    "status"             => $_this->status,
                    "description"        => $_this->description,
                    "wilaya"             => $_this->wilaya,
                    "commune"            => $_this->commune,
                    "owner_user_id"      => $_this->owner_user_id,
                    "created_datetime"   => $_this->created_at,
                    "followers"          => new VisitViewFollowCountResource( $_this->follow_instances ),
                    "visits"             => new VisitViewFollowCountResource( $_this->log_items()->where( "action", "store_visited" )->get() ),
                    "rating"             => $rating,
                ];
            }),
        ];
    }
}
