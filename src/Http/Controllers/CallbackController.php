<?php

namespace Z1lab\OpenID\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Z1lab\OpenID\Client as OpenID;

class CallbackController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        $this->validate($request);

        $code = $request->get('code');
        $client = new Client(['base_uri' => config('openid.server')]);

        $params = [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('openid.client.id'),
            'client_secret' => config('openid.client.secret'),
            'redirect_uri'  => URL::route('openid.callback'),
            'code'          => $code,
        ];

        try {
            $response = $client->post('oauth/token', ['form_params' => $params]);

            $status = $response->getStatusCode();
            $response = json_decode($response->getBody());

            if (isset($response->error)) throw new HttpException($status, $response['error']);

        } catch (\Exception $exception) {
            $this->handleErrors($exception);
        }

        $auth_expires = $response->expires_in / 60;
        $refresh_expires = $response->refresh_expires_in / 60;

        return Redirect::to(Cookie::get('url_intended'))
            ->withCookie(
                Cookie::make(
                    OpenID::$access_cookie,
                    $response->access_token,
                    $auth_expires,
                    NULL,
                    NULL,
                    FALSE,
                    FALSE
                )
            )
            ->withCookie(
                Cookie::make(
                    OpenID::$openid_cookie,
                    $response->id_token,
                    $auth_expires,
                    NULL,
                    NULL,
                    FALSE,
                    FALSE
                )
            )
            ->withCookie(
                Cookie::make(
                    OpenID::$refresh_cookie,
                    $response->refresh_token,
                    $refresh_expires
                )
            );
    }

    /**
     * @param Request $request
     */
    private function validate(Request $request)
    {
        if ($request->has('error')) {
            switch ($request->query('error')) {
                case 'access_denied':
                    $status = Response::HTTP_UNAUTHORIZED;
                    $message = $request->has('message')
                        ? $request->query('message')
                        : NULL;
                    break;

                case 'temporarily_unavailable':
                    $status = Response::HTTP_SERVICE_UNAVAILABLE;
                    $message = $request->has('message')
                        ? $request->query('message')
                        : NULL;
                    break;

                case 'invalid_request':
                case 'unauthorized_client':
                case 'unsupported_response_type':
                case 'invalid_scope':
                case 'server_error':
                default:
                    $status = $request->server->get('REDIRECT_STATUS', Response::HTTP_INTERNAL_SERVER_ERROR);
                    $message = $request->has('message')
                        ? $request->query('message')
                        : NULL;
            }

            throw new HttpException($status, $message);
        }
    }

    /**
     * @param \Exception $exception
     */
    private function handleErrors(\Exception $exception)
    {
        if ($exception instanceof ServerException || $exception instanceof ClientException) {
            $result = json_decode($exception->getResponse()->getBody());
            $message = isset($result->message)
                ? $result->message
                : $exception->getMessage();

            throw new HttpException($exception->getResponse()->getStatusCode(), $message);
        }

        throw new HttpException($exception->getCode() >= 400 && $exception->getCode() < 600
            ? $exception->getCode()
            : 400, $exception->getMessage());
    }
}
