<?php

namespace Fooino\Core\Exceptions;

use Exception;

class CanNotConvertDateException extends Exception
{
    public function __construct($message = 'Can not convert date', $code = 500)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
