<?php

namespace Fooino\Core\Concretes\Math;

use Fooino\Core\Interfaces\Mathable;

class FooinoMathHandler implements Mathable
{
    private array $instances = [];

    public function __construct(private int $precision = 10)
    {
        \bcscale(10);
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function setPrecision(int $precision): Mathable
    {
        return $this->instances[$precision] ??= new static(precision: $precision);
    }
}
