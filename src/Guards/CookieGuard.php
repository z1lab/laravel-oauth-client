<?php

namespace OpenID\Client\Guards;

use Carbon\Carbon;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Cookie;
use OpenID\Client\Client;
use OpenID\Client\Traits\CreateUserTrait;
use OpenID\Client\Traits\ValidateTokenTrait;

class CookieGuard implements Guard
{
    use GuardHelpers, ValidateTokenTrait, CreateUserTrait;

    /**
     * CookieGuard constructor.
     */
    public function __construct()
    {
        $this->provider = null;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            if ($this->user->expires_at < Carbon::now()) {
                return null;
            }

            return $this->user;
        }

        $token = Cookie::get(Client::$access_cookie, '');

        if (empty($token)) {
            return null;
        }

        $access_token = $this->validateToken($token);
        if (!$access_token) {
            return null;
        }

        $token = Cookie::get(Client::$openid_cookie, '');

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
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return true;
    }
}