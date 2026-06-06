<?php

namespace Fooino\Core\Concretes\Math;

use Fooino\Core\Interfaces\Mathable;
use Fooino\Core\Exceptions\MathCalculationException;

class FooinoMathHandler implements Mathable
{
    private const int BC_SCALE = 12;

    private array $instances = [];

    public function __construct(private int $precision = 12)
    {
        /**
         *  Difference Between BC_SCALE and $precision
         * 
         *  All bc functions must use BC_SCALE for calculations
         *  The $precision is just for returning truncated number(not rounded) and It will not used in calculations
         * 
         *  Truncate number is good for each country policy for example 1000.01 is not valid in Iran since 0.01 is worthless but in other countries like America it means cent.
         *  So we use $precision = 0 For Iran and $precision = 2 for America
         * 
         *  Example: Math::setPrecision(precision: 0)->number(Math::sum(5.599, 5.499)));
         * 
         *  Base on BC_SCALE the result is 11.098. if we assumed BC_SCALE = $precision = 0 the result was 10 which is wrong
         *  To output number we use $precision = 0 and the result is 11
         * 
         *  All calculations use BC_SCALE which is a high number to not loss precision
         */

        bcscale(scale: self::BC_SCALE);

        if (
            $this->getPrecision() > bcscale() ||
            $this->getPrecision() < 0
        ) {
            $this->throwInvalidPrecisionException();
        }
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function setPrecision(int $precision): Mathable
    {
        /**  
         * Facade in laravel use singleton design pattern and we need new fresh instance by $precision
         * Do not chain setPrecision with setPrecision like Math::setPrecision(2)->setPrecision(3)
         * It can increase memory usage
         */
        return $this->instances[$precision] ??= new static(precision: $precision);
    }

    private function throwInvalidPrecisionException(): never
    {
        app(MathCalculationException::class)
            ->setMessage('msg.mathCalculationExceptionInvalidPrecision')
            ->setCode(10101)
            ->critical()
            ->with([
                'precision' => $this->getPrecision(),
                'bc_scale'  => bcscale()
            ])
            ->throw();
    }
}
