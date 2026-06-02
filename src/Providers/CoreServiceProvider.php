<?php

namespace Fooino\Core\Providers;

use Fooino\Core\Concretes\Json\JsonManager;
use Fooino\Core\Concretes\Date\DateManager;
use Fooino\Core\Concretes\Math\MathManager;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSingletons();
    }

    /**
     * Register singleton files
     *
     * @return self
     */
    protected function registerSingletons(): self
    {
        $this->app->singleton('fooino-json-facade', function ($app) {
            return new JsonManager($app);
        });

        $this->app->singleton('fooino-date-facade', function ($app) {
            return new DateManager($app);
        });

        $this->app->singleton('fooino-math-facade', function ($app) {
            return new MathManager($app);
        });

        return $this;
    }
}
