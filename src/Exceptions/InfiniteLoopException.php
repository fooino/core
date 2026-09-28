<?php

namespace Fooino\Core\Exceptions;

/**
 * The code range is 250 to 499
 */
class InfiniteLoopException extends FooinoException
{
    protected $message = 'msg.infiniteLoopException';

    protected $code = 250;

    protected string $level = 'critical';

    /**
     * Configure the exception when the date interval produces no end or would iterate indefinitely
     */
    final public function _251(): static
    {
        return $this
            ->setMessage('msg.infiniteLoopExceptionInvalidIntervalForDatesBetween')
            ->setCode(251)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    /**
     * Configure the exception when a sanitizer value is nested deeper than the maximum allowed depth
     */
    final public function _252(): static
    {
        return $this
            ->setMessage('msg.infiniteLoopExceptionSanitizerDepthLimit')
            ->setCode(252)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    /**
     * Configure the exception when the token generator exhausts its uniqueness retry limit
     */
    final public function _253(): static
    {
        return $this
            ->setMessage('msg.infiniteLoopExceptionInTokenGenerator')
            ->setCode(253)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }
}
