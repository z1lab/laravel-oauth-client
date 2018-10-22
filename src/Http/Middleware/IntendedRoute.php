<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 22/10/2018
 * Time: 19:22
 */

namespace OpenID\Client\Http\Middleware;


use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class IntendedRoute
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     **/
    public function handle($request, $next)
    {
        if ($request->isMethod('GET')) {
            Session::put('url.intended', URL::full());
        }

        return $next($request);
    }
}