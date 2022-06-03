<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Store;
use App\Http\Resources\StoreResource;
use App\Http\Resources\StoreResourceCollection;

class StoreController extends Controller
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
        $action_type = isset( request()->route()->getAction()['action_type'] ) ? request()->route()->getAction()['action_type'] : 'all';

        if ( $action_type === "all" ){
            $result = Store::where(['status'=>'active'])->paginate();
        }

        if ( $action_type === "search" && !is_null($query_string) ){
            $result = Store::where( 'name' , 'LIKE' , '%'.$query_string.'%' )
                    ->where(['status'=>'active'])
                    ->orWhere( 'description' , 'LIKE' , '%'.$query_string.'%' )
                    ->orWhere( 'wilaya' , 'LIKE' , '%'.$query_string.'%' )
                    ->paginate();
        }

        return ($result) ? new StoreResourceCollection( $result ) : null;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home_collection()
    {
        $response = json_decode((new StoreResourceCollection(
            Store::where(['status'=>'active'])->paginate(5)
        ))->toJson(),true)['data'];

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $username
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        return response()->json( new StoreResource( Store::where( 'username' , $username )->firstOrFail() ) );
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
        //
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
