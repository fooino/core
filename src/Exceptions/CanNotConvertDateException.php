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

    /**
     * Configure the exception for an invalid timezone that cannot be resolved for date conversion
     */
    final public function _1001(): static
    {
        return $this
            ->setMessage('msg.canNotConvertDateExceptionInvalidTimezone')
            ->setCode(1001)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    /**
     * Configure the exception when an empty date string is provided but a valid date is required
     */
    final public function _1002(): static
    {
        return $this
            ->setMessage('msg.canNotConvertDateExceptionDateIsEmpty')
            ->setCode(1002)
            ->warning()
            ->setHttpStatusCode(500)
            ->dontReport();
    }

    /**
     * Configure the exception when the date string cannot be parsed into a valid date value
     */
    final public function _1003(): static
    {
        return $this
            ->setMessage('msg.canNotConvertDateExceptionInvalidDate')
            ->setCode(1003)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    /**
     * Configure the exception when the system default timezone is not UTC, which would produce incorrect conversions
     */
    final public function _1004(): static
    {
        return $this
            ->setMessage('msg.canNotConvertDateExceptionInvalidDefaultTimezone')
            ->setCode(1004)
            ->emergency()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }
}
