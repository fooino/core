<?php

namespace Fooino\Core\Exceptions;

trait FooinoExceptionList
{
    // FooinoException codes

    // CanNotConvertDateException (range 10050-10099)





    // MathCalculationException (range 10100-10199)
    public function _10101(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionInvalidPrecision')
            ->setCode(10101)
            ->critical()
            ->shouldReport();
    }

    public function _10102(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionInvalidArgumentsCount')
            ->setCode(10102)
            ->error()
            ->shouldReport();
    }

    public function _10103(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionInvalidArgumentType')
            ->setCode(10103)
            ->error()
            ->shouldReport();
    }

    public function _10104(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionDivisionByZero')
            ->setCode(10104)
            ->critical()
            ->shouldReport();
    }

    public function _10105(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionInvalidValueError')
            ->setCode(10105)
            ->critical()
            ->shouldReport();
    }

    public function _10106(): static
    {
        return $this
            ->setMessage('msg.mathCalculationExceptionUnsupportedFunction')
            ->setCode(10106)
            ->shouldReport();
    }

    // InfiniteLoopException (range 10200-10300)
    public function _10201(): static
    {
        return $this
            ->setMessage('msg.infiniteLoopException')
            ->setCode(10201)
            ->critical()
            ->shouldReport();
    }

    public function _10202(): static
    {
        return $this
            ->setMessage('msg.infiniteLoopExceptionInTokenGenerator')
            ->setCode(10202)
            ->critical()
            ->shouldReport();
    }

    // TokenGeneratorException (range 10400-10499)
    public function _10401(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionLengthMustBePositive')
            ->setCode(10401)
            ->error()
            ->shouldReport();
    }

    public function _10402(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionBigLengthNumber')
            ->setCode(10402)
            ->error()
            ->shouldReport();
    }

    public function _10403(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionSmallLengthNumberForStrongPassword')
            ->setCode(10403)
            ->error()
            ->shouldReport();
    }

    public function _10404(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionSmallLengthNumberForPassword')
            ->setCode(10404)
            ->error()
            ->shouldReport();
    }

    public function _10405(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionFieldIsRequired')
            ->setCode(10405)
            ->error()
            ->shouldReport();
    }

    public function _10406(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionSmallLengthNumberForMemorable')
            ->setCode(10406)
            ->error()
            ->shouldReport();
    }
}
