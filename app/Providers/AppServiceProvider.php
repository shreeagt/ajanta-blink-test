<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Prevent PHP 8.4 deprecation logs from crashing Laravel Logger on live
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
