<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntityPreferenceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $entity_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($entity_id, Request $request)
    {
        $entity_table = request()->segments()[env('API_DOMAIN')?1:2];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $entity_id
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($entity_id, Request $request, $id)
    {
        $entity_table = request()->segments()[env('API_DOMAIN')?1:2];
    }
}
