<?php

namespace Z1lab\OpenID\Facades;

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
