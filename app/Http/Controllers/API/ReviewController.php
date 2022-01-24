<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $reviewed_thing_id
     * @return \Illuminate\Http\Response
     */
    public function index($reviewed_thing_id)
    {
        $reviewed_thing_table = request()->segments()[env('API_DOMAIN')?1:2];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $reviewed_thing_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($reviewed_thing_id, Request $request)
    {
        $reviewed_thing_table = request()->segments()[env('API_DOMAIN')?1:2];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $reviewed_thing_id
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($reviewed_thing_id, Request $request, $id)
    {
        $reviewed_thing_table = request()->segments()[env('API_DOMAIN')?1:2];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $reviewed_thing_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($reviewed_thing_id,$id)
    {
        $reviewed_thing_table = request()->segments()[env('API_DOMAIN')?1:2];
    }
}
