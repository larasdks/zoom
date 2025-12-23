<?php

namespace laraSDKs\Zoom\Providers;

use Illuminate\Support\ServiceProvider;
use laraSDKs\Zoom\Client;

/**
 * Registers the Zoom API client in the Laravel service container.
 */
class ZoomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/zoom.php', 'connectors-studio.zoom');

        $this->app->singleton(Client::class, function ($app) {
            $config = $app['config']['connectors-studio.zoom'];

            return new Client($config);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/zoom.php' => config_path('zoom.php'),
        ], 'config');
    }
}
