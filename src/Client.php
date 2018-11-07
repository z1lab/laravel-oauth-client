<?php

namespace Z1lab\OpenID;

use Illuminate\Support\Facades\URL;

class Client
{
    use ApiShortcuts;

    /**
     * @var string
     */
    public static $access_cookie = 'auth_token';

    /**
     * @var string
     */
    public static $openid_cookie = 'id_token';

    /**
     * @var string
     */
    public static $refresh_cookie = 'refresh_token';

    /**
     * @param array $scopes
     * @return string
     */
    public function askForToken(array $scopes = [])
    {
        $scopes = array_prepend($scopes, 'openid');

        $query = http_build_query([
            'client_id'     => config('openid.client.id'),
            'redirect_uri'  => URL::route('openid.callback'),
            'response_type' => 'code',
            'scope'         => implode(' ', $scopes),
        ]);

        return str_finish(config('openid.server'), '/') . "oauth/authorize?$query";
    }
}
