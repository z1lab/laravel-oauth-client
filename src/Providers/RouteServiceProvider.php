<?php

namespace Z1lab\OpenID\Providers;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The root namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = 'Z1lab\OpenID\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapOpenIDRoutes();
        $this->mapClientRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapOpenIDRoutes()
    {
        Route::prefix('openid')
            ->as('openid.')
            ->middleware(EncryptCookies::class)
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/openid.php');
    }

    protected function mapClientRoutes()
    {
        Route::middleware(EncryptCookies::class)
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
