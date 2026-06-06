<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static int getPrecision()
 * @method static \Fooino\Core\Interfaces\Mathable setPrecision(int $precision)
 * 
 * @method static string convertScientificNumber(string|int|float $number)
 * @method static string trimTrailingZeros(string|int|float $number)
 * @method static int countDecimalPlaces(string|int|float $number)
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
