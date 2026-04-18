<?php

namespace Fooino\Core\Concretes\Json;

use Fooino\Core\Interfaces\Jsonable;
use Illuminate\Http\JsonResponse;
use stdClass;

class FooinoJsonHandler implements Jsonable
{
    public function is(int|float|string|null|bool|array|object $value): bool
    {
        return !is_string($value) ? false : json_validate(json: $value);
    }

    public function encode(
        mixed $mixed,
        int $flags = 0,
        int $depth = 512
    ): string|false {
        return (\is_resource($mixed)) ? '' : ($this->is($mixed) ? $mixed : \json_encode(value: $mixed, flags: $flags, depth: $depth));
    }

    public function decode(
        mixed $json,
        bool|null $associative = null,
        int $depth = 512,
        int $flags = 0
    ): mixed {
        return !$this->is($json) ? (blank($json) ? ($this->types()[\gettype($json)]) : $json) : \json_decode(json: $json, associative: $associative, depth: $depth, flags: $flags);
    }

    public function decodeToArray(mixed $json): array
    {
        return (array) $this->decode(json: $json, associative: true);
    }

    public function response(
        int $status = 200,
        string $message = '',
        array $errors = [],
        array $data = [],
        array $additional = [],
        array $headers = []
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
                headers: $headers
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

    private function types(): array
    {
        return [
            'boolean'       => false,
            'integer'       => 0,
            'double'        => 0,
            'string'        => '',
            'array'         => [],
            'object'        => new stdClass,
            'resource'      => '',
            'NULL'          => null,
            'unknown type'  => ''
        ];
    }
}
