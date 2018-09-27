<?php

namespace OpenID\Client;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class Client
{
    /**
     * The name for API token cookies.
     *
     * @var string
     */
    public static $cookie = 'laravel_token';

    /**
     * Indicates if Passport should unserializes cookies.
     *
     * @var bool
     */
    public static $unserializesCookies = false;

    /**
    * Indicates if Passport should ignore incoming CSRF tokens.
    *
    * @var bool
    */
    public static $ignoreCsrfToken = false;

    /**
     * Get or set the name for API token cookies.
     *
     * @param  string|null  $cookie
     * @return string|static
     */
    public static function cookie($cookie = null)
    {
        if (is_null($cookie)) {
            return static::$cookie;
        }

        static::$cookie = $cookie;

        return new static;
    }

    /**
    * Instruct Passport to enable cookie serialization.
    *
    * @return static
    */
    public static function withCookieSerialization()
    {
        static::$unserializesCookies = true;

        return new static;
    }

    /**
     * Instruct Passport to disable cookie serialization.
     *
     * @return static
     */
    public static function withoutCookieSerialization()
    {
        static::$unserializesCookies = false;

        return new static;
    }

    /**
     * @param array $scopes
     * @return string
     */
    public function login(array $scopes)
    {
        array_prepend($scopes, 'openid');

        $query = http_build_query([
            'client_id'     => Config::get('openid-client.client.id'),
            'redirect_uri'  => URL::route('openid-client.callback'),
            'response_type' => 'code',
            'scope'         => implode(' ', $scopes),
        ]);
        return Config::get('openid-client.server') . "/oauth/authorize?$query";
    }
}