<?php

namespace Fooino\Core\Exceptions;

/**
 * The code range is 1 to 999
 */
class FooinoRuntimeException extends FooinoException
{
    protected $message = 'msg.fooinoRunTimeException';

    protected $code = 1;

    protected string $level = 'warning';
}
