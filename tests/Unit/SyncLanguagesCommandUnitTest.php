<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Models\Language;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

class SyncLanguagesCommandUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function test_sync_language_command()
    {
        Artisan::call('sync:languages');

        $this->assertEquals(Language::count(), collect(config('fooino-core-languages', []))->count());

        $this->assertDatabaseHas(
            'languages',
            [
                'code'      => 'fa',
                'name'      => 'فارسی',
                'direction' => 'RTL',
                'status'    => 'ACTIVE',
                'state'     => 'DEFAULT',
                'timezones' => jsonEncode([
                    'Asia/Tehran'
                ])
            ]
        );

        $this->assertDatabaseHas(
            'languages',
            [
                'code'      => 'en',
                'name'      => 'English',
                'direction' => 'LTR',
                'status'    => 'INACTIVE',
                'state'     => 'NON_DEFAULT'
            ]
        );

        config([
            'fooino-core-languages' =>
            array_merge(
                config('fooino-core-languages'),
                [
                    [
                        'code'      => 'foo',
                        'name'      => 'Bar',
                        'direction' => 'RTL',
                        'status'    => 'ACTIVE',
                        'state'     => 'DEFAULT'
                    ]
                ]
            )
        ]);

        Artisan::call('sync:languages');

        $this->assertDatabaseHas('languages', [
            'code'      => 'foo',
            'name'      => 'Bar',
            'direction' => 'RTL',
            'status'    => 'ACTIVE',
            'state'     => 'DEFAULT'
        ]);
    }
}
