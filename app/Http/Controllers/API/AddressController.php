<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Address;
use App\Http\Resources\AddressResource;
use App\Http\Resources\UserResource;

class AddressController extends Controller
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
            'surname' => ['required', 'string', 'max:32'],
            'name_s' => ['required', 'string', 'max:64'],
            'address_line_one' => ['required', 'string', 'max:255'],
            'address_line_two' => ['string', 'nullable', 'max:255'],
            'postal_code' => ['string', 'nullable', 'max:32'],
            'commune' => ['required', 'string', 'max:32'],
            'wilaya' => ['required', 'string', 'max:32'],
        ]);

        $auth_user = auth('api')->user();

        $validated_data['owner_table']  = $owner_table;
        $validated_data['owner_id']     = $owner_id;
        $validated_data['adder_user_id']= $auth_user->id;

        $address = Address::create($validated_data);

        $response['address'] = new AddressResource( Address::find($address->id) );
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
            'surname' => ['string', 'max:32'],
            'name_s' => ['string', 'max:64'],
            'address_line_one' => ['string', 'max:255'],
            'address_line_two' => ['string', 'max:255'],
            'postal_code' => ['string', 'max:6'],
            'commune' => ['string', 'max:32'],
            'wilaya' => ['string', 'max:32'],
        ]);

        $address = Address::find($id);
        $address->update($validated_data);

        $response['address'] = new AddressResource( Address::find($address->id) );
        $response['auth_user'] = new UserResource( auth('api')->user() );
        return response()->json($response);
    }
}
