<?php

namespace Fooino\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool is($string)
 * @method static mixed encode($mixed, int $flags = 0, int $depth = 512)
 * @method static mixed decode($json, $associative = null, int $depth = 512, int $flags = 0)
 * @method static array decodeToArray($json)
 * @method static \Illuminate\Http\JsonResponse response(int $status = 200, string $message = '', array $data = [], array $errors = [], array $headers = [])
 * @method static array template()
 *
 * @see \Fooino\Core\Concretes\Json\JsonResponse
 */
class Json extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'json-facade';
    }
}
