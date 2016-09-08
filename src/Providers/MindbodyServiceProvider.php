<?php

namespace Nlocascio\Mindbody\Providers;

use Illuminate\Support\ServiceProvider;
use Nlocascio\Mindbody\Services\MindbodyService;

class MindbodyServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Merge config.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/mindbody.php' => config_path('mindbody.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/mindbody.php', 'mindbody'
        );
    }

    /**
     * Bind service to 'mindbody' for use with Facade
     */
    public function register()
    {
        $this->app->singleton('mindbody', function () {
            return new MindbodyService;
        });
    }

}