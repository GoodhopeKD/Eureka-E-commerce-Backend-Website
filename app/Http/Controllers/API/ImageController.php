<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use App\Models\Image;
use App\Http\Resources\ImageResource;

class ImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $owner_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'name' => ['required', 'string', 'max:32'],
            'type' => ['required', 'string', 'max:12'],
            'uri' => ['required','string', 'max:255'],
            'height' => ['integer'],
            'width' => ['integer'],
            'title' => ['required', 'string', 'max:64'],
            'alt' => ['required', 'string', 'max:255'],
            'tag' => ['required', 'string', Rule::in(['profile_image', 'store_banner', 'product_image', 'event_poster', 'post_receipt'])],
            'owner_table' => ['required', 'string', Rule::in(['users', 'stores', 'products', 'events'])],
            'owner_id' => ['required', 'exists:'.$request->owner_table.',id', 'integer'],
        ]);

        $user = auth('api')->user();
        $validated_data['adder_user_id'] = $user->id;

        $image = Image::create($validated_data);

        return new ImageResource( Image::find($image->id) );
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
        $owner_table = request()->segments()[env('API_DOMAIN')?1:2];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $owner_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($owner_id, $id)
    {
        $owner_table = request()->segments()[env('API_DOMAIN')?1:2];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $some_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $upload_dir_name = request()->route()->getAction()['upload_dir_name'];

        $validated_data = $request->validate([
            'images' => ['required', 'array'],
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $response = ['images' => []];
        if($request->has('images')) {
            foreach($request->file('images') as $image) {
                $filename = substr(md5(microtime()),rand(0,9),20).'.'.$image->extension();
                $path = $image->storeAs('public/'.$upload_dir_name,$filename);
                $image_data = [];
                $image_data['name'] = $filename;
                $image_data['uri'] = Storage::url('public/'.$upload_dir_name.'/'.$filename);
                $image_data['old_name'] = $image->getClientOriginalName();
                array_push( $response['images'], $image_data );
            }
            $response["success"] = true;
            $response["message"] = "Success! image(s) uploaded";
        }
        else {
            $response["success"] = false;
            $response["message"] = "Failed! image(s) not uploaded";
        }
        return response()->json($response);
    }
}
