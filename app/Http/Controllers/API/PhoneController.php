<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;

use App\Models\Phone;
use App\Http\Resources\PhoneResource;
use App\Http\Resources\UserResource;

class PhoneController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $owner_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($owner_id, Request $request)
    {
        $response = [];

        $owner_table = request()->segments()[env('API_DOMAIN')?1:2];
        $validated_data = $request->validate([
            'country_code' => ['required', 'string', 'min:1', 'max:4'],
            'number' => ['required', 'string', 'min:1', 'max:16'],
            'tag' => ['required', 'string', Rule::in(['calls_or_whatsapp', 'calls','whatsapp'])],
        ]);

        $user = auth('api')->user();

        $validated_data['owner_table']  = $owner_table;
        $validated_data['owner_id']     = $owner_id;
        $validated_data['adder_user_id']= $user->id;

        $phone = Phone::create($validated_data);

        $response['phone'] = new PhoneResource( Phone::find($phone->id) );
        $response['auth_user'] = new UserResource( auth('api')->user() );
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $owner_id
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($owner_id, Request $request, $id)
    {
        $response = [];

        $validated_data = $request->validate([
            'country_code' => ['string', 'min:1', 'max:4'],
            'number' => ['string', 'min:1', 'max:16'],
            'tag' => ['string', Rule::in(['calls_or_whatsapp', 'calls','whatsapp'])],
        ]);

        $phone = Phone::find($id);
        $phone->update($validated_data);

        $response['phone'] = new PhoneResource( Phone::find($phone->id) );
        $response['auth_user'] = new UserResource( auth('api')->user() );
        return response()->json($response);
    }
}