<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 17/11/2018
 * Time: 17:48
 */

namespace Z1lab\OpenID\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class ApiService
{
    /**
     * @var string
     */
    private $cacheKey;

    /**
     * ApiService constructor.
     */
    public function __construct()
    {
        $this->cacheKey = 'openid-user-' . Auth::user()->id;
    }

    /**
     * @param string $token
     * @return mixed
     */
    public function getUser(string $token)
    {
        if(config('app.env') !== 'production') {
            return Cache::rememberForever($this->cacheKey, function () use ($token){
                return $this->findUser($token);
            });
        }

        return $this->findUser($token);
    }

    /**
     * @param string $token
     * @return mixed
     */
    public function refreshUser(string $token)
    {
        $this->forgetUser();
        
        return $this->getUser($token);
    }

    /**
     * Remove logged user from cache
     */
    public function forgetUser()
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * @param string $token
     * @return mixed
     */
    private function findUser(string $token)
    {
        $client = new Client();
        $bearer = "Bearer {$token}";
        $id = Auth::user()->id;
        $url = str_finish(getenv('AUTH_SERVER'), '/') . 'api/' . getenv('API_VERSION') . "/users/{$id}";

        $response = $client->get($url, [
            'headers' => [
                'Authorization' => $bearer,
            ],
        ]);

        return json_decode($response->getBody());
    }
}
