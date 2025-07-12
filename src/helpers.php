<?php

use Fooino\Core\Facades\Math;
use Fooino\Core\Tasks\Tools\ReplaceForbiddenCharactersTask;


if (
    !function_exists('math')
) {
    function math(...$args)
    {
        return Math::instance(...$args);
    }
}

if (
    !function_exists('trimTrailingZeroes')
) {
    function trimTrailingZeroes(
        $number,
        $decimalSeparator = '.'
    ): string {

        return Math::trimTrailingZeroes(
            number: $number,
            decimalSeparator: $decimalSeparator
        );
    }
}

if (
    !function_exists('numberFormat')
) {
    function numberFormat(
        $number,
        $decimalSeparator = '.',
        $thousandsSeparator = ',',
        $divisor = 1
    ): string {

        return Math::numberFormat(
            number: $number,
            decimalSeparator: $decimalSeparator,
            thousandsSeparator: $thousandsSeparator,
            divisor: $divisor
        );
    }
}

if (
    !function_exists('number')
) {
    function number($number): string
    {
        return Math::number($number);
    }
}

if (
    !function_exists('add')
) {
    function add($a, $b): string
    {
        return Math::add($a, $b);
    }
}

if (
    !function_exists('subtract')
) {
    function subtract($a, $b): string
    {
        return Math::subtract($a, $b);
    }
}

if (
    !function_exists('multiply')
) {
    function multiply($a, $b): string
    {
        return Math::multiply($a, $b);
    }
}

if (
    !function_exists('divide')
) {
    function divide($a, $b): string
    {
        return Math::divide($a, $b);
    }
}

if (
    !function_exists('modulus')
) {
    function modulus($a, $b): string
    {
        return Math::modulus($a, $b);
    }
}

if (
    !function_exists('square')
) {
    function square($number): string
    {
        return Math::sqrt($number);
    }
}

if (
    !function_exists('power')
) {
    function power($number, $exponent = 2): string
    {
        return Math::power($number, $exponent);
    }
}

if (
    !function_exists('greaterThan')
) {
    function greaterThan($a, $b): bool
    {
        return Math::greaterThan($a, $b);
    }
}

if (
    !function_exists('greaterThanOrEqual')
) {
    function greaterThanOrEqual($a, $b): bool
    {
        return Math::greaterThanOrEqual($a, $b);
    }
}

if (
    !function_exists('lessThan')
) {
    function lessThan($a, $b): bool
    {
        return Math::lessThan($a, $b);
    }
}

if (
    !function_exists('lessThanOrEqual')
) {
    function lessThanOrEqual($a, $b): bool
    {
        return Math::lessThanOrEqual($a, $b);
    }
}

if (
    !function_exists('equal')
) {
    function equal($a, $b): bool
    {
        return Math::equal($a, $b);
    }
}

if (
    !function_exists('notEqual')
) {
    function notEqual($a, $b): bool
    {
        return Math::notEqual($a, $b);
    }
}

if (!function_exists('replaceForbiddenCharacters')) {

    function replaceForbiddenCharacters(
        string|int|float|null $value,
        array $excludes = [],
        string $replacementChar = '_'
    ) {
        return filled($value) ? app(ReplaceForbiddenCharactersTask::class)->run(value: $value, excludes: $excludes, replacementChar: $replacementChar) : $value;
    }
}


if (
    !function_exists('emptyToNullOrValue')
) {
    function emptyToNullOrValue(mixed $value = null): mixed
    {
        return (is_null($value) || blank($value) || (is_string($value) && strtolower($value) == 'null')) ? null : $value;
    }
}

if (
    !function_exists('zeroToNullOrValue')
) {
    function zeroToNullOrValue(mixed $value = null): mixed
    {
        $value = emptyToNullOrValue(value: $value);
        return ((is_string($value) || is_numeric($value)) && in_array($value, [0, 0.0, '0', '0.0'])) ? null : $value;
    }
}

if (
    !function_exists('removeComma')
) {
    function removeComma($value)
    {
        return (\is_string($value) || \is_array($value)) ? \str_replace(',', '', $value) : $value;
    }
}


if (
    !function_exists('replaceSlashToDash')
) {
    function replaceSlashToDash(array|string $value): array|string
    {
        return \str_replace(
            search: '/',
            replace: '-',
            subject: $value
        );
    }
}



if (
    !function_exists('trimEmptyString')
) {

    function trimEmptyString(mixed $value): mixed
    {
        return (\is_string($value) && filled($value)) ? trim($value) : $value;
    }
}
