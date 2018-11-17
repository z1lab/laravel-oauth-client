<?php

namespace Z1lab\OpenID\Traits;

use Carbon\Carbon;
use Lcobucci\JWT\Token;
use Z1lab\OpenID\Models\User;

trait CreateUserTrait
{
    /**
     * @param Token $token
     * @return User
     */
    protected function createUserFromToken(Token $token)
    {
        $attributes = [];

        $this->defaults($attributes, $token);
        $this->timestamps($attributes, $token);
        $this->roles($attributes, $token);

        return new User($attributes);
    }

    /**
     * @param array $attributes
     * @param Token $token
     */
    private function defaults(array &$attributes, Token $token)
    {
        $map = [
            'id'             => 'sub',
            'name'           => 'name',
            'nickname'       => 'nickname',
            'avatar'         => 'picture',
            'email'          => 'email',
            'email_verified' => 'email_verified',
            'phone_verified' => 'phone_number_verified',
            'document'       => 'document',
        ];

        foreach ($map as $key => $value) {
            $attributes[$key] = $token->getClaim($value);
        }
    }

    /**
     * @param array $attributes
     * @param Token $token
     */
    private function timestamps(array &$attributes, Token $token)
    {
        $map = [
            'expires_at' => 'exp',
            'auth_time'  => 'auth_time',
            'updated_at' => 'updated_at',
        ];

        foreach ($map as $key => $value) {
            $attributes[$key] = Carbon::createFromTimestamp($token->getClaim($value));
        }

        $attributes['birthdate'] = $token->getClaim('birthdate');

        if (filled($attributes['birthdate']))
            $attributes['birthdate'] = Carbon::createFromFormat('Y-M-D', $attributes['birthdate']);
    }

    /**
     * @param       $attributes
     * @param Token $token
     */
    private function roles(array &$attributes, Token $token)
    {
        $attributes['roles'] = [];

        $roles = explode(' ', $token->getClaim('roles'));

        foreach ($roles as $role) {
            if (!empty($role)) $attributes['roles'][] = $role;
        }
    }
}
