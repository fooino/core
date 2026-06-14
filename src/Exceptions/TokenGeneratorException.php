<?php

namespace Fooino\Core\Exceptions;

class TokenGeneratorException extends FooinoException
{
    protected $message = 'msg.tokenGeneratorException';

    protected $code = 10400;

    protected string $level = 'error';
}
