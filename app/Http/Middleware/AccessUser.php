<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiAuthenticationException;
use App\Exceptions\ApiAuthorizationException;
use Closure;
use Illuminate\Http\Request;

class AccessUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws ApiAuthorizationException
     * @throws ApiAuthenticationException
     */
    public function handle(Request $request, Closure $next)
    {
        //check if authenticated
        if(!auth()->check()){
            throw new ApiAuthenticationException();
        }

        //check if authorized
        $routeName = $request->route()->getName();
        if(!auth()->user()->permissions()->contains($routeName)){
            throw new ApiAuthorizationException();
        }

        return $next($request);
    }
}
