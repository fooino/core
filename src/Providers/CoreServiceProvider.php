<?php

namespace Fooino\Core\Providers;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this
            ->registerPublishes()
            ->registerResources();
    }

    public function register()
    {
        $this
            // ->loadProviders()
            ->registerSingletons()
            // ->registerBinds()
            ->loadCommands();
    }


    protected function registerPublishes(): self
    {
        $this
            ->publishConfigs()
            ->publishMigrations()
            ->publishAssets()
            ->publishAll();

        return $this;
    }

    protected function publishConfigs(): self
    {
        // $this->publishes(
        //     [
        //         __DIR__ . "/../../config/fooino-core.php"                     => config_path('fooino-core.php'),
        //     ],
        //     'fooino-core-config'
        // );

        return $this;
    }

    protected function publishMigrations(): self
    {
        // $this->publishes(
        //     [
        //         __DIR__ . "/../../database/migrations/2000_00_00_000000_create_foobar_table.php"                             => database_path('migrations/2000_00_00_000000_create_foobar_table.php'),
        //     ],
        //     'fooino-core-migrations'
        // );

        return $this;
    }

    protected function publishAssets(): self
    {
        // $this->publishes(
        //     [
        //         __DIR__ . "/../../assets/" => public_path('vendor/fooino/core')
        //     ],
        //     'fooino-core-assets'
        // );

        return $this;
    }

    protected function publishAll(): self
    {
        // $this->publishes(self::$publishes[coreServiceProvider::class], 'fooino-core-publish-all');

        return $this;
    }


    protected function registerResources(): self
    {
        $this
            ->registerMigrations()
            ->registerConfigs();

        return $this;
    }

    protected function registerMigrations(): self
    {
        // $this->loadMigrationsFrom(__DIR__ . "/../../database/migrations");
        return $this;
    }

    protected function registerConfigs(): self
    {
        // // for testing purposes or if the user did not publish the config file
        // foreach (['fooino-core'] as $config) {

        //     if (blank(config($config))) {
        //         $this->mergeConfigFrom(__DIR__ . "/../../config/{$config}.php", $config);
        //     }
        // }
        return $this;
    }
    
    protected function loadProviders(): self
    {
        // $this->app->register(CoreEventServiceProvider::class);
        return $this;
    }

    protected function registerSingletons(): self
    {
        // $this->app->singleton('your-abstract-class-name', function ($app) {
        //     return new YourFacadeConcreteClass();
        // });

        return $this;
    }

    protected function registerBinds(): self
    {
        // $this->app->bind('your-abstract-class-name', function ($app) {
        //     return new YourFacadeConcreteClass();
        // });

        return $this;
    }

    protected function loadCommands(): self
    {
        // $this->commands([
        //     YourCommandClass::class,
        // ]);

        return $this;
    }
}
