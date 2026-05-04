<?php

use Fooino\Core\Facades\Json;
use Illuminate\Http\JsonResponse;

if (!function_exists('isJson')) {
    function isJson(int|float|string|null|bool|array|object $value): bool
    {
        return Json::is(value: $value);
    }
}

if (!function_exists('jsonEncode')) {
    function jsonEncode(int|float|string|null|bool|array|object $value, int $flags = 0, int $depth = 512): string|false
    {
        return Json::encode(value: $value, flags: $flags, depth: $depth);
    }
}

if (!function_exists('jsonEncodePrettified')) {
    function jsonEncodePrettified(string|array $value): string
    {
        return Json::encodePrettified(value: $value);
    }
}

if (!function_exists('jsonDecode')) {
    function jsonDecode(int|float|string|null|bool|array|object $json, bool|null $associative = null, int $depth = 512, int $flags = 0): mixed
    {
        return Json::decode(json: $json, associative: $associative, depth: $depth, flags: $flags);
    }
}

if (!function_exists('jsonDecodeToArray')) {
    function jsonDecodeToArray(int|float|string|null|bool|array|object $json): array
    {
        return Json::decodeToArray(json: $json);
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [], array $headers = [], int $options = 0): JsonResponse
    {
        return Json::response(status: $status, message: $message, errors: $errors, data: $data, additional: $additional, headers: $headers, options: $options);
    }
}

if (!function_exists('nullIfBlank')) {

    function nullIfBlank(int|float|string|null|bool|array|object|callable $value, int|float|string|null|bool|array|object|callable $fallback = null): int|float|string|null|bool|array|object|callable
    {
        return ((blank($value) || (is_string($value) && (strtolower($value) == 'null' || blank(trim(str_replace(["'", '"'], '', trim($value))))))) ? null : $value) ?? $fallback;
    }
}
