<?php

namespace Nlocascio\Mindbody\Providers;

use Nlocascio\Mindbody\Facades\Mindbody;
use Illuminate\Support\ServiceProvider;
use Nlocascio\Mindbody\Services\MindbodyAPI;
use Nlocascio\Mindbody\Services\MindbodyService;

class MindbodyServiceProvider extends ServiceProvider
{

    protected $defer = false;

    /**
     * Merge config.
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/mindbody.php', 'mindbody'
        );
    }

    /**
     * Bind service to 'mindbody' for user with Facade
     */
    public function register()
    {
        $this->app->singleton('Mindbody', function () {
            return new MindbodyService;
        });
    }

//    /**
//     * Get the services provided by the provider.
//     *
//     * @return array
//     */
//    public function provides()
//    {
//        return ['Mindbody'];
//    }

}