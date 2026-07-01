<?php

namespace Fooino\Core\Exceptions;

/**
 * The code range is 1200 to 1299
 */
class TokenGeneratorException extends FooinoException
{
    protected $message = 'msg.tokenGeneratorException';

    protected $code = 1200;

    protected string $level = 'error';

    final public function _1201(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionLengthMustBePositive')
            ->setCode(1201)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1202(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionBigLengthNumber')
            ->setCode(1202)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1203(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionSmallLengthNumberForStrongPassword')
            ->setCode(1203)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1204(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionSmallLengthNumberForPassword')
            ->setCode(1204)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1205(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionFieldIsRequired')
            ->setCode(1205)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1206(): static
    {
        return $this
            ->setMessage('msg.tokenGeneratorExceptionSmallLengthNumberForMemorable')
            ->setCode(1206)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }
}
