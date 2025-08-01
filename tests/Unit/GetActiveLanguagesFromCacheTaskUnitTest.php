<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tasks\Language\GetActiveLanguagesFromCacheTask;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GetActiveLanguagesFromCacheTaskUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function test_get_active_languages_task_v1()
    {
        $this->artisan('sync:languages');
        foreach (app(GetActiveLanguagesFromCacheTask::class)->run() as $language) {
            $this->assertTrue($language->status == 'ACTIVE');
        }
    }
}
