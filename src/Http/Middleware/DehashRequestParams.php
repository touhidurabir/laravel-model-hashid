<?php

namespace Touhidurabir\ModelHashid\Http\Middleware;

use Closure;
use Throwable;
use Hashids\Hashids;
use Illuminate\Http\Response;

class DehashRequestParams {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * 
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if ( ! config('hasher.enable') ) {

            return $next($request);    
        }

        $dehashedParams = [];

        foreach ($request->all() as $name => $value) {

            $dehashedParams[$name] = decode_hashids($value);
        }

        $request->merge($dehashedParams);
        
        return $next($request);
    }
}
