<?php

namespace OpenID\Client\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CallbackController
{
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            switch ($request->query('error')) {
                case 'access_denied':
                    throw new HttpException(401,
                        $request->has('message') ? $request->query('message') : null);
                    break;
                case 'temporarily_unavailable':
                    throw new HttpException(503,
                        $request->has('message') ? $request->query('message') : null);
                    break;
                case 'invalid_request':
                case 'unauthorized_client':
                case 'unsupported_response_type':
                case 'invalid_scope':
                case 'server_error':
                default:
                    throw new HttpException($request->server->get('REDIRECT_STATUS', 500),
                        $request->has('message') ? $request->query('message') : null);
                    break;
            }
        }

        $code = $request->get('code');
        $client = new Client(['base_uri' => config('openid.server')]);
        try {
            $response = $client->post('oauth/token', [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => config('openid.client.id'),
                    'client_secret' => config('openid.client.secret'),
                    'redirect_uri'  => URL::route('openid.callback'),
                    'code'          => $code,
                ],
            ]);
            $status = $response->getStatusCode();
            $response = json_decode($response->getBody());
            if (isset($response->error)) {
                throw new HttpException($status, $response['error']);
            }
        } catch (\Exception $exception) {
            if ($exception instanceof ServerException || $exception instanceof ClientException) {
                $result = json_decode($exception->getResponse()->getBody());
                $message = isset($result->message) ? $result->message : $exception->getMessage();
                throw new HttpException($exception->getResponse()->getStatusCode(), $message);
            }
            throw new HttpException($exception->getCode() >= 400 && $exception->getCode() < 600? $exception->getCode() : 400,
                $exception->getMessage());
        }

        $minutes = $response->expires_in / 60;

        return Redirect::to(Cookie::get('url_intended'))
            ->withCookie(Cookie::make(\OpenID\Client\Client::$access_cookie, $response->access_token, $minutes))
            ->withCookie(Cookie::make(\OpenID\Client\Client::$openid_cookie, $response->id_token, $minutes))
            ->withCookie(Cookie::make(\OpenID\Client\Client::$refresh_cookie, $response->refresh_token,
                $response->refresh_expires_in / 60));
    }
}