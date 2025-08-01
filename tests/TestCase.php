<?php

namespace Fooino\Core\Tests;

use Astrotomic\Translatable\TranslatableServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Fooino\Core\Providers\CoreServiceProvider;
use Spatie\Activitylog\ActivitylogServiceProvider;

class TestCase extends TestbenchTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        activity()->disableLogging();
    }

    protected function getPackageProviders($app)
    {
        return [
            CoreServiceProvider::class,
            TranslatableServiceProvider::class,
            ActivitylogServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'    => 'sqlite',
            'database'  => ':memory:'
        ]);
    }
}
