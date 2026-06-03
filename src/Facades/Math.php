<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static int getPrecision()
 * @method static \Fooino\Core\Interfaces\Mathable setPrecision(int $precision)
 * 
 * @method static string convertScientificNumber(string|int|float|null $number)
 * @method static string trimTrailingZeros(string|int|float|null $number, string $decimalSeparator = '.')
 * @method static int decimalPlaceNumber(string|int|float|null $number, string $decimalSeparator = '.')
 * 
 * @method static string number(string|int|float|null $number) 
 * @method static string numberFormat(string|int|float|null $number, string $decimalSeparator = '.', string $thousandsSeparator = ',')
 * 
 * @method static string sum(mixed ...$args)
 * @method static string subtract(mixed ...$args)
 * @method static string multiply(mixed ...$args)
 * @method static string divide(mixed ...$args)
 * @method static string modulus(mixed ...$args)
 * @method static string power(string|int|float|null $number, int $exponent = 2)
 * @method static string sqrt(string|int|float|null $number)
 * 
 * @method static string roundUp(string|int|float|null $number)
 * @method static string roundDown(string|int|float|null $number)
 * @method static string roundClose(string|int|float|null $number , int $precision = 0)
 * 
 * @method static bool greaterThan(string|int|float|null $a, string|int|float|null $b)
 * @method static bool greaterThanOrEqual(string|int|float|null $a, string|int|float|null $b)
 * @method static bool lessThan(string|int|float|null $a, string|int|float|null $b)
 * @method static bool lessThanOrEqual(string|int|float|null $a, string|int|float|null $b)
 * @method static bool equal(string|int|float|null $a, string|int|float|null $b)
 * @method static bool notEqual(string|int|float|null $a, string|int|float|null $b)
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
