<?php

namespace Z1lab\OpenID\Guards;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Z1lab\OpenID\Traits\CreateUserTrait;
use Z1lab\OpenID\Traits\ValidateTokenTrait;

class TokenGuard
{
    use ValidateTokenTrait, CreateUserTrait;

    /**
     * @var \Z1lab\OpenID\Models\User
     */
    protected $user;

    /**
     * @param Request $request
     * @return null|\Z1lab\OpenID\Models\User
     */
    public function user(Request $request)
    {
        if (!is_null($this->user)) {
            if ($this->user->expires_at < Carbon::now()) return $this->user = NULL;

            return $this->user;
        }

        $token = $this->getTokenForRequest($request);
        if (empty($token)) return NULL;

        $access_token = $this->validateToken($token);
        if (!$access_token) return NULL;

        $token = $this->geIdTokenForRequest($request);
        if (empty($token)) return NULL;

        $id_token = $this->validateToken($token);
        if (!$id_token || $id_token->getClaim('sub') !== $access_token->getClaim('sub')) return NULL;

        return $this->user = $this->createUserFromToken($id_token);
    }

    /**
     * Get the token for the current request.
     *
     * @param Request $request
     * @return string
     */
    public function getTokenForRequest(Request $request)
    {
        return $request->bearerToken();
    }


    /**
     * Get the id_token for the current request.
     *
     * @param Request $request
     * @return string
     */
    public function geIdTokenForRequest(Request $request)
    {
        return $request->header('Id_Token', '');
    }
}
