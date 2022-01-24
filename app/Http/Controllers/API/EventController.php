<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Storage;

use App\Models\Image;
use App\Models\Pin;

use App\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Resources\UserResource;

use App\Models\LogItem;
use App\Models\ConnectInstance;

class EventController extends Controller
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
            $result = Event::paginate();
        }

        if ( $action_type === "search" ){
            $result = Event::where( 'title' , 'LIKE' , '%'.$query_string.'%' )
                    ->orWhere( 'description' , 'LIKE' , '%'.$query_string.'%' )
                    ->orWhere( 'venue' , 'LIKE' , '%'.$query_string.'%' )
                    ->orWhere( 'contact_details' , 'LIKE' , '%'.$query_string.'%' )
                    ->orWhere( 'other_details' , 'LIKE' , '%'.$query_string.'%' )
                    ->paginate();
        }

        return ($result) ? EventResource::collection( $result ) : null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = [];

        $validated_data = $request->validate([
            'title' => ['required','string', 'max:64'],
            'description' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:64'],
            'event_datetime' => ['required', 'date:Y-m-d H:i:s'],
            'utc_offset' =>['string', 'nullable', 'max:8'],
            'contact_details' =>['string', 'nullable', 'max:255'],
            'other_details' =>['string', 'nullable', 'max:255'],
            'event_poster' => ['required'],
        ]);

        $reference = "EVN".random_int(100000, 199999).strtoupper(substr(md5(microtime()),rand(0,9),7));
        while ( Event::where( 'reference', $reference )->exists() ){
            $reference = "EVN".random_int(100000, 199999).strtoupper(substr(md5(microtime()),rand(0,9),7));
        }
        $validated_data['reference'] = $reference;

        $auth_user = auth('api')->user();
        $validated_data['adder_admin_user_id'] = $auth_user->id;

        $event = Event::create($validated_data);

        $image_data = $validated_data['event_poster'];
        $image_data['title'] = $event->title.' (event image)';
        $image_data['alt'] = 'This image is an event poster for the entry "'.$event->title.'" with reference number '.$event->reference;
        $image_data['tag'] = 'event_poster';
        $image_data['owner_table'] = 'events';
        $image_data['owner_id'] = $event->id;
        $image = (new ImageController)->store( new Request(array_filter( $image_data) ) );

        $response['event'] = new EventResource( Event::find($event->id) );
        $response['auth_user'] = new UserResource( $auth_user );
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $reference
     * @return \Illuminate\Http\Response
     */
    public function show($reference)
    {
        $event = Event::where( 'reference' , $reference )->first();

        if ($event){
            $request_location = request()->ip();
            if ($position = Location::get()) $request_location = $position;

            // Increment views   
            $connect_instance = ConnectInstance::where( "app_access_token" , request()->segments()[env('API_DOMAIN')?0:1] )->first();
            $view_data = [
                'action'                => 'event_viewed',
                'action_user_id'        => $connect_instance['user_id'],
                'connect_instance_id'   => $connect_instance['id'],
                'thing_table'           => 'events',
                'thing_id'              => $event['id'],
                'request_location'      => Crypt::encryptString( json_encode( $request_location )),
            ];

            $log_item = LogItem::where([
                'connect_instance_id' => $view_data['connect_instance_id'],
                'thing_table' => $view_data['thing_table'],
                'thing_id' => $view_data['thing_id'],
            ])
            ->whereNull('thing_column')->orderByDesc('created_at')->first();
    
            if ( $log_item && (new Carbon( $log_item['created_at'] ))->isToday() ){
                if ( isset($view_data['action_user_id'] ) && is_null( $log_item->action_user_id ) )
                    $log_item->update($view_data);
            } else {
                $log_item = LogItem::create($view_data);
            }
        }

        if ($event) return response()->json( new EventResource($event) ); else abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $seller_id
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $response = [];

        $validated_data = $request->validate([
            'title' => ['string', 'max:64'],
            'description' => [ 'string', 'max:255'],
            'venue' => [ 'string', 'max:64'],
            'event_datetime' => [ 'date:Y-m-d H:i:s'],
            'utc_offset' =>['string', 'nullable', 'max:8'],
            'contact_details' =>['string', 'nullable', 'max:255'],
            'other_details' =>['string', 'nullable', 'max:255'],
            'old_images_to_delete_from_storage' => ['array'],
            'images_to_refresh_in_db' => ['array'],
        ]);

        $auth_user = auth('api')->user();
        $validated_data['adder_admin_user_id'] = $auth_user->id;

        $event = Event::find($id);

        $event->update($validated_data);

        if ( isset($validated_data['old_images_to_delete_from_storage']) && count($validated_data['old_images_to_delete_from_storage']) ){
            for ($i=0; $i < count($validated_data['old_images_to_delete_from_storage']); $i++) {
                $image_data = $validated_data['old_images_to_delete_from_storage'][$i];
                $path = str_replace('/storage', '', $image_data['uri']);
                Storage::delete('/public' . $path);
            }
        }

        if ( isset($validated_data['images_to_refresh_in_db']) && count($validated_data['images_to_refresh_in_db']) ){
            for ($i=0; $i < count($validated_data['images_to_refresh_in_db']); $i++) {
                $image_data = $validated_data['images_to_refresh_in_db'][$i];
                $image = Image::find($image_data['id']);
                $image->delete();
            }
        }

        if ( isset($validated_data['event_poster']) ){
            $image_data = $validated_data['event_poster'];
            $image_data['title'] = $event->title.' (event image)';
            $image_data['alt'] = 'This image is an event poster for the entry "'.$event->title.'" with reference number '.$event->reference;
            $image_data['tag'] = 'event_poster';
            $image_data['owner_table'] = 'events';
            $image_data['owner_id'] = $event->id;
            $image = (new ImageController)->store( new Request(array_filter( $image_data) ) );
        }

        $response['event'] = new EventResource( Event::find($event->id) );
        $response['auth_user'] = new UserResource( $auth_user );
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $seller_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = [];

        $event = Event::find($id);
        
        $pins = Pin::where(['item_table'=>'events','item_id'=>$id])->get();
        if (count($pins)){
            for ($i=0; $i < count($pins); $i++) {
                $pin_data = $pins[$i];
                $pin = Pin::find($pin_data['id']);
                $pin->delete();
            }
        }

        $event_resource = new EventResource( $event );

        if ( isset($event_resource['event_poster']) ){
            $image_data = $event_resource['event_poster'];
            $path = str_replace('/storage', '', $image_data['uri']);
            Storage::delete('/public' . $path);
            $image = Image::find($image_data['id']);
            $image->delete();
        }

        $event->delete();

        return response()->json($response);
    }
}
