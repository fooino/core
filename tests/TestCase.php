<?php

namespace Fooino\Core\Tests;

use Fooino\Core\Providers\CoreServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CoreServiceProvider::class,
        ];
    }
}
