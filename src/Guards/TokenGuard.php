<?php

namespace OpenID\Client\Guards;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Config;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use OpenID\Client\Client;
use OpenID\Client\User;

class TokenGuard
{
    /**
     * Get the user for the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function user(Request $request)
    {
        if ($request->bearerToken()) {
            return $this->authenticateViaBearerToken($request);
        }

        return null;
    }

    /**
     * Authenticate the incoming request via the Bearer token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return null|\OpenID\Client\User
     */
    protected function authenticateViaBearerToken($request)
    {
       try {
            $access_token = $this->validateAuthenticatedRequest($request);

            if (!$access_token) {
                return null;
            }

            try {
                if ($request->hasCookie(Client::cookie())) {
                    $token = (new Parser())->parse($request->cookie(Client::cookie()));

                    $this->validateToken($token, 'OpenID');

                    if ($access_token->getClaim('sub') !== $token->getClaim('sub')) {
                        throw new \Exception('OpenID token is invalid', 401);
                    }
                }
            } catch (\InvalidArgumentException $exception) {
                throw new \Exception($exception->getMessage(), 401);
            } catch (\RuntimeException $exception) {
                throw  new \Exception('Error while decoding to JSON', 401);
            }

            $user = $this->getUserByToken($token ?? $access_token);

            if (! $user) {
                return null;
            }

            $user->access_token = (string) $access_token;

            return $user;
        } catch (\Exception $e) {
            $request->headers->set( 'Authorization', '', true );

            Container::getInstance()->make(
                ExceptionHandler::class
            )->report($e);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return Token
     * @throws \Exception
     */
    private function validateAuthenticatedRequest(Request $request)
    {
        if (!$request->headers->has('authorization')) {
            throw new \Exception('Missing "Authorization" header', 401);
        }

        $header = $request->headers->get('authorization');
        $jwt = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $header[0]));

        try {
            $token = (new Parser())->parse($jwt);

            $this->validateToken($token, 'Access');

            if (!in_array(Config::get('openid-client.client.id'), explode(' ', $token->getClaim('aud')))) {
                throw new \Exception('Access token is invalid', 401);
            }

            return $token;
        } catch (\InvalidArgumentException $exception) {
            throw new \Exception($exception->getMessage(), 401);
        } catch (\RuntimeException $exception) {
            throw  new \Exception('Error while decoding to JSON', 401);
        }
    }

    /**
     * @param Token $token
     * @param string $name
     * @throws \Exception
     */
    private function validateToken(Token $token, string $name)
    {
        try {
            if (!$token->verify(new Sha256(), 'file://' . Config::get('openid-client.key'))) {
                throw new \Exception("$name token could not be verified", 401);
            }
        } catch (\BadMethodCallException $exception) {
            throw new \Exception("$name token is not signed", 401);
        }

        $data = new ValidationData();
        $data->setCurrentTime(time());

        if (!$token->validate($data)) {
            throw new \Exception("$name token is invalid", 401);
        }

    }

    /**
     * @param Token $token
     * @return User
     */
    private function getUserByToken(Token $token)
    {
        $user = new User(['id' => $token->getClaim('sub'), 'expires_at' => $token->getClaim('exp')]);
        $special_claims = array_except(array_keys($token->getClaims()), ['sub', 'aud', 'jti', 'iat', 'iss', 'nbf']);
        foreach ($special_claims as $claim) {
            $user->$claim = $token->getClaim($claim);
        }

        return $user;
    }
}
