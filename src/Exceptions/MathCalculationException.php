<?php

namespace Fooino\Core\Exceptions;

/**
 * The code range is 1100 to 1199
 */
class MathCalculationException extends FooinoException
{
    protected $message = 'msg.mathCalculationException';

    protected $code = 1100;

    protected string $level = 'error';

    final public function _1101(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionInvalidPrecision')
            ->setCode(1101)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1102(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionInvalidArgumentsCount')
            ->setCode(1102)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1103(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionInvalidArgumentType')
            ->setCode(1103)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1104(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionDivisionByZero')
            ->setCode(1104)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1105(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionInvalidValueError')
            ->setCode(1105)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1106(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionUnsupportedFunction')
            ->setCode(1106)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }
}
