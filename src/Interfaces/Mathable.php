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
    public function convertScientificNumber(string|int|float $number): string;

    /**
     * Trim trailing Zeros from end of number
     */
    public function trimTrailingZeros(string|int|float $number): string;

    /**
     * Get decimal count of number
     */
    public function countDecimalPlaces(string|int|float $number): int;

    /**
     * Convert numbers to well-formatted in truncated base on precision
     */
    public function number(mixed ...$number): string|array;

    /**
     * Convert number to currency format base on precision
     */
    public function numberFormat(string|int|float $number, string $thousandsSeparator = ','): string;
}
