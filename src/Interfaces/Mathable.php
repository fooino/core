<?php

namespace Fooino\Core\Interfaces;

use RoundingMode;

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

    /**
     * Sum series of number or array of numbers
     */
    public function sum(mixed ...$operand): string;

    /**
     * Subtract series of number or array of numbers
     */
    public function subtract(mixed ...$operand): string;

    /**
     * Multiply series of number or array of numbers
     */
    public function multiply(mixed ...$operand): string;

    /**
     * Divide series of number or array of numbers
     */
    public function divide(mixed ...$operand): string;

    /**
     * Modulus series of number or array of numbers
     */
    public function modulus(mixed ...$operand): string;

    /**
     * Raise an arbitrary precision number to 
     */
    public function power(string|int|float|array $number, int $exponent = 2): string|array;

    /**
     * Get the square root of an arbitrary precision number
     */
    public function sqrt(string|int|float|array $number): string|array;

    /**
     * Round the number up
     */
    public function roundUp(string|int|float|array $number): string|array;

    /**
     * Round the number down
     */
    public function roundDown(string|int|float|array $number): string|array;

    /**
     * Round the number to the nearest integer
     */
    public function roundClose(string|int|float|array $number, int $precision = 0, RoundingMode $mode = RoundingMode::HalfAwayFromZero): string|array;
}
