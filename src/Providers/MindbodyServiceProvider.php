<?php

namespace Nlocascio\Mindbody\Providers;

use Mindbody;
use Illuminate\Support\ServiceProvider;

class MindbodyServiceProvider extends ServiceProvider {

    protected $defer = false;

    /**
     * Register and merge config.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/mindbody-laravel.php', 'mindbody-laravel'
        );
    }

    /**
     * Bind service to 'mindbody' for user with Facade
     */
    public function boot()
    {
        $this->app->bind('mindbody', 'Nlocascio\Mindbody\Services\MindbodyService');
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

}