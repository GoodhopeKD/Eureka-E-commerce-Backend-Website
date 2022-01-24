<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $reviews = $this->reviews;

        $rating = 0;
        if ( count( $reviews ) ){
            $total = 0;
            foreach ($reviews as $review) {
                $total += $review->rating;
            }
            $rating = round( $total/count($reviews) , 2 );
        }

        return [
            "id"                 => $this->id,
            "name"               => $this->name,
            "username"           => $this->username,
            "banner_image"       => new ImageResource( $this->banner_image ), 
            "status"             => $this->status,
            "description"        => $this->description,
            "wilaya"             => $this->wilaya,
            "commune"            => $this->commune,
            //"phones"             => PhoneResource::collection( $this->phones ),
            "owner_user_id"      => $this->owner_user_id,
            "owner"              => ( $this->owner_user_id ) ? new EntityDisplayCardResource( $this->owner ) : null,
            "created_datetime"   => $this->created_at,
            "followers"          => new VisitViewFollowCountResource( $this->follow_instances ),
            "visits"             => new VisitViewFollowCountResource( $this->log_items()->where( "action", "store_visited" )->get() ),
            "social_links"       => SocialLinkResource::collection( $this->social_links ),
            "rating"             => $rating,
            "notifications"      => EntityNotificationResource::collection( $this->notifications ),
            "preferences"        => EntityPreferenceResource::collection( $this->preferences ),
        ];
    }
}
