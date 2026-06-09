<?php

use Fooino\Core\Facades\Date;
use Fooino\Core\Facades\Json;
use Fooino\Core\Facades\Math;

use Fooino\Core\Interfaces\Mathable;
use Illuminate\Http\JsonResponse;

if (!function_exists('isJson')) {
    /**
     * Validate a value is json or not.
     */
    function isJson(int|float|string|null|bool|array|object $value): bool
    {
        return Json::is(value: $value);
    }
}

if (!function_exists('jsonEncode')) {
    /**
     * Encode a value to json format.
     */
    function jsonEncode(int|float|string|null|bool|array|object $value, int $flags = 0, int $depth = 512): string|false
    {
        return Json::encode(value: $value, flags: $flags, depth: $depth);
    }
}

if (!function_exists('jsonEncodePrettified')) {
    /**
     * Encode a value to json format for showing purpose.
     */
    function jsonEncodePrettified(string|array $value): string
    {
        return Json::encodePrettified(value: $value);
    }
}

if (!function_exists('jsonDecode')) {
    /**
     * Decode a json to value.
     */
    function jsonDecode(int|float|string|null|bool|array|object $json, bool|null $associative = null, int $depth = 512, int $flags = 0): mixed
    {
        return Json::decode(json: $json, associative: $associative, depth: $depth, flags: $flags);
    }
}

if (!function_exists('jsonDecodeToArray')) {
    /**
     * Decode a json to array.
     */
    function jsonDecodeToArray(int|float|string|null|bool|array|object $json): array
    {
        return Json::decodeToArray(json: $json);
    }
}

if (!function_exists('jsonResponse')) {
    /**
     * Return response to user.
     */
    function jsonResponse(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [], array $headers = [], int $options = 0): JsonResponse
    {
        return Json::response(status: $status, message: $message, errors: $errors, data: $data, additional: $additional, headers: $headers, options: $options);
    }
}

if (!function_exists('dateConvert')) {
    /**
     * Convert date base on timezone and the format you desire.
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     */
    function dateConvert(string|int|null $date, string $format = 'Y-m-d H:i:s', DateTimeZone|string $from = 'UTC', DateTimeZone|string $to = 'UTC', string $fallback = '', bool $throwException = false): string
    {
        return Date::convert(date: $date, format: $format, from: $from, to: $to, fallback: $fallback, throwException: $throwException);
    }
}

if (!function_exists('math')) {
    /**
     * Get a fresh Mathable instance configured with the given precision
     */
    function math(int $precision = 12): Mathable
    {
        return Math::setPrecision(precision: $precision);
    }
}

if (!function_exists('number')) {
    /**
     * Format one or more numbers by truncating them to the configured precision, removing trailing zeros, and returning clean numeric strings
     */
    function number(mixed ...$number): string|array
    {
        return Math::number(...$number);
    }
}

if (!function_exists('numberFormat')) {
    /**
     * Format a number with thousands separators and apply precision truncation, returning a locale-friendly currency-style string
     */
    function numberFormat(string|int|float $number, string $thousandsSeparator = ','): string
    {
        return Math::numberFormat(number: $number, thousandsSeparator: $thousandsSeparator,);
    }
}

if (!function_exists('sum')) {
    /**
     * Add a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    function sum(mixed ...$operand): string
    {
        return Math::sum(...$operand);
    }
}

if (!function_exists('subtract')) {
    /**
     * Subtract a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function subtract(mixed ...$operand): string
    {
        return Math::subtract(...$operand);
    }
}

if (!function_exists('multiply')) {
    /**
     * Multiply a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    function multiply(mixed ...$operand): string
    {
        return Math::multiply(...$operand);
    }
}

if (!function_exists('divide')) {
    /**
     * Divide a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function divide(mixed ...$operand): string
    {
        return Math::divide(...$operand);
    }
}

if (!function_exists('remainder')) {
    /**
     * Compute the modulus (remainder) of a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function remainder(mixed ...$operand): string
    {
        return Math::remainder(...$operand);
    }
}

if (!function_exists('roundUp')) {
    /**
     * Round a number up to the next integer (ceiling), away from zero
     */
    function roundUp(string|int|float|array $number): string|array
    {
        return Math::roundUp(number: $number);
    }
}

if (!function_exists('roundDown')) {
    /**
     * Round a number down to the previous integer (floor), toward zero
     */
    function roundDown(string|int|float|array $number): string|array
    {
        return Math::roundDown(number: $number);
    }
}

if (!function_exists('roundClose')) {
    /**
     * Round a number to a specified precision using a configurable rounding mode
     */
    function roundClose(string|int|float|array $number, int $precision = 0, RoundingMode $mode = RoundingMode::HalfAwayFromZero): string|array
    {
        return Math::roundClose(number: $number, precision: $precision, mode: $mode);
    }
}

if (!function_exists('greaterThan')) {
    /**
     * Compare two numbers
     */
    function greaterThan(string|int|float $a, string|int|float $b): bool
    {
        return Math::greaterThan(a: $a, b: $b);
    }
}

if (!function_exists('greaterThanOrEqual')) {
    /**
     * Compare two numbers
     */
    function greaterThanOrEqual(string|int|float $a, string|int|float $b): bool
    {
        return Math::greaterThanOrEqual(a: $a, b: $b);
    }
}

if (!function_exists('lessThan')) {
    /**
     * Compare two numbers
     */
    function lessThan(string|int|float $a, string|int|float $b): bool
    {
        return Math::lessThan(a: $a, b: $b);
    }
}

if (!function_exists('lessThanOrEqual')) {
    /**
     * Compare two numbers
     */
    function lessThanOrEqual(string|int|float $a, string|int|float $b): bool
    {
        return Math::lessThanOrEqual(a: $a, b: $b);
    }
}

if (!function_exists('equal')) {
    /**
     * Compare two numbers
     */
    function equal(string|int|float $a, string|int|float $b): bool
    {
        return Math::equal(a: $a, b: $b);
    }
}

if (!function_exists('notEqual')) {
    /**
     * Compare two numbers
     */
    function notEqual(string|int|float $a, string|int|float $b): bool
    {
        return Math::notEqual(a: $a, b: $b);
    }
}

if (!function_exists('isZero')) {
    /**
     * Check the value is zero or not
     */
    function isZero(int|float|string|null|bool|array|object|callable $value): bool
    {
        $value = (is_object($value) && $value instanceof Stringable) ? $value->__toString() : $value;

        if (
            is_null($value) ||
            is_bool($value) ||
            is_array($value) ||
            is_object($value) ||
            $value instanceof Closure
        ) {
            return false;
        }

        return preg_match(pattern: '/^[+-]?(?:0+\.?0*|\.0+|(?:0+\.?0*|\.0*)[Ee][+-]?\d+)$/', subject: trim((string) $value)) === 1;
    }
}

if (!function_exists('nullIfBlank')) {
    /**
     * Returns a fallback value when the input is considered "blank" or a null-like string which usually produce by js.
     */
    function nullIfBlank(int|float|string|null|bool|array|object|callable $value, int|float|string|null|bool|array|object|callable $fallback = null): int|float|string|null|bool|array|object|callable
    {
        return ((blank($value) || (is_string($value) && trim(str_replace(["'", "`", '"', "null", "undefined", "nan"], '', strtolower($value))) === '')) ? null : $value) ?? $fallback;
    }
}

if (!function_exists('nullIfBlankOrZero')) {
    /**
     * Returns a fallback value when the input is considered "blank" or a null-like string or 0.
     */
    function nullIfBlankOrZero(int|float|string|null|bool|array|object|callable $value, int|float|string|null|bool|array|object|callable $fallback = null): int|float|string|null|bool|array|object|callable
    {
        $value = nullIfBlank(value: $value);

        return (isZero(value: $value) ? null : $value) ?? $fallback;
    }
}

if (!function_exists('removeComma')) {
    /**
     * Remove comma between letters when the value is string or array
     */
    function removeComma(int|float|string|null|bool|array $value, string $replace = ''): int|float|string|null|bool|array
    {
        return (\is_string($value) || \is_array($value)) ? \str_replace(',', $replace, $value) : $value;
    }
}

if (!function_exists('removeSpace')) {
    /**
     * Remove space between letters when the value is string or array
     */
    function removeSpace(int|float|string|null|bool|array $value, string $replace = ''): int|float|string|null|bool|array
    {
        return (\is_string($value) || \is_array($value)) ? \str_replace(' ', $replace, $value) : $value;
    }
}

if (!function_exists('sanitizeNumber')) {
    /**
     * Remove space and comma from value
     */
    function sanitizeNumber(int|float|string|null|bool|array $value): int|float|string|null|bool|array
    {
        return removeSpace(value: removeComma(value: $value));
    }
}

if (!function_exists('replaceSlashToDash')) {
    /**
     * Replace slashes to dashes when the value is string or array
     */
    function replaceSlashToDash(int|float|string|null|bool|array $value): int|float|string|null|bool|array
    {
        return (\is_string($value) || \is_array($value)) ? \str_replace('/', '-', $value) : $value;
    }
}

if (!function_exists('setDefaultLocale')) {
    /**
     * Setter for 'app.locale' config
     */
    function setDefaultLocale(string $locale): void
    {
        config(['app.locale' => $locale]);
    }
}

if (!function_exists('getDefaultLocale')) {
    /**
     * Getter for 'app.locale' config
     */
    function getDefaultLocale(): string
    {
        return (config('app.locale', 'fa')) ?: 'fa';
    }
}

if (!function_exists('currentDate')) {
    /**
     * Return date in 'Y-m-d' format
     */
    function currentDate(): string
    {
        return \date('Y-m-d');
    }
}

if (!function_exists('currentDateTime')) {
    /**
     * Return date in 'Y-m-d H:i:s' format
     */
    function currentDateTime(): string
    {
        return \date('Y-m-d H:i:s');
    }
}

if (!function_exists('callMethodIfExists')) {
    /**
     * Safely call a method on an object or class if it exists, otherwise return a fallback value.
     */
    function callMethodIfExists(object|string $object, string $method, mixed $fallback = null, array $methodArgs = [], array $constructorArgs = []): mixed
    {
        return method_exists($object, $method) ? (is_string($object) ? (new $object(...$constructorArgs)) : $object)->{$method}(...$methodArgs) : value($fallback, ...$methodArgs);
    }
}
