<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 19/10/2018
 * Time: 10:20
 */

namespace Z1lab\OpenID\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Z1lab\OpenID\Exceptions\MissingRoleException;

class CheckRoles
{
    /**
     * @param       $request
     * @param       $next
     * @param mixed ...$roles
     * @return mixed
     * @throws AuthenticationException
     * @throws MissingRoleException
     */
    public function handle($request, $next, ...$roles)
    {
        if (!$request->user()) throw new AuthenticationException();

        foreach ($roles as $role) {
            if (!in_array($role, $request->user()->roles)) throw new MissingRoleException($role);
        }

        return $next($request);
    }
}
