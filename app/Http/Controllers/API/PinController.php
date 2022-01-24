<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;

use App\Models\Pin;
use App\Http\Resources\PinResource;
use App\Http\Resources\UserResource;


class PinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($adder_user_id,$list)
    {
        $result = null;

        if ( $list === "all" ){
            $result = Pin::where(['adder_user_id'=>$adder_user_id])->orderBy('created_at','DESC')->paginate(30);
        }

        if ( $list === "cart" ){
            $result = Pin::where(['adder_user_id'=>$adder_user_id, 'pin_type'=>'cart'])->orderBy('created_at','DESC')->paginate(10);
        }

        if ( $list === "pinned_favourite_products" ){
            $result = Pin::where(['adder_user_id'=>$adder_user_id, 'pin_type'=>'favourite', 'item_table'=>'products'])->orderBy('created_at','DESC')->paginate(10);
        }

        if ( $list === "pinned_events" ){
            $result = Pin::where(['adder_user_id'=>$adder_user_id, 'pin_type'=>'favourite', 'item_table'=>'events'])->orderBy('created_at','DESC')->paginate(10);
        }

        return ($result) ? PinResource::collection( $result ) : null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $adder_user_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($adder_user_id,Request $request)
    {
        $response = [];

        $validated_data = $request->validate([
            'item_id' => ['required', 'exists:'.$request->item_table.',id', 'integer'],
            'item_table' => ['required', 'string', Rule::in(['products', 'events'])],
            'pin_type' => ['required', 'string', Rule::in(['favourite', 'cart'])],
            'item_cart_count' => ['integer','nullable'],
        ]);

        $user = auth('api')->user();
        $validated_data['adder_user_id'] = $user->id;

        $pin = Pin::create($validated_data);
        $response['pin'] = new PinResource( Pin::find($pin->id) );
        $response['auth_user'] = new UserResource( auth('api')->user() );
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($adder_user_id, Request $request, $id)
    {
        $response = [];

        $validated_data = $request->validate([
            'pin_type' => ['string', Rule::in(['favourite', 'cart'])],
            'item_cart_count' => ['integer','nullable'],
        ]);

        $user = auth('api')->user();
        $validated_data['adder_user_id'] = $user->id;

        $pin = Pin::find($id);
        $pin->update($validated_data);

        $response['pin'] = Pin::find($id);
        $response['auth_user'] = new UserResource( $user );

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($adder_user_id,$id)
    {
        $response = [
            'auth_user' => null
        ];

        $pin = Pin::find($id);
        $pin->delete();

        $response['auth_user'] = new UserResource( auth('api')->user() );

        return response()->json($response);
    }
}
