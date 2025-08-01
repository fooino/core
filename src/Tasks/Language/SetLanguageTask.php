<?php

namespace Fooino\Core\Tasks\Language;


class SetLanguageTask
{
    public function run(string|null $language = null): void
    {
        $default = app(GetDefaultLanguageTask::class)->run()?->code ?? 'fa';
        $locales = collect(app(GetActiveLanguagesFromCacheTask::class)->run())->pluck('code')->toArray();

        $update = [
            'locales'                                   => filled($locales) ? $locales : ['en', 'fa', 'ar', 'zh', 'es', 'hi', 'pt', 'bn', 'ru', 'ja', 'vi', 'tr', 'ko', 'fr', 'de', 'it', 'ku', 'ku-so', 'ku-su'],
            'locale_separator'                          => '-',
            'locale'                                    => $language ?? $default,
            'use_fallback'                              => true,
            'use_property_fallback'                     => true,
            'translation_model_namespace'               => null,
            'translation_suffix'                        => 'Translation',
            'locale_key'                                => 'locale',
            'to_array_always_loads_translations'        => true,
            'fallback_locale'                           => $default,
        ];

        foreach ($update as $key => $value) {
            config(['translatable.' . $key => $value]);
        }
    }
}
