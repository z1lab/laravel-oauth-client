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

class MissingScopeException extends AuthorizationException
{
    /**
     * The roles that the user did not have.
     *
     * @var array
     */
    protected $scopes;

    /**
     * Create a new missing scope exception.
     *
     * @param  array|string $scopes
     * @param  string       $message
     * @return void
     */
    public function __construct($scopes = [], $message = 'Invalid scope(s) provided.')
    {
        parent::__construct($message);

        $this->scopes = Arr::wrap($scopes);
    }

    /**
     * Get the scopes that the user did not have.
     *
     * @return array
     */
    public function scopes()
    {
        return $this->scopes;
    }
}
