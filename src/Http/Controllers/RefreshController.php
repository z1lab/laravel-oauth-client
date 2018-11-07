<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 18/10/2018
 * Time: 19:14
 */

namespace Z1lab\OpenID\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Z1lab\OpenID\Client;
use Z1lab\OpenID\Http\Resources\User;
use Z1lab\OpenID\Traits\CreateUserTrait;
use Z1lab\OpenID\Traits\ValidateTokenTrait;

class RefreshController
{
    use CreateUserTrait, ValidateTokenTrait;
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request)
    {
        $refresh_token = Cookie::get(Client::$refresh_cookie, '');

        if (empty($refresh_token)) return new JsonResponse(['success' => FALSE], Response::HTTP_UNAUTHORIZED);

        $scopes = 'openid' . $request->filled('scopes') ? (' ' . $request->get('scopes')) : '';
        $client = new \GuzzleHttp\Client(['base_uri' => config('openid.server')]);

        $params = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id'     => config('openid.client.id'),
            'client_secret' => config('openid.client.secret'),
            'scope'         => $scopes,
        ];

        try {
            $response = $client->post('/oauth/token', ['form_params' => $params]);

            $result = json_decode($response->getBody());
            $auth_expires = $result->expires_in / 60;
            $refresh_expires = $result->refresh_expires_in / 60;

            $user = $this->createUserFromToken($this->validateToken($result->id_token));

            $data = collect($user->toArray());

            return (new User($data))->response()
                ->withCookie(
                    Cookie::make(
                        Client::$access_cookie,
                        $result->access_token,
                        $auth_expires
                    )
                )
                ->withCookie(
                    Cookie::make(
                        Client::$openid_cookie,
                        $result->id_token,
                        $auth_expires
                    )
                )
                ->withCookie(
                    Cookie::make(
                        Client::$refresh_cookie,
                        $result->refresh_token,
                        $refresh_expires
                    )
                );
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
