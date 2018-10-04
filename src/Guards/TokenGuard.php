<?php

namespace OpenID\Client\Guards;

use Carbon\Carbon;
use Illuminate\Http\Request;
use OpenID\Client\Traits\CreateUserTrait;
use OpenID\Client\Traits\ValidateTokenTrait;

class TokenGuard
{
    use ValidateTokenTrait, CreateUserTrait;

    /**
     * Get the currently authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(Request $request)
    {
        if (!is_null($this->user)) {
            if ($this->user->expires_at < Carbon::now()) {
                return $this->user = null;
            }

            return $this->user;
        }

        $token = $this->getTokenForRequest($request);

        if (empty($token)) {
            return null;
        }

        $access_token = $this->validateToken($token);
        if (!$access_token) {
            return null;
        }

        $token = $this->geIdTokenForRequest($request);

        if (empty($token)) {
            return null;
        }

        $id_token = $this->validateToken($token);

        if (!$id_token || $id_token->getClaim('sub') !== $access_token->getClaim('sub')) {
            return null;
        }

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