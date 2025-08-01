<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Actions\Admin\GetPaginatedLanguagesAction;
use Fooino\Core\Models\Language;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

class GetPaginatedLanguagesActionUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function test_the_get_paginated_languages_action()
    {
        Artisan::call('sync:languages');

        $languages = app(GetPaginatedLanguagesAction::class)->run();

        $this->assertTrue($languages->count() == Language::count());
    }
}