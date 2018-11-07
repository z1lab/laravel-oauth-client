<?php
/**
 * Created by Olimar Ferraz
 * webmaster@z1lab.com.br
 * Date: 30/10/2018
 * Time: 15:55
 */

namespace Z1lab\OpenID\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;
use Z1lab\LaravelMeta\Facade as Meta;

class LoginController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function index()
    {
        if (Auth::check()) return redirect(Cookie::get('url_intended', route('home')));

        Meta::set('title', __('Login form'));

        return View('auth.login');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function register()
    {
        if (Auth::check()) return redirect(Cookie::get('url_intended', route('home')));

        Meta::set('title', __('Register form'));

        return View('auth.register');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function accountRecovery()
    {
        if (Auth::check()) return redirect(Cookie::get('url_intended', route('home')));

        Meta::set('title', __('Account recovery form'));

        return View('auth.account-recovery');
    }


}
