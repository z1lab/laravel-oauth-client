<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 13/02/2019
 * Time: 16:13
 */

namespace Z1lab\OpenID\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Z1lab\OpenID\Facades\OpenidFacade;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('GET') && !$request->wantsJson() && $request->filled('token')) {
            $data = $request->query();
            unset($data['token']);

            $url = URL::current();
            if (!empty($data))
                $url .= ('?' . http_build_query($data));

            if (Auth::check()) {
                return new RedirectResponse($url);
            } else {
                return (new RedirectResponse(OpenidFacade::askForToken() . '&token=' . $request->query('token')))
                    ->withCookie(Cookie::make('url_intended', $url, 10));
            }
        }

        return $next($request);
    }
}
