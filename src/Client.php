<?php

namespace Z1lab\OpenID;

use Illuminate\Support\Facades\Cache;
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


    /**
     * @param array $scopes
     * @return string|null
     */
    public function getClientToken(array $scopes = [])
    {
        if ($client_token = Cache::get('client_token', NULL))
            return $client_token;

        $client = new \GuzzleHttp\Client(['base_uri' => config('openid.server')]);

        try {
            $response = $client->post('oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => config('openid.client.id'),
                    'client_secret' => config('openid.client.secret'),
                    'scope' => empty($scopes) ? '' : implode(' ', $scopes),
                ],
            ]);
        } catch (\Exception $e) {
            return NULL;
        }

        $result = json_decode($response->getBody());

        return Cache::remember('client_token', $result->expires_in / 60, function () use ($result) {
            return $result->access_token;
        });
    }
}
