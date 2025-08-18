<?php


namespace Fooino\Core\Exceptions;

use Exception;

class ResolveRequestValidationException extends Exception
{

    public int $status = 422;

    public array $errors = [];

    public function __construct(array $errors = [])
    {
        $this->code = $this->status;
        $this->errors = $errors;

        foreach ($errors as $error) {
            $this->message .= implode(', ', $error);
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
