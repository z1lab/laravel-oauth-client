<?php

namespace Z1lab\OpenID\Guards;

use Carbon\Carbon;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Cookie;
use Z1lab\OpenID\Client;
use Z1lab\OpenID\Traits\CreateUserTrait;
use Z1lab\OpenID\Traits\ValidateTokenTrait;

class CookieGuard implements Guard
{
    use GuardHelpers, ValidateTokenTrait, CreateUserTrait;

    /**
     * CookieGuard constructor.
     */
    public function __construct()
    {
        $this->provider = NULL;
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            if ($this->user->expires_at < Carbon::now()) return NULL;

            return $this->user;
        }

        $token = Cookie::get(Client::$access_cookie, '');
        if (empty($token)) return NULL;

        $access_token = $this->validateToken($token);
        if (!$access_token) return NULL;

        $token = Cookie::get(Client::$openid_cookie, '');
        if (empty($token)) return NULL;

        $id_token = $this->validateToken($token);
        if (!$id_token || $id_token->getClaim('sub') !== $access_token->getClaim('sub')) return NULL;

        return $this->user = $this->createUserFromToken($id_token);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return TRUE;
    }
}
