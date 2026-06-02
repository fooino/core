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

    /**
     * Convert scientific number to numeric string
     */
    public function convertScientificNumber(string|int|float|null $number): string;
}
