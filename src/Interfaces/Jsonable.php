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
     * @param  mixed  $mixed
     * @param  int  $flags
     * @param  int  $depth
     * 
     * @return string|false
     */
    public function encode(mixed $mixed, int $flags = 0, int $depth = 512): string|false;


    /**
     * Decode a json to variable.
     *
     * @param  mixed  $json
     * @param  bool|null  $associative
     * @param  int  $depth
     * @param  int  $flags
     * 
     * @return mixed
     */
    public function decode(mixed $json, bool|null $associative = null, int $depth = 512, int $flags = 0): mixed;


    /**
     * Decode a json to array.
     *
     * @param  mixed  $json
     * 
     * @return array
     */
    public function decodeToArray(mixed $json): array;

    /**
     * Return response to user.
     *
     * @param  int  $status
     * @param  string  $message
     * @param  array  $errors
     * @param  array  $data
     * @param  array  $additional
     * @param  array  $headers
     * 
     * @return JsonResponse
     */
    public function response(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [], array $headers = []): JsonResponse;


    /**
     * return template for response.
     *
     * @return array
     */
    public function responseTemplate(): array;
}
