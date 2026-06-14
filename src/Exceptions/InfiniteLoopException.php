<?php

namespace Fooino\Core\Exceptions;

class InfiniteLoopException extends FooinoException
{
    protected $message = 'msg.infiniteLoopException';

    // Range code 10200 - 10300
    protected $code = 10200;

    protected string $level = 'critical';
}
