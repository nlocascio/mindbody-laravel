<?php

namespace Nlocascio\Mindbody\Providers;

use Illuminate\Support\ServiceProvider;
use Nlocascio\Mindbody\Services\MindbodyService;

class MindbodyServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Boot ServiceProvider
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/mindbody.php' => config_path('mindbody.php'),
        ]);
    }

    /**
     * Register ServiceProvider bindings
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/mindbody.php', 'mindbody'
        );

        $this->app->singleton(MindbodyService::class, function ($app) {
            return new MindbodyService(config('mindbody'));
        });
    }

    public function provides()
    {
        return [MindbodyService::class];
    }

}