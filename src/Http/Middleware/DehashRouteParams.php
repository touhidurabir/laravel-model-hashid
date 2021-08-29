<?php

namespace Touhidurabir\ModelHashid\Http\Middleware;

use Closure;
use Throwable;
use Hashids\Hashids;
use Illuminate\Http\Response;

class DehashRouteParams {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if ( ! config('hasher.enable') ) {

            return $next($request);    
        }

        $parameters = $request->route()->parameters();

        foreach ($parameters as $parameter => $value) {

            $request->route()->setParameter($parameter, decode_hashid($value));
        }
        
        return $next($request);
    }
}
