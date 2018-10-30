<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 26/10/2018
 * Time: 17:39
 */

namespace OpenID\Client\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

class LogoutController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (Auth::check()) {
            $client = new Client(['base_uri' => config('openid.server')]);

            try {
                $client->post('/api/actions/logout',
                    ['headers' => ['Authorization' => 'Bearer ' . Cookie::get(\OpenID\Client\Client::$access_cookie)]]);
            } catch (\Exception $exception) {
                return new JsonResponse(['success' => FALSE], 400);
            }
        }

        return (new JsonResponse(['success' => TRUE]))
            ->withCookie(Cookie::forget(\OpenID\Client\Client::$access_cookie))
            ->withCookie(Cookie::forget(\OpenID\Client\Client::$refresh_cookie))
            ->withCookie(Cookie::forget(\OpenID\Client\Client::$openid_cookie));
    }
}
