<?php

namespace Fooino\Core\Concretes\Math;

use Fooino\Core\Interfaces\Mathable;

class FooinoMathHandler implements Mathable
{
    private const BC_SCALE = 10;
    private array $instances = [];

    public function __construct(private int $precision = 10)
    {
        bcscale(self::BC_SCALE);
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function setPrecision(int $precision): Mathable
    {
        return $this->instances[$precision] ??= new static(precision: $precision);
    }

    public function convertScientificNumber(string|int|float|null $number): string
    {
        if (is_null($number)) {
            return '0';
        }

        return (string) (preg_match(pattern: '/^-?\d*\.?\d+[Ee][+-]?\d+$/', subject: $number) ? sprintf("%." . self::BC_SCALE . "f", floatval($number)) : $number);
    }
}
