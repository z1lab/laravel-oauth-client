<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 29/10/2018
 * Time: 22:59
 */

namespace OpenID\Client;


class Api
{
    /**
     * @var string 
     */
    protected $server;

    /**
     * Api constructor.
     */
    public function __construct() 
    {
        $this->server = str_finish(config('openid.server'), '/') . 'api/actions/';
    }

    /**
     * @return string
     */
    public function loginRoute()
    {
        return $this->server . 'login';
    }

    /**
     * @return string
     */
    public function logoutRoute()
    {
        return $this->server . 'logout';
    }

    /**
     * @return string
     */
    public function registerRoute()
    {
        return $this->server . 'register';
    }

    /**
     * @return string
     */
    public function recoveryRoute()
    {
        return $this->server . 'recovery';
    }

    /**
     * @return string
     */
    public function resetRoute()
    {
        return $this->server . 'recovery/reset';
    }
}
