<?php

namespace Fooino\Core\Interfaces;

use Illuminate\Http\JsonResponse;

interface Jsonable
{
    /**
     * Determine whether a string is valid JSON
     */
    public function is(int|float|string|null|bool|array|object $value): bool;

    /**
     * Serialize a value to a JSON string, passing through values that are already valid JSON
     */
    public function encode(int|float|string|null|bool|array|object $value, int $flags = 0, int $depth = 512): string|false;

    /**
     * Serialize a value to a human-readable JSON string with HTML-safe escaping for display
     */
    public function encodePretty(string|array $value): string;

    /**
     * Convert a JSON string back to its original PHP value
     */
    public function decode(int|float|string|null|bool|array|object $json, bool|null $associative = null, int $depth = 512, int $flags = 0): int|float|string|null|bool|array|object;

    /**
     * Convert a JSON string to an associative array
     */
    public function decodeToArray(int|float|string|null|bool|array|object $json): array;

    /**
     * Build a JSON HTTP response with a standardized structure for API responses
     */
    public function respond(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [], array $headers = [], int $options = 0): JsonResponse;

    /**
     * Get the default structure used for JSON API responses
     */
    public function responseTemplate(): array;
}
