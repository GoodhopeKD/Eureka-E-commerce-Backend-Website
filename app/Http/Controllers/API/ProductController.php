<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\Rule;

use App\Models\Image;
use App\Models\ProductVariation;
use App\Models\LogItem;
use App\Models\ConnectInstance;
use App\Models\Pin;
use App\Models\Order;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductResourceCollection;
use App\Http\Resources\UserResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $param_1
     * @param $param_2
     * @return \Illuminate\Http\Response
     */
    public function index($param_1=null,$param_2=null)
    {
        $result = null;
        $action_type = request()->route()->getAction()['action_type'];

        if ( $action_type === "from_seller_available" && is_numeric($param_1) ){
            $seller_table = request()->segments()[env('API_DOMAIN')?1:2];
            $result = Product::where(['status'=>'available','seller_table'=>$seller_table,'seller_id'=>$param_1])->orderByDesc('updated_at')->paginate();
        }

        if ( $action_type === "from_seller_all" && is_numeric($param_1) ){
            $seller_table = request()->segments()[env('API_DOMAIN')?1:2];
            $result = Product::where(['seller_table'=>$seller_table,'seller_id'=>$param_1])->orderByDesc('updated_at')->paginate();
        }

        if ( $action_type === "from_location" && $param_1 ){
            $result = Product::where(['status'=>'available','wilaya'=>$param_1])->orderByDesc('updated_at')->paginate();
        }

        if ( $action_type === "pending_action" ){
            $result = Product::where(['status'=>'pending_confirmation'])->orWhere(['status'=>'suspended'])->orderBy('updated_at','ASC')->paginate();
        }

        if ( $action_type === "popular" ){
            $result = Product::where(['status'=>'available'])
            ->withCount(["log_items" => function($query){
                $query
                ->where('log_items.action', 'product_viewed')
                ->whereDate('created_at', Carbon::today());
            }])
            ->orderByDesc('log_items_count')
            ->paginate();
        }

        if ( $action_type === "all" ){
            $result = Product::where(['status'=>'available'])->orderByDesc('updated_at','DESC')->paginate();
        }

        if ( $action_type === "search" ){
            $result = Product::where(['status'=>'available'])
            ->where( 'name' , 'LIKE' , '%'.$param_1.'%' )
            ->orWhere( 'details' , 'LIKE' , '%'.$param_1.'%' )
            ->orWhere( 'commune' , 'LIKE' , '%'.$param_1.'%' )
            ->orWhere( 'wilaya' , 'LIKE' , '%'.$param_1.'%' )
            ->orderByDesc('updated_at')->paginate();
        }

        return ($result) ? new ProductResourceCollection( $result ) : null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $seller_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($seller_id,Request $request)
    {
        $response = [
            'products_structured_collection' => ( new ProductController )->structured_collection()->original,
        ];

        $seller_table = request()->segments()[env('API_DOMAIN')?1:2];

        $validated_data = $request->validate([
            'name' => ['required','string', 'max:64'],
            'category_id' => ['required', 'exists:product_categories,id', 'integer'],
            //'variations' => ['array'],
            'price' => ['required', 'numeric', 'between:100,999999999'],
            'details' => ['required', 'string', 'max:1024'],
            'entry_type' => ['required', 'string', Rule::in(['product', 'service','product_and_or_service'])],
            'condition' => ['required', 'string', 'max:32'],
            'commune' =>['required', 'string', 'max:32'],
            'wilaya' => ['required', 'string', 'max:32'],
            'images' => ['required', 'array'],
            'seller_table' => ['required', 'string', Rule::in(['users', 'stores'])],
            'seller_id' => ['required', 'integer'],
            'stock_available' => ['integer','nullable'],
        ]);

        $reference = "PRD".random_int(100000, 199999).strtoupper(substr(md5(microtime()),rand(0,9),7));
        while ( Product::where( 'reference', $reference )->exists() ){
            $reference = "PRD".random_int(100000, 199999).strtoupper(substr(md5(microtime()),rand(0,9),7));
        }
        $validated_data['reference'] = $reference;

        $auth_user = auth('api')->user();
        $validated_data['adder_user_id'] = $auth_user->id;

        if ($seller_table=='stores'){
            $validated_data['confirmation_datetime'] = now()->toDateTimeString();
            $validated_data['status'] = 'available';
        }

        $product = Product::create($validated_data);

        for ($i=0; $i < count($validated_data['images']); $i++) {
            $image_data = $validated_data['images'][$i];
            $image_data['title'] = $product->name.' (product image)';
            $image_data['alt'] = 'This image is a product image for the entry "'.$product->name.'" with reference number '.$product->reference;
            $image_data['tag'] = 'product_image';
            $image_data['owner_table'] = 'products';
            $image_data['owner_id'] = $product->id;
            $image = (new ImageController)->store(new Request(array_filter( $image_data) ) );
        }

        if ( isset($validated_data['variations']) && count($validated_data['variations']) ){
            for ($i=0; $i < count($validated_data['variations']); $i++) {
                $variation_data = $validated_data['variations'][$i];
                $variation_data['owner_table'] = 'products';
                $variation_data['owner_id'] = $product->id;
                $variation = (new ProductVariationController)->store(new Request(array_filter( $variation_data) ) );
            }
        }

        $response['product'] = new ProductResource( Product::find($product->id) );
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
        $product = Product::where( 'reference' , $reference )->first();
        $with_related = isset(request()->route()->getAction()['action_type']) && request()->route()->getAction()['action_type'] == 'with_related';

        if ($product){
            $request_location = request()->ip();
            if ($position = Location::get()) $request_location = $position;

            // Increment views   
            $connect_instance = ConnectInstance::where( "app_access_token" , request()->segments()[env('API_DOMAIN')?0:1] )->first();
            $view_data = [
                'action'                => 'product_viewed',
                'action_user_id'        => $connect_instance['user_id'],
                'connect_instance_id'   => $connect_instance['id'],
                'thing_table'           => 'products',
                'thing_id'              => $product['id'],
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

            $related_products = $with_related ? json_decode(( new ProductResourceCollection(
                Product::where(['status'=>'available'])->orderByDesc('updated_at')->paginate(10)
                ))->toJson(),true)['data'] : [];

            return response()->json( (new ProductResource($product))->related_products($related_products) );
        }

        abort(404, 'Product with ref: '.$reference.' not found');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function structured_collection($user_id=null)
    {
        $response = [];

        $response['market_square_preview'] = json_decode((new ProductResourceCollection(
            Product::where(['status'=>'available'])->orderByDesc('updated_at')->paginate(10)
        ))->toJson(),true)['data'];

        $response['popular'] = json_decode((new ProductResourceCollection(
            Product::where(['status'=>'available'])
            ->withCount(["log_items" => function($query){
                $query
                ->where('log_items.action', 'product_viewed')
                ->whereDate('created_at', Carbon::today());
            }])
            ->orderByDesc('log_items_count')
            ->paginate(5)
        ))->toJson(),true)['data'];

        if (is_numeric($user_id))
            $response['recommended'] = json_decode((new ProductResourceCollection(
                Product::where(['status'=>'available'])->orderByDesc('wilaya')->paginate(10)
            ))->toJson(),true)['data'];

        $response['todays_picks'] = json_decode((new ProductResourceCollection(
            Product::where(['status'=>'available'])->orderByDesc('price')->paginate(10)
        ))->toJson(),true)['data'];

        return response($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $category_id
     * @return \Illuminate\Http\Response
     */
    public function category($category_id = null)
    {
        return new ProductResourceCollection( Product::where( 'category_id' , $category_id )->paginate()->sortByDesc('created_at') );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $seller_id
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($seller_id,Request $request, $id)
    {
        $response = [
            'products_structured_collection' => ( new ProductController )->structured_collection()->original,
        ];

        $seller_table = request()->segments()[env('API_DOMAIN')?1:2];

        $validated_data = $request->validate([
            'name' => ['string', 'max:64'],
            'category_id' => ['exists:product_categories,id', 'integer'],
            'price' => ['integer'],
            'status' => ['string', Rule::in(['pending_confirmation','available', 'unavailable','suspended'])],
            'details' => ['string', 'max:1024'],
            'condition' => ['string', 'max:32'],
            'commune' =>['string', 'max:32'],
            'wilaya' => ['string', 'max:32'],

            'images' => ['array'], // fresh list
            'old_images_to_delete_from_storage' => ['array'],
            'images_to_refresh_in_db' => ['array'],

            //'variations_to_refresh_in_db' => ['array'],

            'stock_available' => ['integer','nullable'],
        ]);

        $product = Product::find($id);

        if ( isset($validated_data['status']) ){
            $auth_user = auth('api')->user();
            if ( isset($auth_user->admin_extension) ){
                if ( $validated_data['status'] == 'available' && !isset( $product->confirmation_datetime ) ){
                    $validated_data['confirmation_datetime'] = now()->toDateTimeString();
                    $validated_data['intermediary_admin_user_id'] = $auth_user->id;
                }
            }
        }

        $product->update($validated_data);

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

        if ( isset($validated_data['variations_to_refresh_in_db']) && count($validated_data['variations_to_refresh_in_db']) ){
            for ($i=0; $i < count($validated_data['variations_to_refresh_in_db']); $i++) {
                $variation_data = $validated_data['variations_to_refresh_in_db'][$i];
                $variation = ProductVariation::find($variation_data['id']);
                $variation->delete();
            }
        }
        
        if ( isset($validated_data['images']) && count($validated_data['images']) ){
            for ($i=0; $i < count($validated_data['images']); $i++) {
                $image_data = $validated_data['images'][$i];
                $image_data['title'] = $product->name.' (product image)';
                $image_data['alt'] = 'This image is a product image for the entry "'.$product->name.'" with reference number '.$product->reference;
                $image_data['tag'] = 'product_image';
                $image_data['owner_table'] = 'products';
                $image_data['owner_id'] = $product->id;
                $image = (new ImageController)->store(new Request(array_filter( $image_data) ) );
            }
        }

        if ( isset($validated_data['variations']) && count($validated_data['variations']) ){
            for ($i=0; $i < count($validated_data['variations']); $i++) {
                $variation_data = $validated_data['variations'][$i];
                $variation_data['owner_table'] = 'products';
                $variation_data['owner_id'] = $product->id;
                $variation = (new ProductVariationController)->store(new Request(array_filter( $variation_data) ) );
            }
        }

        $response['product'] = new ProductResource( Product::find($product->id) );
        $response['auth_user'] = new UserResource( auth('api')->user() );
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $seller_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($seller_id,$id)
    {
        $response = [
            'auth_user' => new UserResource( auth('api')->user() ),
            'products_structured_collection' => ( new ProductController )->structured_collection()->original,
        ];

        $product = Product::find($id);

        $orders = Order::where(['product_id'=>$id])->get();
        if (count($orders)){
            abort(405, 'Product with ref: '.$product->reference.' found in user orders and therefore can\'t be deleted directly');
        }

        
        $pins = Pin::where(['item_table'=>'products','item_id'=>$id])->get();
        if (count($pins)){
            for ($i=0; $i < count($pins); $i++) {
                $pin_data = $pins[$i];
                $pin = Pin::find($pin_data['id']);
                $pin->delete();
            }
        }

        $product_resource = new ProductResource( $product );

        if ( isset($product_resource['images']) && count($product_resource['images']) ){
            for ($i=0; $i < count($product_resource['images']); $i++) {
                $image_data = $product_resource['images'][$i];
                $path = str_replace('/storage', '', $image_data['uri']);
                Storage::delete('/public' . $path);
                $image = Image::find($image_data['id']);
                $image->delete();
            }
        }

        if ( isset($product_resource['reviews']) && count($product_resource['reviews']) ){
            for ($i=0; $i < count($product_resource['reviews']); $i++) {
                $review_data = $product_resource['reviews'][$i];
                $review = Review::find($review_data['id']);
                $review->delete();
            }
        }

        if ( isset($product_resource['variations']) && count($product_resource['variations']) ){
            for ($i=0; $i < count($product_resource['variations']); $i++) {
                $variation_data = $product_resource['variations'][$i];
                $variation = ProductVariation::find($variation_data['id']);
                $variation->delete();
            }
        }

        $product->delete();

        return response()->json($response);
    }
}
