<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 29/10/2018
 * Time: 16:32
 */

namespace Z1lab\OpenID\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Z1lab\OpenID\Services\ApiService;
use Illuminate\Support\Facades\Cookie;

class ApiController
{
    /**
     * @return bool|string
     */
    public function show()
    {
        $user = [];

        if (Auth::check()) $user = (new ApiService)->getUser(Cookie::get('auth_token'));

        return new JsonResponse($user);
    }
}
