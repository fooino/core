<?php


namespace Fooino\Core\Actions\Admin;

use Fooino\Core\Models\Language;
use Fooino\Core\Enums\LanguageStatus;
use Fooino\Core\Tasks\Language\RecacheActiveLanguagesTask;

class ActivateLanguageAction
{
    public function run(Language $language): Language
    {
        return dbTransaction(function () use ($language) {

            $language->update([
                'status' => LanguageStatus::ACTIVE->value,
            ]);

            app(RecacheActiveLanguagesTask::class)->run();

            return $language;
        });
    }
}
