<?php

namespace Z1lab\OpenID\Models;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Z1lab\OpenID\Traits\MagicMethodsTrait;
use Z1lab\OpenID\Traits\UserTrait;

class User implements UserContract, MustVerifyEmail
{
    use UserTrait, MagicMethodsTrait;

    /**
     * User constructor.
     *
     * @param $attributes
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return $this->attributes['email_verified'];
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return FALSE;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        return;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = $this->attributes;

        $data['expires_at'] = $this->expires_at->format('Y-m-d H:i:s');
        $data['auth_time'] = $this->auth_time->format('Y-m-d H:i:s');
        $data['updated_at'] = $this->updated_at->format('Y-m-d H:i:s');

        $data['address'] = NULL !== $this->address ? $this->address->toArray() : NULL;
        $data['phone'] = NULL !== $this->phone ? $this->phone->toArray() : NULL;

        return $data;
    }
}
