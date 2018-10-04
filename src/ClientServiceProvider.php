<?php

namespace OpenID\Client;

use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use OpenID\Client\Guards\CookieGuard;
use OpenID\Client\Guards\TokenGuard;

class ClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->deleteCookieOnLogout();

        $this->app->singleton('openid', function ($app) {
            return new Client();
        });
    }

    public function register()
    {
        $this->registerGuards();

        $this->registerConfig();
    }

    /**
     * Register the cookie deletion event handler.
     *
     * @return void
     */
    protected function deleteCookieOnLogout()
    {
        Event::listen(Logout::class, function () {
            if (Request::hasCookie(Client::$access_cookie)) {
                Cookie::queue(Cookie::forget(Client::$access_cookie));
            }

            if (Request::hasCookie(Client::$openid_cookie)) {
                Cookie::queue(Cookie::forget(Client::$openid_cookie));
            }
        });
    }

    /**
     * Register the token guard.
     *
     * @return void
     */
    protected function registerGuards()
    {
        Auth::extend('passport', function ($app, $name, array $config) {
            return tap($this->makeGuard($config), function ($guard) {
                $this->app->refresh('request', $guard, 'setRequest');
            });
        });

        Auth::extend('cookie', function ($app, $name, array $config) {
            return new CookieGuard();
        });
    }

    /**
     * Make an instance of the token guard.
     *
     * @param  array  $config
     * @return \Illuminate\Auth\RequestGuard
     */
    protected function makeGuard(array $config)
    {
        return new RequestGuard(function ($request) use ($config) {
            return (new TokenGuard())->user($request);
        }, $this->app['request']);
    }

    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/openid.php' => config_path('openid.php')
        ], 'openid-config');
    }
}