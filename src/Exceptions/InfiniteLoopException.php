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

    final public function _251(): static
    {
        return $this
            ->setMessage('msg.infiniteLoopExceptionInvalidIntervalForDatesBetween')
            ->setCode(251)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

    final public function _252(): static
    {
        return $this
            ->setMessage('msg.infiniteLoopExceptionSanitizerRecursionLimit')
            ->setCode(252)
            ->critical()
            ->setHttpStatusCode(500)
            ->shouldReport();
    }

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
