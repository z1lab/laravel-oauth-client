<?php

namespace Z1lab\OpenID\Traits;

use Carbon\Carbon;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;

trait ValidateTokenTrait
{
    /**
     * @param string $jwt
     * @return \Lcobucci\JWT\Token|null
     */
    protected function validateToken(string $jwt)
    {
        $token = (new Parser())->parse($jwt);

        $data = new ValidationData();

        $data->setCurrentTime(Carbon::now()->getTimestamp());
        $data->setIssuer(str_finish(config('openid.server'), '/'));

        if ($token->validate($data) && $token->verify(new Sha256(), 'file://' . config('openid.key'))) {
            $audiences = explode(' ', $token->getClaim('aud'));

            return $token;
        }

        return NULL;
    }
}
