<?php

namespace Fooino\Core\Concretes\Date;

use Fooino\Core\Interfaces\Dateable;
use Illuminate\Support\Manager;

class DateManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return 'FooinoDateHandler';
    }

    /**
     * Create fooino driver.
     */
    public function createFooinoDateHandlerDriver(): Dateable
    {
        return new FooinoDateHandler();
    }
}
