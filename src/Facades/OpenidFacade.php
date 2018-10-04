<?php

namespace OpenID\Client\Facades;

use Illuminate\Support\Facades\Facade;

class OpenidFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'openid';
    }
}