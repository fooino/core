<?php

namespace Fooino\Core\Exceptions;

/**
 * The code range is 1000 to 1099
 */
class CanNotConvertDateException extends FooinoException
{
    protected $message = 'msg.canNotConvertDateException';

    protected $code = 1000;

    protected string $level = 'warning';

    final public function _1001(): static
    {
        return $this
            ->setMessage('msg.canNotConvertDateExceptionInvalidTimezone')
            ->setCode(1001)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _1002(): static
    {
        return $this
            ->setMessage('msg.canNotConvertDateExceptionDateIsEmpty')
            ->setCode(1002)
            ->warning()
            ->setHttpStatusCode(500)
            ->dontReport();
    }

    final public function _1003(): static
    {
        return $this
            ->setMessage('msg.canNotConvertDateExceptionInvalidDate')
            ->setCode(1003)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }
}
