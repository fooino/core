<?php

namespace Fooino\Core\Interfaces;

interface Mathable
{
    /**
     * Get the precision
     */
    public function getPrecision(): int;

    /**
     * Set the precision
     */
    public function setPrecision(int $precision): Mathable;
}
