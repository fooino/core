<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static int getPrecision()
 * @method static \Fooino\Core\Interfaces\Mathable setPrecision(int $precision)
 * 
 * @method static string|array convertScientificNumber(string|int|float|array $number)
 * @method static string trimTrailingZeros(string|int|float $number)
 * @method static int countDecimalPlaces(string|int|float $number)
 * 
 * @method static string|array number(string|int|float|array ...$number) 
 * @method static string numberFormat(string|int|float $number, string $thousandsSeparator = ',')
 * 
 * @method static string sum(string|int|float|array ...$operand)
 * @method static string subtract(string|int|float|array ...$operand)
 * 
 * @method static string multiply(string|int|float|array ...$operand)
 * @method static string divide(string|int|float|array ...$operand)
 * 
 * @method static string remainder(string|int|float|array ...$operand)
 * 
 * @method static string|array power(string|int|float|array $number, int $exponent = 2)
 * @method static string|array sqrt(string|int|float|array $number)
 * 
 * @method static string|array roundUp(string|int|float|array $number)
 * @method static string|array roundDown(string|int|float|array $number)
 * @method static string|array roundClose(string|int|float|array $number , int $precision = 0, \RoundingMode $mode = \RoundingMode::HalfAwayFromZero)
 * 
 * @method static bool greaterThan(string|int|float $a, string|int|float $b)
 * @method static bool greaterThanOrEqual(string|int|float $a, string|int|float $b)
 * 
 * @method static bool lessThan(string|int|float $a, string|int|float $b)
 * @method static bool lessThanOrEqual(string|int|float $a, string|int|float $b)
 * 
 * @method static bool equal(string|int|float $a, string|int|float $b)
 * @method static bool notEqual(string|int|float $a, string|int|float $b)
 * 
 * @see \Fooino\Core\Interfaces\Mathable
 * @see \Fooino\Core\Concretes\Math\MathManager
 * @see \Fooino\Core\Concretes\Math\FooinoMathHandler
 */
class Math extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'fooino-math-facade';
    }
}
