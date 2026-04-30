<?php

namespace Fooino\Core\Concretes\Json;

use Fooino\Core\Interfaces\Jsonable;
use Illuminate\Http\JsonResponse;

class FooinoJsonHandler implements Jsonable
{
    public function is(int|float|string|null|bool|array|object $value): bool
    {
        return !is_string($value) ? false : json_validate(json: $value);
    }

    public function encode(
        int|float|string|null|bool|array|object $value,
        int $flags = 0,
        int $depth = 512
    ): string|false {
        return $this->is(value: $value) ? $value : \json_encode(value: $value, flags: $flags, depth: $depth);
    }

    public function decode(
        int|float|string|null|bool|array|object $json,
        bool|null $associative = null,
        int $depth = 512,
        int $flags = 0
    ): mixed {
        return !$this->is(value: $json) ? $json : \json_decode(json: $json, associative: $associative, depth: $depth, flags: $flags);
    }

    public function decodeToArray(int|float|string|null|bool|array|object $json): array
    {
        return (array) $this->decode(json: $json, associative: true);
    }

    public function response(
        int $status = 200,
        string $message = '',
        array $errors = [],
        array $data = [],
        array $additional = [],
        array $headers = [],
        int $options = 0
    ): JsonResponse {

        return response()
            ->json(
                data: [
                    'status'                => $status,
                    'success'               => ($status >= 200 && $status <= 299) ? true : false,
                    'message'               => $message,
                    'errors'                => $errors,
                    'data'                  => $data,
                    'additional'            => $additional,
                ],
                status: $status,
                headers: $headers,
                options: $options
            );
    }

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
