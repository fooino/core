<?php

namespace Fooino\Core\Providers;

use Fooino\Core\Concretes\Json\JsonManager;
use Fooino\Core\Concretes\Date\DateManager;
use Fooino\Core\Concretes\Math\MathManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerSingletons();
    }

    /**
     * Register singleton files
     */
    protected function registerSingletons(): self
    {
        $this->app->singleton(abstract: 'fooino-json-facade', concrete: fn(Application $app) => new JsonManager($app));

        $this->app->singleton(abstract: 'fooino-date-facade', concrete: fn(Application $app) => new DateManager($app));

        $this->app->singleton(abstract: 'fooino-math-facade', concrete: fn(Application $app) => new MathManager($app));

        return $this;
    }
}
