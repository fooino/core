<?php

namespace Fooino\Core\Tasks\Language;

use Fooino\Core\Enums\LanguageState;

class GetDefaultLanguageTask
{
    public function run()
    {
        return collect(app(GetActiveLanguagesTask::class)->run())->where('state', LanguageState::DEFAULT->value)->first();
    }
}
