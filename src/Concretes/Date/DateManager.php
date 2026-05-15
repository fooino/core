<?php

namespace Fooino\Core\Concretes\Date;

use Fooino\Core\Interfaces\Dateable;
use Illuminate\Support\Manager;

class DateManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'FooinoDateHandler';
    }

    public function createFooinoDateHandlerDriver(): Dateable
    {
        return new FooinoDateHandler();
    }
}
