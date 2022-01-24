<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ConnectInstanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        try {
            $device_info = json_decode( Crypt::decryptString( $this->device_info ) );
            $agent_app_info = json_decode( Crypt::decryptString( $this->agent_app_info ) );
            $request_location = json_decode( Crypt::decryptString( $this->request_location ) );
        } catch (DecryptException $e) {
            $device_info = $this->device_info;
            $agent_app_info = $this->agent_app_info;
            $request_location = $this->request_location;
        }

        return [
            'id'                    => $this->id,
            'app_access_token'      => $this->app_access_token,
            'user_id'               => $this->user_id,
            'user'                  => ( $this->user_id ) ? new EntityDisplayCardResource( $this->user ) : null,
            'started_datetime'      => $this->created_at,
            'updated_datetime'      => $this->updated_at,
            'signin_datetime'       => $this->signin_datetime,
            'last_active_datetime'  => $this->last_active_datetime,
            'signout_datetime'      => $this->signout_datetime,
            'status'                => $this->status,
            'device_info'           => $device_info,
            'agent_app_info'        => $agent_app_info,
            'request_location'      => $request_location,
            'utc_offset'            => $this->utc_offset,
        ];
    }
}
