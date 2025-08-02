<?php

namespace Fooino\Core\Tasks\Language;

use Fooino\Core\Abstracts\SingletonableTask;
use Fooino\Core\Models\Language;
use Illuminate\Support\Facades\Cache;

class GetActiveLanguagesFromCacheTask extends SingletonableTask
{
    public function getData(): mixed
    {
        return Cache::remember(
            key: FOOINO_ACTIVE_LANGUAGES_CACHE_KEY,
            ttl: FOOINO_MEDIUM_TTL_TIME,
            callback: function () {
                return Language::disablePrioritiable()
                    ->sortByStateAndStatus()
                    ->select([
                        'id',
                        'name',
                        'code',
                        'direction',
                        'status',
                        'state',
                        'priority',
                        'timezones'
                    ])
                    ->active()
                    ->get();
            }
        );
    }
}
