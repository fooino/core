<?php


namespace Fooino\Core\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class CheckModelForChangePriorityRule implements ValidationRule
{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            blank($value) ||
            !class_exists($value) ||
            !method_exists($value, 'objectUsedPrioritiable') ||
            !app($value)->objectUsedPrioritiable()
        ) {
            $fail(__(key: 'msg.modelIsInvalid'));
            return;
        }
    }
}
