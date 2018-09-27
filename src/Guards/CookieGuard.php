<?php

namespace OpenID\Client\Guards;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use OpenID\Client\Client;
use OpenID\Client\User;

class CookieGuard
{
    /** The currently authenticated user.
    *
    * @var User
    */
    protected $user;

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null|User
     * @throws \Exception
     */
    protected function user()
    {
        if (!Cookie::has(Client::cookie())) {
            return null;
        }

        if (!is_null($this->user)) {
            if ($this->user->expires_at >= Carbon::now()) {
                return $this->user;
            }

            return $this->user = null;
        }

        return $this->user = $this->getUserFromCookie(Cookie::get(Client::cookie()));
    }

    /**
     * @param string $cookie
     * @return null|User
     * @throws \Exception
     */
    private function getUserFromCookie(string $cookie)
    {
        try {
            $token = (new Parser())->parse($cookie);

            $this->validateToken($token, 'OpenID');
        } catch (\InvalidArgumentException $exception) {
            throw new \Exception($exception->getMessage(), 401);
        } catch (\RuntimeException $exception) {
            throw  new \Exception('Error while decoding to JSON', 401);
        }

        $user = $this->getUserByToken($token);

        if (! $user) {
            return null;
        }

        $user->access_token = Session::get('access_token');

        return $user;
    }

    /**
     * @param Token $token
     * @param string $name
     * @throws \Exception
     */
    private function validateToken(Token $token, string $name)
    {
        try {
            if (!$token->verify(new Sha256(), 'file://' . Config::get('openid-client.key'))) {
                throw new \Exception("$name token could not be verified", 401);
            }
        } catch (\BadMethodCallException $exception) {
            throw new \Exception("$name token is not signed", 401);
        }

        $data = new ValidationData();
        $data->setCurrentTime(time());

        if (!$token->validate($data)) {
            throw new \Exception("$name token is invalid", 401);
        }

    }

    /**
     * @param Token $token
     * @return User
     */
    private function getUserByToken(Token $token)
    {
        $user = new User(['id' => $token->getClaim('sub'), 'expires_at' => $token->getClaim('exp')]);
        $special_claims = array_except(array_keys($token->getClaims()), ['sub', 'aud', 'jti', 'iat', 'iss', 'nbf']);
        foreach ($special_claims as $claim) {
            $user->$claim = $token->getClaim($claim);
        }

        return $user;
    }

    /**
     * Determine if current user is authenticated. If not, throw an exception.
     *
     * @return User
     *
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Exception
     */
    public function authenticate()
    {
        if (! is_null($user = $this->user())) {
            return $user;
        }

        throw new AuthenticationException;
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser()
    {
        return ! is_null($this->user);
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     * @throws \Exception
     */
    public function check()
    {
        return ! is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     * @throws \Exception
     */
    public function guest()
    {
        return ! $this->check();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return string|null
     * @throws \Exception
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }

        return null;
    }

    /**
     * Set the current user.
     *
     * @param  User  $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}