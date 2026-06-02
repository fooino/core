<?php

namespace Fooino\Core\Concretes\Math;

use Fooino\Core\Interfaces\Mathable;
use Illuminate\Support\Manager;

class MathManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return 'FooinoMathHandler';
    }

    /**
     * Create fooino driver.
     */
    public function createFooinoMathHandlerDriver(): Mathable
    {
        return new FooinoMathHandler();
    }
}
