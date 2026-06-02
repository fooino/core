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

    /**
     * Trim trailing Zeros from end of number base on decimal separator
     */
    public function trimTrailingZeros(string|int|float|null $number, string $decimalSeparator = '.'): string;

    /**
     * Convert number to well-formatted base on precision
     */
    public function number(string|int|float|null $number): string;
}
