<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;

use App\Models\Address;
use App\Models\Phone;
use App\Models\Pin;
use App\Models\Product;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderResourceCollection;
use App\Http\Resources\UserResource;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $entity_id
     * @return \Illuminate\Http\Response
     */
    public function index($entity_id=null)
    {
        $result = null;
        $action_type = request()->route()->getAction()['action_type'];

        if ( $action_type === "all" ){
            $result = Order::orderBy('created_at','DESC')->paginate();
        }

        if ( $action_type === "received" && is_numeric($entity_id) ){
            $seller_table = request()->segments()[env('API_DOMAIN')?1:2];
            $result = Order::where(['seller_table'=>$seller_table,'seller_id'=>$entity_id])->paginate();
        }

        if ( $action_type === "placed" && is_numeric($entity_id) ){
            $result = Order::where('placer_user_id',$entity_id)->paginate();
        }

        return ($result) ? new OrderResourceCollection( $result ) : null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stores(Request $request)
    {
        //
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
        $response = [];
        $seller_table = request()->segments()[env('API_DOMAIN')?1:2];

        $validated_data = $request->validate([
            'product_id' => ['required', 'exists:products,id', 'integer'],
            'product_count' => ['required','integer'],
            //'product_variations' => ['array'],
            'delivery_fee' => ['required', 'numeric', 'between:0,999999999'],
            'amount_due_provisional' => ['required', 'numeric', 'between:100,999999999'],
            'discount_code' => ['string', 'max:32', 'nullable'],
            'discount_amount' => ['numeric', 'between:10,10000', 'nullable'],
            'amount_due_final' => ['numeric', 'between:100,999999999', 'nullable'],
            'payment_method' => ['required', 'string', Rule::in(['cash', 'post_cheque','post_transfer'])],
            'delivery_address' => ['required'],
            'delivery_phone' => ['required'],
        ]);

        $reference = "ORD".random_int(100000, 199999).strtoupper(substr(md5(microtime()),rand(0,9),7));
        while ( Order::where( 'reference', $reference )->exists() ){
            $reference = "ORD".random_int(100000, 199999).strtoupper(substr(md5(microtime()),rand(0,9),7));
        }
        $validated_data['reference'] = $reference;

        $auth_user = auth('api')->user();
        $validated_data['placer_user_id'] = $auth_user->id;

        $validated_data['seller_table'] = $seller_table;
        $validated_data['seller_id'] = $seller_id;

        $order = Order::create($validated_data);

        if ( isset($validated_data['product_variations']) && count($validated_data['product_variations']) ){
            for ($i=0; $i < count($validated_data['product_variations']); $i++) {
                $variation_data = $validated_data['product_variations'][$i];
                $variation_data['owner_table'] = 'orders';
                $variation_data['owner_id'] = $order->id;
                $variation = (new ProductVariationController)->store(new Request(array_filter( $variation_data) ) );
            }
        }

        $address_data = $validated_data['delivery_address'];
        $address_data['owner_table'] = 'orders';
        $address_data['owner_id'] = $order->id;
        $address_data['adder_user_id'] = $auth_user->id;
        $address = Address::create($address_data);

        $phone_data = $validated_data['delivery_phone'];
        $phone_data['owner_table'] = 'orders';
        $phone_data['owner_id'] = $order->id;
        $phone_data['adder_user_id'] = $auth_user->id;
        $phone = Phone::create($phone_data);

        $product = Product::find($validated_data['product_id']);
        if ($product->entry_type=='product'){
            if ($product->stock_available > 1){
                $product->update(['stock_available' => $product->stock_available-1 ]);
            } else {
                $product->update(['stock_available' => 0,'status'=>'unavailable']);
            }
        }

        if ($product->entry_type=='product_and_or_service' && is_numeric($product->stock_available)  && $product->stock_available > 0){
            $product->update(['stock_available' => $product->stock_available-1 ]);
        }

        $pin = Pin::where([ 'item_table' => 'products', 'item_id' => $validated_data['product_id'], 'adder_user_id' =>$auth_user->id ])->first();

        if ($pin) $pin->delete();

        $response['order'] = new OrderResource( Order::find($order->id) );
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
        $order = Order::where( 'reference' , $reference )->first();
        if ($order){
            return response()->json( (new OrderResource($order)) );
        }
        abort(404, 'Order with ref: '.$reference.' not found');
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
        $response = [];

        $seller_table = request()->segments()[env('API_DOMAIN')?1:2];

        $validated_data = $request->validate([
            'payment_method' => ['string', Rule::in(['cash','post_cheque','post_transfer'])],
            'delivery_fee' => ['integer'],
            'estimated_delivery_datetime' => ['date:Y-m-d H:i:s'],
            'status' => ['string', Rule::in(['placed','delivery_fee_set','payment_made','payment_confirmed','delivered','completed','cancelled'])],
            'visible_to_placer' => ['boolean'],
            'visible_to_seller' => ['boolean'],
            'visible_to_admin' => ['boolean'],
            'images' => ['array'], // fresh list
            'old_images_to_delete_from_storage' => ['array'],
            'images_to_refresh_in_db' => ['array'],
        ]);

        $auth_user = auth('api')->user();
        $order = Order::find($id);

        if ( isset($validated_data['status']) ){

            switch ($validated_data['status']) {
                case 'delivery_fee_set':
                    if (is_numeric($validated_data['delivery_fee'])){
                        $validated_data['delivery_fee_set_datetime'] = now()->toDateTimeString();
                        $validated_data['amount_due_final'] = $order->amount_due_provisional + $validated_data['delivery_fee'];
                    } else {
                        abort(422, 'Delivery fee is missing');
                        unset($validated_data['status']);
                    }
                    break;

                case 'payment_made':
                    $validated_data['payment_made_datetime'] = now()->toDateTimeString();
                    break;

                case 'payment_confirmed':
                    $validated_data['payment_confirmed_datetime'] = now()->toDateTimeString();
                    $validated_data['intermediary_admin_user_id'] = $auth_user->id;
                    break;

                case 'delivered':
                    $validated_data['delivered_datetime'] = now()->toDateTimeString();
                    break;

                case 'completed':
                    $validated_data['completed_datetime'] = now()->toDateTimeString();
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        $order->update($validated_data);

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
                $variation = OrderVariation::find($variation_data['id']);
                $variation->delete();
            }
        }

        if (isset($validated_data['post_payment_receipt'])){
            $image_data = $validated_data['post_payment_receipt'];
            $image_data['title'] = $order->reference.' (order image)';
            $image_data['alt'] = 'This image is a order image for the entry "'.$order->reference;
            $image_data['tag'] = 'post_receipt';
            $image_data['owner_table'] = 'orders';
            $image_data['owner_id'] = $order->id;
            $image = (new ImageController)->store( new Request(array_filter( $image_data) ) );
        }

        $response['order'] = new OrderResource( Order::find($order->id) );
        $response['auth_user'] = new UserResource( auth('api')->user() );
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
