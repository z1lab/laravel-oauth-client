<?php

namespace OpenID\Client;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Client
{
    /**
     * @var string
     */
    public static $access_cookie = 'auth_token';

    /**
     * @var string
     */
    public static $openid_cookie = 'identification_token';

    /**
     * @var string
     */
    public static $refresh_cookie = 'refresh_token';

    /**
     * @param array $scopes
     * @return RedirectResponse
     */
    private function askForToken(array $scopes = [])
    {
        $scopes = array_prepend($scopes, 'openid');

        $query = http_build_query([
            'client_id'     => config('openid.client.id'),
            'redirect_uri'  => URL::route('openid.callback'),
            'response_type' => 'code',
            'scope'         => implode(' ', $scopes),
        ]);
        return new RedirectResponse(config('openid.server') . "/oauth/authorize?$query");
    }

    /**
     * @param array $scopes
     * @return bool
     */
    private function refreshToken(array $scopes = []) : bool
    {
        $refresh_token = Cookie::get(self::$refresh_cookie, '');
        if (empty($refresh_token)) {
            return false;
        }

        $scopes = array_prepend($scopes, 'openid');
        $client = new \GuzzleHttp\Client(['base_uri' => config('openid.server')]);
        try {
            $respone = $client->post('/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id' => config('openid.client.id'),
                    'client_secret' => config('openid.client.secret'),
                    'scope'         => implode(' ', $scopes),
                ]
            ]);
            $result = json_decode($respone->getBody());
            $expires = Carbon::now()->addSeconds($result->expires_in);
            Cookie::queue(Cookie::make(self::$access_cookie, $result->access_token, $expires->diffInMinutes()));
            Cookie::queue(Cookie::make(self::$openid_cookie, $result->id_token, $expires->diffInMinutes()));
            Cookie::queue(Cookie::make(self::$refresh_cookie, $result->refresh_token, $expires->diffInMinutes()));

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return void
     */
    private function routes()
    {
        Route::get('openid/callback', 'OpenID\Client\Http\Controllers\CallbackController@callback')
            ->name('openid.callback');

        Route::post('logout', 'OpenID\Client\Http\Controllers\LogoutController@logout')->name('logout');
    }
}