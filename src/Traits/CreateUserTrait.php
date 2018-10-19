<?php

namespace OpenID\Client\Traits;

use Carbon\Carbon;
use Lcobucci\JWT\Token;
use OpenID\Client\Address;
use OpenID\Client\User;

trait CreateUserTrait
{
    /**
     * @param Token $token
     * @return User
     */
    protected function createUserFromToken(Token $token)
    {
        $attributes = [];

        $attributes['id'] = $token->getClaim('sub');
        $attributes['expires_at'] = Carbon::createFromTimestamp($token->getClaim('exp'));
        $attributes['auth_time'] = Carbon::createFromTimestamp($token->getClaim('auth_time'));
        $attributes['name'] = $token->getClaim('name');
        $attributes['social_name'] = $token->getClaim('social_name');
        $attributes['nickname'] = $token->getClaim('nickname');
        $attributes['username'] = $token->getClaim('preferred_username');
        $attributes['avatar'] = $token->getClaim('picture');
        $attributes['email'] = $token->getClaim('email');
        $attributes['email_verified'] = $token->getClaim('email_verified');
        if ($attributes['email_verified'] === 'false') {
            $attributes['email_verified'] = false;
        } else {
            $attributes['email_verified'] = (bool) $attributes['email_verified'];
        }
        $attributes['gender'] = $token->getClaim('gender');
        $attributes['birthdate'] = $token->getClaim('birthdate');
        if (filled($attributes['birthdate'])) {
            $attributes['birthdate'] = Carbon::createFromFormat('Y-M-D', $attributes['birthdate']);
        }
        $attributes['phone'] = $token->getClaim('phone_number');
        $attributes['phone_verified'] = $token->getClaim('phone_number_verified');
        $attributes['address'] = $token->getClaim('address');
        if (filled($attributes['address'])) {
            $attributes['address'] = new Address(json_decode($attributes['address'], true));
        }
        $attributes['updated_at'] = Carbon::createFromTimestamp($token->getClaim('updated_at'));
        $attributes['roles'] = [];
        $roles = explode(' ', $token->getClaim('roles'));
        foreach ($roles as $role) {
            if (!empty($role)) {
                $attributes['roles'][] = $role;
            }
        }

        return new User($attributes);
    }
}