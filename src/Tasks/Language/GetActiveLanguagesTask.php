<?php

namespace Fooino\Core\Tasks\Language;

use Fooino\Core\Abstracts\SingletonableTask;
use Fooino\Core\Models\Language;
use Illuminate\Support\Facades\Cache;

class GetActiveLanguagesTask extends SingletonableTask
{
    public function getData(): mixed
    {
        return Cache::remember(
            key: FOOINO_ACTIVE_LANGUAGES_CACHE_KEY,
            ttl: 60 * 60 * 24, // one day
            callback: function () {
                return Language::select([
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
