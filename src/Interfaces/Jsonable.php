<?php

namespace Fooino\Core\Interfaces;

use Illuminate\Http\JsonResponse;

interface Jsonable
{
    /**
     * Check a variable is json or not.
     *
     * @param  int|float|string|null|bool|array|object $value
     * 
     * @return bool
     */
    public function is(int|float|string|null|bool|array|object $value): bool;


    /**
     * Encode a variable to json.
     *
     * @param  int|float|string|null|bool|array|object $value
     * @param  int  $flags
     * @param  int  $depth
     * 
     * @return string|false
     */
    public function encode(int|float|string|null|bool|array|object $value, int $flags = 0, int $depth = 512): string|false;


    /**
     * Decode a json to variable.
     *
     * @param  int|float|string|null|bool|array|object  $json
     * @param  bool|null  $associative
     * @param  int  $depth
     * @param  int  $flags
     * 
     * @return mixed
     */
    public function decode(int|float|string|null|bool|array|object $json, bool|null $associative = null, int $depth = 512, int $flags = 0): mixed;


    /**
     * Decode a json to array.
     *
     * @param  int|float|string|null|bool|array|object  $json
     * 
     * @return array
     */
    public function decodeToArray(int|float|string|null|bool|array|object $json): array;

    /**
     * Return response to user.
     *
     * @param  int  $status
     * @param  string  $message
     * @param  array  $errors
     * @param  array  $data
     * @param  array  $additional
     * @param  array  $headers
     * @param  int  $options
     * 
     * @return JsonResponse
     */
    public function response(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [], array $headers = [], int $options = 0): JsonResponse;


    /**
     * return template for response.
     *
     * @return array
     */
    public function responseTemplate(): array;
}
