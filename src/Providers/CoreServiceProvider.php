<?php

namespace Fooino\Core\Providers;

use Fooino\Core\Concretes\{
    Json\JsonResponse,
    Math\MathManager,
    Date\DateManger
};
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerPublishes();
        $this->registerResources();
    }

    public function register()
    {
        $this->registerSingletons();
    }

    protected function registerPublishes(): self
    {
        $this
            ->publishAssets();

        return $this;
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

    protected function registerResources(): self
    {
        $this
            ->registerMigrations();


        return $this;
    }

    protected function registerMigrations(): self
    {
        $this->loadMigrationsFrom(__DIR__ . "/../../database/migrations");
        return $this;
    }

    protected function registerSingletons(): self
    {
        $this->app->singleton('json-facade', function ($app) {
            return new JsonResponse();
        });

        $this->app->singleton('math-facade', function ($app) {
            return new MathManager();
        });

        $this->app->singleton('date-facade', function ($app) {
            return new DateManger();
        });

        return $this;
    }
}
