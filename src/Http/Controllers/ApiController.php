<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 29/10/2018
 * Time: 16:32
 */

namespace Z1lab\OpenID\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Z1lab\OpenID\Http\Resources\User;
use Illuminate\Http\JsonResponse;

class ApiController
{
    /**
     * @return bool|string
     */
    public function show()
    {
        if(Auth::check()) return new User(collect(Auth::user()->toArray()));

        return new JsonResponse();
    }
}
