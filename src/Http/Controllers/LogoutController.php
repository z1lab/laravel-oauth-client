<?php

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
                $client->post('/logout');
            } catch (\Exception $exception) {
                return new JsonResponse(['success' => FALSE], 400);
            }
            Cookie::queue(Cookie::forget(\OpenID\Client\Client::$access_cookie));
            Cookie::queue(Cookie::forget(\OpenID\Client\Client::$refresh_cookie));
            Cookie::queue(Cookie::forget(\OpenID\Client\Client::$openid_cookie));
            Request::session()->invalidate();
        }

        return new JsonResponse(['success' => TRUE]);
    }
}