<?php

namespace Fooino\Core\Exceptions;

class MathCalculationException extends FooinoException
{
    protected $message = 'msg.mathCalculationException';

    protected $code = 10100;

    protected string $level = 'error';
}
