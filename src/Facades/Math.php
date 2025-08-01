<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static Math instance(...$args)
 * 
 * @method static string trimTrailingZeroes(string|int|float|null $number, string $decimalSeparator = '.')
 * @method static string convertScientificNumber(string|int|float|null $number)
 * @method static string number(string|int|float|null $number)
 * @method static string numberFormat(string|int|float|null $number, string $decimalSeparator = '.', string $thousandsSeparator = ',', int|float $divisor = 1)
 * 
 * @method static string add(string|int|float|null $a, string|int|float|null $b)
 * @method static string subtract(string|int|float|null $a, string|int|float|null $b)
 * @method static string multiply(string|int|float|null $a, string|int|float|null $b)
 * @method static string divide(string|int|float|null $a, string|int|float|null $b)
 * @method static string modulus(string|int|float|null $a, string|int|float|null $b)
 * @method static string power(string|int|float|null $number, string|int|float|null $exponent = 2)
 * @method static string sqrt(string|int|float|null $number)
 * 
 * @method static string roundUp(string|int|float|null $number)
 * @method static string roundDown(string|int|float|null $number)
 * @method static string roundClose(string|int|float|null $number)
 * 
 * @method static bool greaterThan(string|int|float|null $a, string|int|float|null $b)
 * @method static bool greaterThanOrEqual(string|int|float|null $a, string|int|float|null $b)
 * @method static bool lessThan(string|int|float|null $a, string|int|float|null $b)
 * @method static bool lessThanOrEqual(string|int|float|null $a, string|int|float|null $b)
 * @method static bool equal(string|int|float|null $a, string|int|float|null $b)
 * @method static bool notEqual(string|int|float|null $a, string|int|float|null $b)
 * 
 * @method static int decimalPlaceNumber(string|int|float|null $number, string $decimalSeparator = '.')
 * @method static int getPrecision()
 * @method static bool getTrimTrailingZeroes()
 *
 * @see \Fooino\Core\Concretes\Math\MathManager;
 */
class Math extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fooino-math-facade';
    }
}
