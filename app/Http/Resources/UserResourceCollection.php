<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
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
                $reviews = $_this->seller_reviews;

                $rating = 0;
                if ( count( $reviews ) ){
                    $total = 0;
                    foreach ($reviews as $review) {
                        $total += $review->rating;
                    }
                    $rating = round( $total/count($reviews) , 2 );
                }

                return [
                    "id"                            => $_this->id,
                    "email"                         => $_this->email,
                    "email_verified_datetime"       => $_this->email_verified_datetime,
                    "surname"                       => $_this->surname,
                    "name_s"                        => $_this->name_s,
                    "username"                      => $_this->username,
                    "profile_image"                 => new ImageResource( $_this->profile_image ), 
                    "gender"                        => $_this->gender,
                    "signup_datetime"               => $_this->created_at,
                    "last_active_datetime"          => $_this->connect_instances()->orderBy('last_active_datetime', 'desc')->first()->last_active_datetime,
                    "account_type"                  => $_this->account_type,
                    "account_level"                 => $_this->account_level,
                    "account_status"                => $_this->account_status,
                    "account_verified_datetime"     => $_this->account_verified_datetime,
                    "loyalty_points"                => $_this->loyalty_points,
                    "referral_code"                 => $_this->referral_code,
                    "seller_visits"                 => new VisitViewFollowCountResource( $_this->log_items()->where( "action", "seller_visited" )->get() ),
                    "seller_rating"                 => $rating,
                ];
            }),
        ];
    }
}
