<?php

namespace Fooino\Core\Concretes\Json;

use Fooino\Core\Interfaces\Jsonable;
use Illuminate\Http\JsonResponse;

class FooinoJsonHandler implements Jsonable
{
    /**
     * Check whether the input is a valid JSON string, preserving non-string values as-is
     */
    public function is(int|float|string|null|bool|array|object $value): bool
    {
        return is_string($value) && json_validate(json: $value);
    }

    /**
     * Serialize any value to JSON, passing through strings that are already valid JSON to avoid double-encoding
     */
    public function encode(int|float|string|null|bool|array|object $value, int $flags = 0, int $depth = 512): string|false
    {
        return $this->is(value: $value) ? $value : json_encode(value: $value, flags: $flags, depth: $depth);
    }

    /**
     * Format a value as human-readable JSON with HTML-safe escaping, suitable for display or debugging
     */
    public function encodePretty(string|array $value): string
    {
        if (is_null(nullIfBlank(value: $value))) {
            return '';
        }

        $input = $this->is(value: $value) ? $this->decodeToArray(json: $value) : $value;

        $encoded = $this->encode(value: $input, flags: JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return e(value: $encoded);
    }

    /**
     * Parse JSON back to its original PHP type, passing through non-JSON values unchanged
     */
    public function decode(int|float|string|null|bool|array|object $json, bool|null $associative = null, int $depth = 512, int $flags = 0): int|float|string|null|bool|array|object
    {
        return !$this->is(value: $json) ? $json : json_decode(json: $json, associative: $associative, depth: $depth, flags: $flags);
    }

    /**
     * Decode JSON to an associative array, with a typed return for array operations
     */
    public function decodeToArray(int|float|string|null|bool|array|object $json): array
    {
        return (array) $this->decode(json: $json, associative: true);
    }

    /**
     * Build a JSON HTTP response with a consistent status, success, message, errors, and data structure
     */
    public function respond(
        int $status = 200,
        string $message = '',
        array $errors = [],
        array $data = [],
        array $additional = [],
        array $headers = [],
        int $options = 0
    ): JsonResponse {

        $payload = array_merge(
            $this->responseTemplate(),
            [
                'status'        => $status,
                'success'       => $status >= 200 && $status <= 299,
                'message'       => $message,
                'errors'        => $errors,
                'data'          => $data,
                'additional'    => $additional,
            ]
        );

        return response()->json(
            data: $payload,
            status: $status,
            headers: $headers,
            options: $options
        );
    }

    /**
     * Define the default shape of a JSON API response so every response starts from a consistent structure
     */
    public function responseTemplate(): array
    {
        return [
            'status'        => 200,
            'success'       => true,
            'message'       => '',
            'errors'        => [],
            'data'          => [],
            'additional'    => []
        ];
    }
}
