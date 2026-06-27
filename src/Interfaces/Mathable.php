<?php

namespace Fooino\Core\Interfaces;

use RoundingMode;

interface Mathable
{
    /**
     * Retrieve the current precision value used for truncating output numbers
     */
    public function getPrecision(): int;

    /**
     * Set a new precision value and return a fresh instance configured
     */
    public function setPrecision(int $precision): Mathable;

    /**
     * Expand a number expressed in scientific notation (e.g. 1.5E+4) into its full numeric string representation
     */
    public function convertScientificNumber(string|int|float|array $number): string|array;

    /**
     * Remove all trailing zeros after the decimal point from a number, returning a clean numeric string
     */
    public function trimTrailingZeros(string|int|float $number): string;

    /**
     * Count how many decimal places a number has after trimming any trailing zeros
     */
    public function countDecimalPlaces(string|int|float $number): int;

    /**
     * Format one or more numbers by truncating them to the configured precision, removing trailing zeros, and returning clean numeric strings
     */
    public function number(string|int|float|array ...$number): string|array;

    /**
     * Format a number with thousands separators and apply precision truncation, returning a locale-friendly currency-style string
     */
    public function numberFormat(string|int|float $number, string $thousandsSeparator = ','): string;

    /**
     * Add a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    public function sum(string|int|float|array ...$operand): string;

    /**
     * Subtract a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    public function subtract(string|int|float|array ...$operand): string;

    /**
     * Multiply a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    public function multiply(string|int|float|array ...$operand): string;

    /**
     * Divide a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    public function divide(string|int|float|array ...$operand): string;

    /**
     * Compute the modulus (remainder) of a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    public function remainder(string|int|float|array ...$operand): string;

    /**
     * Raise an arbitrary precision number to a given exponent
     */
    public function power(string|int|float|array $number, int $exponent = 2): string|array;

    /**
     * Get the square root of an arbitrary precision number
     */
    public function sqrt(string|int|float|array $number): string|array;

    /**
     * Round a number up to the next integer (ceiling), away from zero
     */
    public function roundUp(string|int|float|array $number): string|array;

    /**
     * Round a number down to the previous integer (floor), toward zero
     */
    public function roundDown(string|int|float|array $number): string|array;

    /**
     * Round a number to a specified precision using a configurable rounding mode
     */
    public function roundClose(string|int|float|array $number, int $precision = 0, RoundingMode $mode = RoundingMode::HalfAwayFromZero): string|array;

    /**
     * Check if the first number is strictly greater than the second using arbitrary precision comparison
     */
    public function greaterThan(string|int|float $num1, string|int|float $num2): bool;

    /**
     * Check if the first number is greater than or equal to the second using arbitrary precision comparison
     */
    public function greaterThanOrEqual(string|int|float $num1, string|int|float $num2): bool;

    /**
     * Check if the first number is strictly less than the second using arbitrary precision comparison
     */
    public function lessThan(string|int|float $num1, string|int|float $num2): bool;

    /**
     * Check if the first number is less than or equal to the second using arbitrary precision comparison
     */
    public function lessThanOrEqual(string|int|float $num1, string|int|float $num2): bool;

    /**
     * Check if two numbers are exactly equal using arbitrary precision comparison
     */
    public function equal(string|int|float $num1, string|int|float $num2): bool;

    /**
     * Check if two numbers differ from each other using arbitrary precision comparison
     */
    public function notEqual(string|int|float $num1, string|int|float $num2): bool;
}
