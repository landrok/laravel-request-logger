<?php

namespace Landrok\Laravel\RequestLogger;

use Landrok\Laravel\RequestLogger;
use Illuminate\Support\ServiceProvider;

class RequestLoggerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__, 2) . '/config/requestlogger.php' => config_path('requestlogger.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2) . '/config/requestlogger.php', 'requestlogger'
        );

        $router = $this->app['router'];

        foreach (config('requestlogger.groups') as $group) {
            $router->pushMiddlewareToGroup(
                $group,
                RequestLoggerMiddleware::class
            );
        }
    }
}
