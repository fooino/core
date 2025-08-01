<?php

namespace Fooino\Core\Tasks\Tools;

use Fooino\Core\Abstracts\SingletonableTask;
use Illuminate\Support\Facades\Cache;

class GetFooinoModelsFromCacheTask extends SingletonableTask
{
    public function getData(): mixed
    {
        return Cache::remember(
            key: FOOINO_MODELS_CACHE_KEY,
            ttl: FOOINO_MEDIUM_TTL_TIME,
            callback: function () {
                return app(GetFooinoModelsTask::class)->run();
            }
        );
    }
}
