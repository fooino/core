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
     * Get math instance base on precision
     */
    function math(int $precision = 12): Mathable
    {
        return Math::setPrecision(precision: $precision);
    }
}

if (!function_exists('isZero')) {
    /**
     * Check the value is zero or not
     */
    function isZero(int|float|string|null|bool|array|object|callable $value): bool
    {
        $value = (is_object($value) && $value instanceof Stringable) ? $value->__toString() : $value;

        if (is_numeric($value)) {

            $regex = [
                '/',
                '^',                    // start with
                '[-+]?',                // optional minus and plus sign
                '0*',                   // zero or more zeros
                '\.?',                  // optional decimal point
                '0*',                   // – zero or more zeros (fractional part)
                '(?:[Ee][+-]?\d+)?',    // optional exponent part
                '$',                    // end with
                '/'
            ];

            return preg_match(pattern: implode('', $regex), subject: trim((string) $value));
        }

        return false;
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
