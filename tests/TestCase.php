<?php

namespace Fooino\Core\Tests;

use Astrotomic\Translatable\TranslatableServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Fooino\Core\Providers\CoreServiceProvider;

class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CoreServiceProvider::class,
            TranslatableServiceProvider::class
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
