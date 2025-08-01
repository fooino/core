<?php


namespace Fooino\Core\Actions\Admin;

use Fooino\Core\Models\Language;
use Fooino\Core\Enums\LanguageStatus;
use Fooino\Core\Tasks\Language\RecacheActiveLanguagesTask;
use Illuminate\Auth\Access\AuthorizationException;

class ActivateLanguageAction
{
    public function run(Language $language): Language
    {
        return dbTransaction(function () use ($language) {


            throw_if(
                !$language->editable,
                new AuthorizationException(
                    message: __(key: 'msg.theDefaultLanguageIsNotEditable')
                )
            );

            $language->update([
                'status' => LanguageStatus::ACTIVE->value,
            ]);

            app(RecacheActiveLanguagesTask::class)->run();

            return $language;
        });
    }
}
