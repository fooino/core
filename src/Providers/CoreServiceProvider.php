<?php

namespace Fooino\Core\Providers;

use Fooino\Core\Concretes\Json\JsonManager;
use Fooino\Core\Concretes\Date\DateManager;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this
            ->publishAssets()
            ->loadMigrations();
    }

    public function register()
    {
        $this
            ->registerSingletons()
            ->registerCommands();
    }

    protected function publishAssets(): self
    {
        $this->publishes(
            [
                __DIR__ . "/../../assets/" => public_path('vendor/fooino/core')
            ],
            'fooino-core-assets'
        );

        return $this;
    }

    protected function loadMigrations(): self
    {
        $this->loadMigrationsFrom(__DIR__ . "/../../database/migrations");
        return $this;
    }

    protected function registerSingletons(): self
    {
        $this->app->singleton('fooino-json-facade', function ($app) {
            return new JsonManager($app);
        });

        $this->app->singleton('fooino-date-facade', function ($app) {
            return new DateManager($app);
        });

        return $this;
    }

    protected function registerCommands(): self
    {
        // $this->commands([
        //     YourCommandClass::class,
        // ]);

        return $this;
    }
}
