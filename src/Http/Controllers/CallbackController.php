<?php

namespace OpenID\Client\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Lcobucci\JWT\Parser;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CallbackController
{
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            switch ($request->get('error')) {
                case 'access_denied':
                    throw new HttpException(401);
                    break;
                case 'temporarily_unavailable':
                    throw new HttpException(503);
                    break;
                case 'invalid_request':
                case 'unauthorized_client':
                case 'unsupported_response_type':
                case 'invalid_scope':
                case 'server_error':
                default:
                    throw new HttpException($request->server->get('REDIRECT_STATUS', 500));
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
            $token = (new Parser())->parse($response->access_token);
            Cookie::queue(Cookie::make(\OpenID\Client\Client::$access_cookie, $response->access_token,
                Carbon::createFromTimestamp($token->hasClaim('exp'))->diffInMinutes()));
            Cookie::queue(Cookie::make(\OpenID\Client\Client::$openid_cookie, $response->id_token,
                Carbon::createFromTimestamp($token->hasClaim('exp'))->diffInMinutes()));
            Cookie::queue(Cookie::make(\OpenID\Client\Client::$refresh_cookie, $response->refresh_token,
                Carbon::createFromTimestamp($token->hasClaim('exp'))->diffInMinutes()));
        } catch (\Exception $exception) {
            if ($exception instanceof ServerException || $exception instanceof ClientException) {
                throw new HttpException($exception->getResponse()->getStatusCode(), $exception->getMessage());
            }
            throw new HttpException($exception->getCode() >= 400 && $exception->getCode() < 600? $exception->getCode() : 400,
                $exception->getMessage());
        }
        return Redirect::intended();
    }
}