<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 26/10/2018
 * Time: 17:39
 */

namespace Z1lab\OpenID\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Z1lab\OpenID\Client as OpenID;

class LogoutController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (Auth::check()) {
            $client = new Client(['base_uri' => config('openid.server')]);
            $headers = [
                'Authorization' => 'Bearer ' . Cookie::get(OpenID::$access_cookie),
            ];

            try {
                $client->post('/api/actions/logout', ['headers' => $headers]);
            } catch (\Exception $e) {
                return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
            }
        }

        return (new JsonResponse())
            ->withCookie(
                Cookie::forget(OpenID::$access_cookie)
            )
            ->withCookie(
                Cookie::forget(OpenID::$refresh_cookie)
            )
            ->withCookie(
                Cookie::forget(OpenID::$openid_cookie)
            );
    }
}
