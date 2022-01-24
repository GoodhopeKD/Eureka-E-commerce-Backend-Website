<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $reviews = $this->seller_reviews;

        $rating = 0;
        if ( count( $reviews ) ){
            $total = 0;
            foreach ($reviews as $review) {
                $total += $review->rating;
            }
            $rating = round( $total/count($reviews) , 2 );
        }

        return [
            "id"                            => $this->id,
            "email"                         => $this->email,
            "email_verified_datetime"       => $this->email_verified_datetime,
            "surname"                       => $this->surname,
            "name_s"                        => $this->name_s,
            "username"                      => $this->username,
            "profile_image"                 => new ImageResource( $this->profile_image ), 
            "gender"                        => $this->gender,
            "phones"                        => PhoneResource::collection( $this->phones ),
            "address"                       => new AddressResource( $this->address ),
            "signup_datetime"               => $this->created_at,
            "last_active_datetime"          => $this->connect_instances()->orderBy('last_active_datetime', 'desc')->first()->last_active_datetime,
            "account_type"                  => $this->account_type,
            "account_level"                 => $this->account_level,
            "account_status"                => $this->account_status,
            "account_verified_datetime"     => $this->account_verified_datetime,
            "loyalty_points"                => $this->loyalty_points,
            "referral_code"                 => $this->referral_code,
            "seller_visits"                 => new VisitViewFollowCountResource( $this->log_items()->where( "action", "seller_visited" )->get() ),
            "seller_rating"                 => $rating,
            "referral_code_use_instances"   => DiscountInstanceResource::collection( $this->referral_code_use_instances ),
            "admin_extension"               => new AdminResource( $this->admin_extension ),
            "pins"                          => PinResource::collection( $this->pins ),
            "search_terms"                  => SearchTermResource::collection( $this->search_terms ),
            "follow_instances"              => FollowInstanceResource::collection( $this->follow_instances ),
            "notifications"                 => EntityNotificationResource::collection( $this->notifications ),
            "preference_items"              => EntityPreferenceResource::collection( $this->preferences ),
            "store_owned"                   => new StoreResource( $this->store_owned ),
            //"placed_orders"                 => json_decode((new OrderResourceCollection( $this->placed_orders ))->toJson(),true)['data'],
            //"seller_received_orders"        => json_decode((new OrderResourceCollection( $this->seller_received_orders ))->toJson(),true)['data'],
            //"seller_products"               => json_decode((new ProductResourceCollection( $this->seller_products ))->toJson(),true)['data'],
            "has_seller_products"           => count($this->seller_products) ? true : false,
            "seller_reviews"                => ReviewResource::collection( $this->seller_reviews ),
        ];
    }
}
