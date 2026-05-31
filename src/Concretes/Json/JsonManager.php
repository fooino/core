<?php

namespace Fooino\Core\Concretes\Json;

use Fooino\Core\Interfaces\Jsonable;
use Illuminate\Support\Manager;

class JsonManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return 'FooinoJsonHandler';
    }

    /**
     * Create fooino driver.
     */
    public function createFooinoJsonHandlerDriver(): Jsonable
    {
        return new FooinoJsonHandler();
    }
}
