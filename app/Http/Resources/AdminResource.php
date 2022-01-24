<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Admin;
use App\Models\User;
use App\Models\EntityNotification;

class AdminResource extends JsonResource
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
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'user'          => ( $this->user_id ) ? new EntityDisplayCardResource( $this->user ) : null,
            'status'        => $this->status,
            'added_datetime'=> $this->created_at,
            'adder_admin_user_id'=> $this->adder_admin_user_id,
            'adder_admin'   => ( $this->adder_admin_user_id ) ? new EntityDisplayCardResource( $this->adder_admin ) : null,
            'permissions'   => PermissionInstanceResource::collection( $this->permissions ),
            'events_added'  => json_decode(( new EventResourceCollection( $this->events_added ))->toJson(),true)['data'],
            'preferences'   => EntityPreferenceResource::collection( $this->preferences ),
            'notifications' => EntityNotificationResource::collection( $this->notifications ),
            'broadcast_notifications' => EntityNotificationResource::collection( EntityNotification::where( 'entity_table','admins' )->whereNull('entity_id')->get() ),
        ];
    }
}
