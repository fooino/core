<?php

namespace Fooino\Core\Concretes\Json;

use Illuminate\Http\JsonResponse;
use stdClass;

class JsonManager
{
    /**
     * Check a variable is json or not.
     *
     * @param  mixed  $string
     * 
     * @return bool
     */
    public function is(mixed $string): bool
    {
        if (
            !\is_string($string)
        ) {
            return false;
        }
        return json_validate(json: $string);
    }

    /**
     * Encode a variable to json.
     *
     * @param  mixed  $mixed
     * @param  int  $flags
     * @param  int  $depth
     * 
     * @return mixed
     */
    public function encode(
        mixed $mixed,
        int $flags = 0,
        int $depth = 512
    ): mixed {
        return (\is_resource($mixed)) ? '' : (($this->is($mixed)) ? $mixed : \json_encode($mixed, $flags, $depth));
    }

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
    public function decode(
        mixed $json,
        bool|null $associative = null,
        int $depth = 512,
        int $flags = 0
    ): mixed {
        return !$this->is($json) ? (blank($json) ? $this->type(value: $json) : $json) : \json_decode($json, $associative, $depth, $flags);
    }


    /**
     * Decode a json to array.
     *
     * @param  mixed  $json
     * 
     * @return array
     */
    public function decodeToArray(mixed $json): array
    {
        return (array) $this->decode(json: $json, associative: true);
    }

    /**
     * Return response to user.
     *
     * @param  int  $status
     * @param  string  $message
     * @param  array  $data
     * @param  array  $errors
     * @param  array  $headers
     * 
     * @return JsonResponse
     */
    public function response(
        int $status = 200,
        string $message = '',
        array $data = [],
        array $errors = [],
        array $headers = []
    ): JsonResponse {

        return response()
            ->json(
                data: [
                    'status'                => $status,
                    'success'               => ($status >= 200 && $status <= 299) ? true : false,
                    'message'               => $message,
                    'errors'                => $errors,
                    'data'                  => $data
                ],
                status: $status,
                headers: $headers
            );
    }

    /**
     * return template for response.
     *
     * @return array
     */
    public function template(): array
    {
        return [
            'status'    => 200,
            'success'   => true,
            'message'   => '',
            'errors'    => [],
            'data'      => []
        ];
    }


    protected function type($value)
    {
        return $this->types()[\gettype($value)];
    }

    protected function types(): array
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
