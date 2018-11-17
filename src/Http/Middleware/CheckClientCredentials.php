<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 17/11/2018
 * Time: 17:23
 */

namespace Z1lab\OpenID\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Z1lab\OpenID\Exceptions\MissingScopeException;
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
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws MissingScopeException
     */
    public function handle($request, \Closure $next, ...$scopes)
    {
        $jwt = $request->bearerToken();
        if ($jwt === NULL)
            throw new AuthenticationException;

        $token = $this->validateToken($jwt);
        if ($token === NULL)
            throw new AuthenticationException;

        $this->checkScopes($token, $scopes);

        return $next($request);
    }

    /**
     * @param \Lcobucci\JWT\Token $token
     * @param mixed $scopes
     * @throws MissingScopeException
     */
    private function checkScopes($token, $scopes)
    {
        if (in_array('*', $tokenScopes = $token->getClaim('scopes'))) {
            return;
        }

        foreach ($scopes as $scope) {
            if (! in_array($scope, $tokenScopes)) {
                throw new MissingScopeException($scope);
            }
        }
    }
}