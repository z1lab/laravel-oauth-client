<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 29/10/2018
 * Time: 16:32
 */

namespace OpenID\Client\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ApiController
{
    /**
     * @return bool|string
     */
    public function show()
    {
        if (Auth::check()) return Auth::user()->toJson();
        
        return json_encode([]);
    }
}
