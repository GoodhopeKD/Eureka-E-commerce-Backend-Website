<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class LogItemResource extends JsonResource
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
            $request_location = json_decode( Crypt::decryptString( $this->request_location ) );
        } catch (DecryptException $e) {
            $request_location = $this->request_location;
        }

        $thing = null;
        $modelClassName = 'App\Model\\' . studly_case(str_singular($this->thing_table));
        $resourceClassName = 'App\Http\Resources\\' . studly_case(str_singular($this->thing_table)) . 'Resource';

        if ( class_exists($modelClassName) && class_exists($resourceClassName)) {
            $thing = new $resourceClassName( $modelClassName::find($this->thing_id) );
        }

        return [
            'id'                    => $this->id,
            'action'                => $this->action,
            'action_user_id'        => $this->action_user_id,
            'action_datetime'       => $this->created_at,
            'connect_instance_id'   => $this->connect_instance_id,
            'thing_table'           => $this->thing_table,
            'thing_id'              => $this->thing_id,
            'thing_column'          => $this->thing_column,
            'thing'                 => $thing,
            'update_initial_value'  => $this->update_initial_value,
            'update_final_value'    => $this->update_final_value,
            'multistep_operation_hash'=> $this->multistep_operation_hash,
            'request_location'      => $request_location,
        ];
    }
}
