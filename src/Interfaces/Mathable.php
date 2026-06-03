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
     * Trim trailing Zeros from end of number base on decimal separator
     */
    public function trimTrailingZeros(string|int|float $number, string $decimalSeparator = '.'): string;

    /**
     * Get decimal count of number
     */
    public function decimalPlaceNumber(string|int|float $number, string $decimalSeparator = '.'): int;

    /**
     * Convert number to well-formatted base on precision
     */
    public function number(string|int|float $number): string;

    /**
     * Convert number to currency format base on precision
     */
    public function numberFormat(string|int|float $number, string $decimalSeparator = '.', string $thousandsSeparator = ','): string;

    /**
     * Sum series of number or array of numbers
     */
    public function sum(mixed ...$args): string;

    /**
     * Subtract series of number or array of numbers
     */
    public function subtract(mixed ...$args): string;

    /**
     * Multiply series of number or array of numbers
     */
    public function multiply(mixed ...$args): string;

    /**
     * Divide series of number or array of numbers
     */
    public function divide(mixed ...$args): string;

    /**
     * Modulus series of number or array of numbers
     */
    public function modulus(mixed ...$args): string;

    /**
     * Raise an arbitrary precision number to 
     */
    public function power(string|int|float $number, int $exponent = 2): string;

    /**
     * Get the square root of an arbitrary precision number
     */
    public function sqrt(string|int|float $number): string;

    /**
     * Round the number up
     */
    public function roundUp(string|int|float $number): string;

    /**
     * Round the number down
     */
    public function roundDown(string|int|float $number): string;

    /**
     * Round the number to the nearest integer
     */
    public function roundClose(string|int|float $number, int $precision = 0): string;

    /**
     * compare two number
     */
    public function greaterThan(string|int|float $a, string|int|float $b): bool;

    /**
     * compare two number
     */
    public function greaterThanOrEqual(string|int|float $a, string|int|float $b): bool;

    /**
     * compare two number
     */
    public function lessThan(string|int|float $a, string|int|float $b): bool;

    /**
     * compare two number
     */
    public function lessThanOrEqual(string|int|float $a, string|int|float $b): bool;

    /**
     * compare two number
     */
    public function equal(string|int|float $a, string|int|float $b): bool;

    /**
     * compare two number
     */
    public function notEqual(string|int|float $a, string|int|float $b): bool;
}
