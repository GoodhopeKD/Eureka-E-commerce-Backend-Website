<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\ConnectInstance;
use App\Http\Resources\ConnectInstanceResource;

class ConnectInstanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function index($user_id)
    {
        return ConnectInstanceResource::collection( ConnectInstance::where( 'user_id' , $user_id )->paginate() );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'user_id' => ['sometimes', 'required', 'exists:users,id', 'integer'],
            'device_info' => ['required', 'array'],
            'agent_app_info' => ['required', 'array'],
            'utc_offset' => ['required','string'],
            'signin_datetime' => ['date:Y-m-d H:i:s|nullable'],
            'request_location' => ['required'],
        ]);

        function generate_app_access_token(){
            return substr(preg_replace('/[^a-zA-Z0-9\']/', '', Hash::make(Str::random(32)) ),6,16);
        }

        $app_access_token = generate_app_access_token();
        while ( ConnectInstance::where( 'app_access_token', $app_access_token )->exists() ){
            $app_access_token = generate_app_access_token();
        }
        
        $validated_data['app_access_token'] = $app_access_token;
        $validated_data['last_active_datetime'] = now()->toDateTimeString();
        $validated_data['device_info'] = Crypt::encryptString( json_encode( $validated_data['device_info'] ));
        $validated_data['agent_app_info'] = Crypt::encryptString( json_encode( $validated_data['agent_app_info'] ));
        $validated_data['request_location'] = Crypt::encryptString( json_encode( $validated_data['request_location'] ));

        $connect_instance = ConnectInstance::create($validated_data);

        return new ConnectInstanceResource( ConnectInstance::find($connect_instance->id) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated_data = $request->validate([
            'user_id' => ['sometimes', 'required', 'exists:users,id', 'integer'],
            'device_info' => ['sometimes', 'required', 'array'],
            'agent_app_info' => ['sometimes', 'required', 'array'],
            'status' => ['string', Rule::in(['active', 'ended'])],
            'last_active_datetime' => ['date:Y-m-d H:i:s'],
            'signin_datetime' => ['date:Y-m-d H:i:s|nullable'],
            'signout_datetime' => ['date:Y-m-d H:i:s|nullable'],
            'utc_offset' => ['string'],
            'request_location' => ['required'],
        ]);

        $validated_data['device_info'] = Crypt::encryptString( json_encode( $validated_data['device_info'] ));
        $validated_data['agent_app_info'] = Crypt::encryptString( json_encode( $validated_data['agent_app_info'] ));
        $validated_data['request_location'] = Crypt::encryptString( json_encode( $validated_data['request_location'] ));

        $connect_instance = ConnectInstance::find($id);
        $connect_instance->update($validated_data);

        return new ConnectInstanceResource( $connect_instance );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $user_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id, $id)
    {
        
    }
}
