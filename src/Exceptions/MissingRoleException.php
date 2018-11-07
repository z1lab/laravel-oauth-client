<?php
/**
 * Created by PhpStorm.
 * User: Felipe
 * Date: 19/10/2018
 * Time: 10:28
 */

namespace Z1lab\OpenID\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;

class MissingRoleException extends AuthorizationException
{
    /**
     * The roles that the user did not have.
     *
     * @var array
     */
    protected $roles;

    /**
     * Create a new missing scope exception.
     *
     * @param  array|string $roles
     * @param  string       $message
     * @return void
     */
    public function __construct($roles = [], $message = 'Invalid role(s) provided.')
    {
        parent::__construct($message);

        $this->roles = Arr::wrap($roles);
    }

    /**
     * Get the roles that the user did not have.
     *
     * @return array
     */
    public function roles()
    {
        return $this->roles;
    }
}
