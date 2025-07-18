<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Guards\ApiGuard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom API guard
        Auth::extend('custom_api', function ($app, $name, array $config) {
            return new ApiGuard(
                Auth::createUserProvider($config['provider']),
                $app['request']
            );
        });
    }
}
