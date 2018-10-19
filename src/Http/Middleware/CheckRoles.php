<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 19/10/2018
 * Time: 10:20
 */

namespace OpenID\Client\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use OpenID\Client\Exceptions\MissingRoleException;

class CheckRoles
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \OpenID\Client\Exceptions\MissingRoleException
     */
    public function handle($request, $next, ...$roles)
    {
        if (!$request->user()) {
            throw new AuthenticationException();
        }

        foreach ($roles as $role) {
            if (!in_array($role, $request->user()->roles)) {
                throw new MissingRoleException($role);
            }
        }

        return $next($request);
    }
}