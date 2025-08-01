<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tasks\Language\GetDefaultLanguageTask;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

class GetDefaultLanguageTaskUnitTest extends TestCase
{
    use DatabaseMigrations;
    
    public function test_the_task()
    {
        Artisan::call('sync:languages');
        $this->assertEquals(app(GetDefaultLanguageTask::class)->run()->code, 'fa');
    }
}
