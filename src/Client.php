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

        if ($refresh_client_token = Cache::get('client_refresh_token', NULL))
            return $this->refreshClientToken($refresh_client_token, $scopes);

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

        return $this->interpretBody($response->getBody());
    }

    /**
     * @param string $client_refresh_token
     * @param array $scopes
     * @return null|string
     */
    private function refreshClientToken(string $client_refresh_token, array $scopes = [])
    {
        $client = new \GuzzleHttp\Client(['base_uri' => config('openid.server')]);

        try {
            $response = $client->post('oauth/token', [
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $client_refresh_token,
                    'client_id'     => config('openid.client.id'),
                    'client_secret' => config('openid.client.secret'),
                    'scope'         => empty($scopes) ? '' : implode(' ', $scopes),
                ],
            ]);
        } catch (\Exception $e) {
            return NULL;
        }

        return $this->interpretBody($response->getBody());
    }

    /**
     * @param string $body
     * @return string
     */
    private function interpretBody(string $body)
    {
        $result = json_decode($body);

        $auth_expires = $result->expires_in / 60;
        $refresh_expires = $result->refresh_expires_in / 60;

        Cache::remember('client_refresh_token', $refresh_expires, function () use ($result) {
            return $result->refresh_token;
        });

        return Cache::remember('client_token', $auth_expires, function () use ($result) {
            return $result->access_token;
        });
    }
}
