<?php

namespace Fooino\Core\Http\Middleware;

use Fooino\Core\Tasks\Tools\PrettifyInputTask;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class PrettifyRequestMiddleware extends TransformsRequest
{
    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        return app(PrettifyInputTask::class)->run(
            key: $key,
            value: $value
        );
    }
}
