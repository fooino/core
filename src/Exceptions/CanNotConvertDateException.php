<?php

namespace Fooino\Core\Exceptions;

class CanNotConvertDateException extends FooinoException
{
    protected $message = 'msg.canNotConvertDateException';

    protected $code = 10050;

    protected string $level = 'warning';
}
