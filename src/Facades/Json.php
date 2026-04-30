<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool is(int|float|string|null|bool|array|object $value)
 * 
 * @method static string|false encode(int|float|string|null|bool|array|object $value, int $flags = 0, int $depth = 512)
 * 
 * @method static mixed decode(int|float|string|null|bool|array|object $json, bool|null $associative = null, int $depth = 512, int $flags = 0)
 * @method static array decodeToArray(int|float|string|null|bool|array|object $json)
 * 
 * @method static \Illuminate\Http\JsonResponse response(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [], array $headers = [], int $options = 0)
 * 
 * @method static array responseTemplate()
 *
 * @see \Fooino\Core\Concretes\Json\JsonManager
 * @see \Fooino\Core\Concretes\Json\FooinoJsonHandler
 */
class Json extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fooino-json-facade';
    }
}
