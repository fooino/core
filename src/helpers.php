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
    function jsonEncode(mixed $mixed, int $flags = 0, int $depth = 512): string|false
    {
        return Json::encode(mixed: $mixed, flags: $flags, depth: $depth);
    }
}

if (!function_exists('jsonDecode')) {
    function jsonDecode(mixed $json, bool|null $associative = null, int $depth = 512, int $flags = 0): mixed
    {
        return Json::decode(json: $json, associative: $associative, depth: $depth, flags: $flags);
    }
}

if (!function_exists('jsonDecodeToArray')) {
    function jsonDecodeToArray(mixed $json): array
    {
        return Json::decodeToArray(json: $json);
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [],  array $headers = []): JsonResponse
    {
        return Json::response(status: $status, message: $message, errors: $errors, data: $data, additional: $additional,  headers: $headers);
    }
}
