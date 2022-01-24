<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OutputResource;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

use App\Models\ConnectInstance;
use App\Http\Resources\ConnectInstanceResource;

use App\Models\Pin;

use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  string  $query_string
     * @return \Illuminate\Http\Response
     */
    public function index($query_string=null)
    {
        $result = null;
        $action_type = request()->route()->getAction()['action_type'];

        if ( $action_type === "all" ){
            $result = User::paginate();
        }

        if ( $action_type === "search" && !is_null($query_string) ){
            $result = User::where( 'name_s' , 'LIKE' , '%'.$query_string.'%' )->orWhere( 'surname' , 'LIKE' , '%'.$query_string.'%' )->paginate();
        }

        return ($result) ? UserResource::collection( $result ) : null;
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
            'surname' => ['required', 'string', 'min:2', 'max:32'],
            'name_s' => ['required', 'string', 'min:2', 'max:64'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:64', 'confirmed'],
        ]);

        $validated_data['password'] = bcrypt($request->password);
        $validated_data['signup_datetime'] = now()->toDateTimeString();

        $auth_user = User::create($validated_data);

        $response = [
            'auth_user' => null,
            'connect_instance' => null,
            'products_structured_collection' => ( new ProductController )->structured_collection($auth_user->id)->original,
            'datalists_collection' => ( new CoreController )->datalists()->original,
            'stores_resource_collection' => ( new StoreController )->index()->response()->getData(true),
        ];

        // Start ConnectInstance
        $connect_instance_data = $request->connect_instance;

        $request_location = $request->ip();
        if ($position = Location::get()) $request_location = $position;

        $connect_instance_data['user_id'] = $auth_user->id;
        $connect_instance_data['signin_datetime'] = now()->toDateTimeString();
        $connect_instance_data['status'] = "active";
        $connect_instance_data['last_active_datetime'] = now()->toDateTimeString();
        $connect_instance_data['request_location'] = $request_location;

        if ($connect_instance_data["id"]){
            $response['connect_instance'] = (new ConnectInstanceController)->update( new Request(array_filter( $connect_instance_data) ), $connect_instance_data["id"] );
        } else {
            $response['connect_instance'] = (new ConnectInstanceController)->store( new Request(array_filter( $connect_instance_data) ) );
        }
        // End ConnectInstance

        $response['auth_access_token'] = $auth_user->createToken('auth_access_token')->accessToken;

        $response['auth_user'] = new UserResource( $auth_user );

        return response( $response );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signin(Request $request)
    {
        $validated_data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:64'],
        ]);

        if (!auth()->attempt($validated_data)){
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $auth_user = auth()->user();

        $response = [
            'auth_user' => null,
            'connect_instance' => null,
            'products_structured_collection' => ( new ProductController )->structured_collection($auth_user->id)->original,
            'datalists_collection' => ( new CoreController )->datalists()->original,
            'stores_resource_collection' => ( new StoreController )->index()->response()->getData(true),
        ];

        // Start ConnectInstance
        $connect_instance_data = $request->connect_instance;

        $request_location = $request->ip();
        if ($position = Location::get()) $request_location = $position;

        $connect_instance_data['user_id'] = $auth_user->id;
        $connect_instance_data['signin_datetime'] = now()->toDateTimeString();
        $connect_instance_data['status'] = "active";
        $connect_instance_data['last_active_datetime'] = now()->toDateTimeString();
        $connect_instance_data['request_location'] = $request_location;

        if ($connect_instance_data["id"] ){
            $response['connect_instance'] = (new ConnectInstanceController)->update( new Request(array_filter( $connect_instance_data) ), $connect_instance_data["id"] );
        } else {
            $response['connect_instance'] = (new ConnectInstanceController)->store( new Request(array_filter( $connect_instance_data) ) );
        }
        // End ConnectInstance

        $response['auth_access_token'] = $auth_user->createToken('auth_access_token')->accessToken;

        $response['auth_user'] = new UserResource( $auth_user );

        return response( $response );
    }

    public function signout(Request $request)
    {
        $auth_user = auth('api')->user();
        $auth_user->token()->revoke();

        // Start Old ConnectInstance
        $connect_instance_data = $request->connect_instance;

        $request_location = $request->ip();
        if ($position = Location::get()) $request_location = $position;

        $connect_instance_data['user_id'] = $auth_user->id;
        $connect_instance_data['signout_datetime'] = now()->toDateTimeString();
        $connect_instance_data['status'] = "ended";
        $connect_instance_data['last_active_datetime'] = now()->toDateTimeString();
        $connect_instance_data['request_location'] = $request_location;

        $connect_instance = (new ConnectInstanceController)->update( new Request(array_filter( $connect_instance_data) ), $connect_instance_data["id"] );
        // End Old ConnectInstance
        
        // Start new ConnectInstance
        $connect_instance_data = $request->connect_instance;

        $connect_instance_data['request_location'] = $request_location;
        unset( $connect_instance_data['id'] );

        $connect_instance = ( new ConnectInstanceController )->store( new Request( array_filter( $connect_instance_data) ) );
        // End new ConnectInstance

        $response = [
            'auth_user' => null,
            "connect_instance" => $connect_instance,
            'products_structured_collection' => ( new ProductController )->structured_collection()->original,
            'datalists_collection' => ( new CoreController )->datalists()->original,
            'stores_resource_collection' => ( new StoreController )->index()->response()->getData(true),
        ];

        return response( $response );
    }

    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function merge_pins($id, Request $request)
    {
        $response = [
            'auth_user' => null
        ];

        $validated_data = $request->validate([
            'local_pins' => ['required', 'array'],
        ]);

        $auth_user = auth('api')->user();

        for ($i=0; $i < count($validated_data['local_pins']); $i++) {

            $pin_data = $validated_data['local_pins'][$i];
            $possible_duplicate = Pin::where([
                'item_table'    =>  $pin_data['item_table'],
                'item_id'       =>  $pin_data['item_id'],
                'adder_user_id' =>  $auth_user->id,
            ])->first();

            if ($possible_duplicate){
                $pin = (new PinController)->update( $auth_user->id, new Request(array_filter( $pin_data) ), $possible_duplicate["id"] );
            } else {
                $pin = (new PinController)->store( $auth_user->id, new Request( array_filter( $pin_data) ) );
            }
        }

        $response['auth_user'] = new UserResource( $auth_user );

        return response( $response );
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $username
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        return response()->json( new UserResource ( User::where( 'username' , $username )->firstOrFail() ) );
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
        $response = [];

        $validated_data = $request->validate([
            'surname' => ['string', 'min:2', 'max:32'],
            'name_s' => ['string', 'min:2', 'max:64'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'username' => ['string', 'alpha_dash', 'max:32', 'unique:users'],
            'password' => ['string', 'min:8', 'max:64', 'confirmed'],
        ]);

        $user = User::find($id);
        $user->update($validated_data);

        $auth_user = User::find( auth('api')->user()->id );

        $response['auth_user'] = new UserResource( $auth_user );
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
