<?php

namespace App\Http\Middleware;

use Closure;
use App\OAuth\OAuthHandler;

class CheckOAuth
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $strService
     * @return mixed
     */
    public function handle($request, Closure $next, $strService)
    {
        $OAuthHandler = new OAuthHandler($strService);
        if($request->session()->has($strService .'-auth'))
        {
            if($OAuthHandler->isAuthValid($request->session()->get($strService .'-auth')))
            {
                return $next($request);
            }
        }
        return redirect($OAuthHandler->provider->local_login);
    }
}