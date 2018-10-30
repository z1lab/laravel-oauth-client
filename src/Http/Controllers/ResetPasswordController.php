<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 29/10/2018
 * Time: 18:52
 */

namespace OpenID\Client\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\View\View;

class ResetPasswordController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function show(Request $request)
    {
        return View('auth.password.reset')
            ->with('email', $request->email)
            ->with('token', $request->token);
    }
}
