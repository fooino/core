<?php

namespace Fooino\Core\Tasks\Language;

use Illuminate\Support\Facades\Cache;

class RecacheActiveLanguagesTask
{
    public function run(): void
    {
        Cache::forget(FOOINO_ACTIVE_LANGUAGES_CACHE_KEY);
        app(GetActiveLanguagesFromCacheTask::class)->reset()->run();
    }
}
