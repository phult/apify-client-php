<?php

namespace Megaads\ApifyClient;

use Illuminate\Support\ServiceProvider;

class ApifyClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Megaads\ApifyClient\Client');
    }
}
