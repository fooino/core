<?php


namespace Fooino\Core\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class CheckModelForMoveToTrashRule implements ValidationRule
{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            blank($value) ||
            !class_exists($value) ||
            !method_exists($value, 'objectUsedTrashable') ||
            !app($value)->objectUsedTrashable()
        ) {
            $fail(__(key: 'msg.modelIsInvalid'));
            return;
        }
    }
}
