<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;

use App\Models\ProductVariation;
use App\Http\Resources\ProductVariationResource;

class ProductVariationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'name' => ['required', 'string', 'max:16'],
            'value' => ['required', 'string', 'max:16'],
            'price' => ['nullable', 'numeric', 'between:100,999999999'],
            'owner_table' => ['required', 'string', Rule::in(['products', 'orders','pins'])],
            'owner_id' => ['required', 'exists:'.$request->owner_table.',id', 'integer'],
        ]);

        $user = auth('api')->user();
        $validated_data['adder_user_id'] = $user->id;

        $product_variation = ProductVariation::create($validated_data);

        return new ProductVariationResource( ProductVariation::find($product_variation->id) );
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
