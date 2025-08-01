<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tasks\Language\SetLanguageTask;
use Fooino\Core\Tasks\Seeder\LoadSeederConfigTask;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

class SetLanguageTaskUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        app(LoadSeederConfigTask::class)->run(path: base_path('config/fooino-core-languages.php'));
        config([
            'fooino-core-languages.1.status' => 'ACTIVE'
        ]);
        Artisan::call('sync:languages');
    }

    public function test_the_task_without_param()
    {
        app(SetLanguageTask::class)->run();

        $this->assertEquals(config('translatable.locale'), 'fa');
        $this->assertEquals(config('translatable.fallback_locale'), 'fa');
        $this->assertEquals(config('translatable.locales'), ['fa', 'en']);
    }
    public function test_the_task_with_param()
    {
        app(SetLanguageTask::class)->run(language: 'en');

        $this->assertEquals(config('translatable.locale'), 'en');
        $this->assertEquals(config('translatable.fallback_locale'), 'fa');
        $this->assertEquals(config('translatable.locales'), ['fa', 'en']);
    }
}
