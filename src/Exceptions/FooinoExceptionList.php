<?php

namespace Fooino\Core\Exceptions;

trait FooinoExceptionList
{
    // FooinoException codes

    // CanNotConvertDateException (range 10050-10099)





    // MathCalculationException (range 10100-10199)












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
