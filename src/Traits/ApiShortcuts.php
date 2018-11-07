<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 29/10/2018
 * Time: 22:59
 */

namespace Z1lab\OpenID;

trait ApiShortcuts
{
    /**
     * @return string
     */
    public function loginRoute()
    {
        return $this->apiActions() . 'login';
    }

    /**
     * @return string
     */
    public function logoutRoute()
    {
        return $this->apiActions() . 'logout';
    }

    /**
     * @return string
     */
    public function registerRoute()
    {
        return $this->apiActions() . 'register';
    }

    /**
     * @return string
     */
    public function recoveryRoute()
    {
        return $this->apiActions() . 'recovery';
    }

    /**
     * @return string
     */
    public function resetRoute()
    {
        return $this->apiActions() . 'recovery/reset';
    }

    /**
     * Api constructor.
     */
    private function apiActions()
    {
        return str_finish(config('openid.server'), '/') . 'api/actions/';
    }
}
