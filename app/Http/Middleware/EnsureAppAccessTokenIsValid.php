<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ConnectInstance;

class EnsureAppAccessTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (( !env('API_DOMAIN') && $request->path() == "api/core/state" )||(env('API_DOMAIN') && $request->path() == "core/state")) return $next($request);
        $connect_instance = ConnectInstance::where( 'app_access_token', $request->segments()[env('API_DOMAIN')?0:1] )->first();
        if ( $connect_instance && $connect_instance['status'] !== "ended" ){
            $request->route()->forgetParameter('app_access_token');
            return $next($request);
        } else {
            return abort(403);
        }
    }
}
