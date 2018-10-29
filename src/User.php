<?php

namespace OpenID\Client;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use OpenID\Client\Traits\MagicMethodsTrait;
use OpenID\Client\Traits\UserTrait;

class User implements UserContract, MustVerifyEmail
{
    use UserTrait, MagicMethodsTrait;

    /**
     * User constructor.
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
        return false;
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
        return $this->attributes;
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->attributes);
    }
}
