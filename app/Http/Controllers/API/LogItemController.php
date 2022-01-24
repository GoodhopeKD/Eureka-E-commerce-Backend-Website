<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Stevebauman\Location\Facades\Location;

use App\Models\LogItem;
use App\Http\Resources\LogItemResource;

class LogItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return LogItemResource::collection(LogItem::paginate());
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
            'action' => ['required', 'string'],
            'action_user_id' => ['sometimes', 'required', 'exists:users,id', 'integer'],
            'connect_instance_id' => ['required', 'exists:connect_instances,id', 'integer'],
            'thing_table' => ['required', 'string'],
            'thing_id' => ['required', 'exists:'.$request->thing_table.',id', 'integer'],
            'thing_column' => ['string'],
            'update_initial_value' => ['string'],
            'update_final_value' => ['string'],
            'multistep_operation_hash' => ['string'],
        ]);

        $request_location = $request->ip();
        if ($position = Location::get()) $request_location = $position;

        $validated_data['request_location'] = Crypt::encryptString( json_encode( $request_location ));

        if (in_array($validated_data['action'],['product_viewed','store_visited'])){
            $log_item = LogItem::where([
                'connect_instance_id' => $validated_data['connect_instance_id'],
                'thing_table' => $validated_data['thing_table'],
                'thing_id' => $validated_data['thing_id'],
                'action' => $validated_data['action'],
            ])
            ->orderBy('created_at', 'desc')->first();
    
            if ( $log_item && (new Carbon( $log_item['created_at'] ))->isToday() ){
                if ( isset($validated_data['action_user_id'] ) && is_null( $log_item->action_user_id ) )
                    $log_item->update($validated_data);
            } else {
                $log_item = LogItem::create($validated_data);
            }
        } else {
            $log_item = LogItem::create($validated_data);
        }  

        return new LogItemResource( LogItem::find( $log_item->id ) );
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
