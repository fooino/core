<?php

namespace Fooino\Core\Providers;

use Fooino\Core\Commands\SyncLanguagesCommand;
use Fooino\Core\Concretes\{
    Json\JsonManager,
    Math\MathManager,
    Date\DateManager
};
use Fooino\Core\Tasks\Language\GetActiveLanguagesFromCacheTask;
use Fooino\Core\Tasks\Tools\GetFooinoModelsFromCacheTask;
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
        $this->loadCommands();
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
        $this->app->singleton('fooino-json-facade', function ($app) {
            return new JsonManager();
        });

        $this->app->singleton('fooino-math-facade', function ($app) {
            return new MathManager();
        });

        $this->app->singleton('fooino-date-facade', function ($app) {
            return new DateManager();
        });


        $singletons = [
            GetFooinoModelsFromCacheTask::class,
            GetActiveLanguagesFromCacheTask::class,
        ];

        foreach ($singletons as $singleton) {

            $this->app->singleton($singleton, function ($app) use ($singleton) {
                return new $singleton;
            });
        }

        return $this;
    }


    protected function loadCommands(): self
    {
        $this->commands([
            SyncLanguagesCommand::class,
        ]);

        return $this;
    }
}
