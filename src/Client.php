<?php

namespace OpenID\Client;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class Client extends Api
{
    /**
     * @var string
     */
    public static $access_cookie = 'auth_token';

    /**
     * @var string
     */
    public static $openid_cookie = 'id_token';

    /**
     * @var string
     */
    public static $refresh_cookie = 'refresh_token';

    /**
     * @param array $scopes
     * @return string
     */
    public function askForToken(array $scopes = [])
    {
        $scopes = array_prepend($scopes, 'openid');

        $query = http_build_query([
            'client_id'     => config('openid.client.id'),
            'redirect_uri'  => URL::route('openid.callback'),
            'response_type' => 'code',
            'scope'         => implode(' ', $scopes),
        ]);
        return str_finish(config('openid.server'), '/') . "oauth/authorize?$query";
    }

    /**
     * @return void
     */
    public function routes()
    {
        Route::get('openid/callback', 'OpenID\Client\Http\Controllers\CallbackController@callback')
            ->middleware(EncryptCookies::class)->name('openid.callback');

        Route::post('openid/refresh', 'OpenID\Client\Http\Controllers\RefreshController@refreshToken')
            ->middleware(EncryptCookies::class)->name('openid.refresh');

        Route::get('openid/user', 'OpenID\Client\Http\Controllers\ApiController@show')
            ->middleware(EncryptCookies::class)->name('openid.user');

        Route::get('password-reset', 'OpenID\Client\Http\Controllers\ResetPasswordController@show')
            ->middleware(EncryptCookies::class)->name('reset-password');

        Route::post('logout', 'OpenID\Client\Http\Controllers\LogoutController@logout')
            ->middleware(EncryptCookies::class)->name('logout');

        Route::get('login', 'OpenID\Client\Http\Controllers\LoginController@index')
            ->middleware(EncryptCookies::class)->name('login');

        Route::get('register', 'OpenID\Client\Http\Controllers\LoginController@register')
            ->middleware(EncryptCookies::class)->name('register');

        Route::get('account-recovery', 'OpenID\Client\Http\Controllers\LoginController@accountRecovery')
            ->middleware(EncryptCookies::class)->name('account-recovery');
    }
}
