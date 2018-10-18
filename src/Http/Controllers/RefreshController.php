<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 18/10/2018
 * Time: 19:14
 */

namespace OpenID\Client\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use OpenID\Client\Client;

class RefreshController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request)
    {
        $refresh_token = Cookie::get(Client::$refresh_cookie, '');
        if (empty($refresh_token)) {
            return new JsonResponse(['success' => false], 400);
        }

        $scopes = 'openid' . $request->filled('scopes') ? (' ' . $request->get('scopes')) : '';
        $client = new \GuzzleHttp\Client(['base_uri' => config('openid.server')]);
        try {
            $respone = $client->post('/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id' => config('openid.client.id'),
                    'client_secret' => config('openid.client.secret'),
                    'scope' => $scopes,
                ]
            ]);
            $result = json_decode($respone->getBody());
            $expires = Carbon::now()->addSeconds($result->expires_in);
            $respone = new JsonResponse(['success' => true]);
            $minutes = $expires->diffInMinutes();

            return $respone->withCookie(Cookie::make(Client::$access_cookie, $result->access_token, $minutes))
                ->withCookie(Cookie::make(Client::$openid_cookie, $result->id_token, $minutes))
                ->withCookie(Cookie::make(Client::$refresh_cookie, $result->refresh_token, $minutes));
        } catch (\Exception $exception) {
            return new JsonResponse(['success' => false], 400);
        }
    }
}