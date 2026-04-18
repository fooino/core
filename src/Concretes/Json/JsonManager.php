<?php

namespace Fooino\Core\Concretes\Json;

use Fooino\Core\Interfaces\Jsonable;
use Illuminate\Support\Manager;

class JsonManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return 'FooinoJsonHandler';
    }

    public function createFooinoJsonHandlerDriver(): Jsonable
    {
        return new FooinoJsonHandler();
    }
}
