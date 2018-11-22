<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 17/11/2018
 * Time: 17:23
 */

namespace Z1lab\OpenID\Http\Middleware;

use Z1lab\OpenID\Traits\ValidateTokenTrait;

class CheckClientCredentials
{
    use ValidateTokenTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  mixed ...$scopes
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$scopes)
    {
        $jwt = $request->bearerToken();
        if ($jwt === NULL) abort(401);

        $token = $this->validateToken($jwt);
        if ($token === NULL) abort(401);

        $this->checkScopes($token, $scopes);

        return $next($request);
    }

    /**
     * @param \Lcobucci\JWT\Token $token
     * @param mixed $scopes
     */
    private function checkScopes($token, $scopes)
    {
        if (in_array('*', $tokenScopes = $token->getClaim('scopes'))) {
            return;
        }

        foreach ($scopes as $scope) {
            if (! in_array($scope, $tokenScopes)) abort(403);
        }
    }
}