<?php

namespace Z1lab\OpenID\Traits;

use Carbon\Carbon;
use Lcobucci\JWT\Token;
use Z1lab\OpenID\Models\Address;
use Z1lab\OpenID\Models\Phone;
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

        $this->strings($attributes, $token);
        $this->timestamps($attributes, $token);
        $this->booleans($attributes, $token);
        $this->address($attributes, $token);
        $this->phone($attributes, $token);
        $this->roles($attributes, $token);

        /*$attributes['id'] = $token->getClaim('sub');
        $attributes['name'] = $token->getClaim('name');
        $attributes['social_name'] = $token->getClaim('social_name');
        $attributes['nickname'] = $token->getClaim('nickname');
        $attributes['username'] = $token->getClaim('preferred_username');
        $attributes['avatar'] = $token->getClaim('picture');
        $attributes['email'] = $token->getClaim('email');
        $attributes['gender'] = $token->getClaim('gender');

        $attributes['email_verified'] = $token->getClaim('email_verified');
        if ($attributes['email_verified'] === 'false') {
            $attributes['email_verified'] = FALSE;
        } else {
            $attributes['email_verified'] = (bool)$attributes['email_verified'];
        }
        $attributes['phone_verified'] = $token->getClaim('phone_number_verified');

        $attributes['expires_at'] = Carbon::createFromTimestamp($token->getClaim('exp'));
        $attributes['auth_time'] = Carbon::createFromTimestamp($token->getClaim('auth_time'));
        $attributes['updated_at'] = Carbon::createFromTimestamp($token->getClaim('updated_at'));
        $attributes['birthdate'] = $token->getClaim('birthdate');
        if (filled($attributes['birthdate'])) $attributes['birthdate'] = Carbon::createFromFormat('Y-M-D', $attributes['birthdate']);


        $attributes['phone'] = $token->getClaim('phone_number') ? new Phone($token->getClaim('phone_number')) : NULL;

        $attributes['address'] = $token->getClaim('address');
        if (filled($attributes['address'])) $attributes['address'] = new Address(json_decode($attributes['address'], TRUE));


        $attributes['roles'] = [];

        $roles = explode(' ', $token->getClaim('roles'));

        foreach ($roles as $role) {
            if (!empty($role)) $attributes['roles'][] = $role;
        }*/

        return new User($attributes);
    }

    /**
     * @param array $attributes
     * @param Token $token
     */
    private function strings(array &$attributes, Token $token)
    {
        $map = [
            'id'          => 'sub',
            'name'        => 'name',
            'social_name' => 'social_name',
            'nickname'    => 'nickname',
            'username'    => 'preferred_username',
            'avatar'      => 'picture',
            'email'       => 'email',
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
     * @param array $attributes
     * @param Token $token
     */
    private function booleans(array &$attributes, Token $token)
    {
        $map = [
            'email_verified' => 'email_verified',
            'phone_verified' => 'phone_number_verified',
        ];

        foreach ($map as $key => $value) {
            $attributes[$key] = $token->getClaim($value) === 'false'
                ? FALSE
                : TRUE;
        }
    }

    /**
     * @param       $attributes
     * @param Token $token
     */
    private function address(array &$attributes, Token $token)
    {
        $attributes['address'] = new Address(json_decode($token->getClaim('address'), TRUE));
    }

    /**
     * @param       $attributes
     * @param Token $token
     */
    private function phone(array &$attributes, Token $token)
    {
        $attributes['phone'] = $token->getClaim('phone_number')
            ? new Phone($token->getClaim('phone_number'))
            : NULL;
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
