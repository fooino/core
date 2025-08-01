<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Actions\Admin\ActivateLanguageAction;
use Fooino\Core\Models\Language;
use Fooino\Core\Tasks\Language\GetActiveLanguagesFromCacheTask;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GetActiveLanguagesFromCacheTaskUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function test_get_active_languages_task()
    {
        $this->artisan('sync:languages');

        app(ActivateLanguageAction::class)->run(language: Language::inactive()->first());

        $this->assertTrue(app(GetActiveLanguagesFromCacheTask::class)->run()->count() == 2);

        foreach (app(GetActiveLanguagesFromCacheTask::class)->run() as $language) {
            $this->assertTrue($language->status == 'ACTIVE');
        }
    }
}
