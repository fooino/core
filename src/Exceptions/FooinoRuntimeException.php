<?php

namespace Fooino\Core\Exceptions;

/**
 * The code range is 1 to 249
 */
class FooinoRuntimeException extends FooinoException
{
    protected $message = 'msg.fooinoRunTimeException';

    protected $code = 1;

    protected string $level = 'warning';

    /**
     * Configure the exception for an invalid period range passed to the date-between generator
     */
    final public function _2(): static
    {
        return $this
            ->setMessage('msg.fooinoRunTimeExceptionInvalidPeriodForDatesBetween')
            ->setCode(2)
            ->warning()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    /**
     * Configure the exception when a date string does not match any recognised format after parsing
     */
    final public function _3(): static
    {
        return $this
            ->setMessage('msg.fooinoRunTimeExceptionInvalidDateString')
            ->setCode(3)
            ->error()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    /**
     * Configure the exception when code attempts to unserialize a singleton instance, which is forbidden
     */
    final public function _4(): static
    {
        return $this
            ->setMessage('msg.fooinoRunTimeExceptionCannotUnserializeSingleton')
            ->setCode(4)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    /**
     * Configure the exception when code attempts to clone a singleton instance, which is forbidden
     */
    final public function _5(): static
    {
        return $this
            ->setMessage('msg.fooinoRunTimeExceptionCannotCloneSingleton')
            ->setCode(5)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }
}
