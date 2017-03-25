<?php

namespace Nlocascio\Mindbody;

use Nlocascio\Mindbody\Services\Mindbody;
use Illuminate\Support\ServiceProvider;

class MindbodyServiceProvider extends ServiceProvider {

    protected $defer = true;

    /**
     * Boot ServiceProvider
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '../../config/mindbody.php' => config_path('mindbody.php'),
        ]);
    }

    /**
     * Register ServiceProvider bindings
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '../../config/mindbody.php', 'mindbody'
        );

        $this->app->singleton(Mindbody::class, function () {
            $connection = config('mindbody.default');
            $settings = config('mindbody.connections');

            return new Manager($connection, $settings);
        });
    }

    public function provides()
    {
        return [Mindbody::class];
    }

}