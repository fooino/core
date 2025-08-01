<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Actions\Admin\ActivateLanguageAction;
use Fooino\Core\Actions\Admin\DeactivateLanguageAction;
use Fooino\Core\Enums\LanguageStatus;
use Fooino\Core\Models\Language;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

class ChangeLanguageStatusUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function test_the_change_language_status_actions()
    {
        Artisan::call('sync:languages');

        $language = Language::active()->first();

        $updated = app(DeactivateLanguageAction::class)->run(language: $language);

        $this->assertTrue($updated->status == LanguageStatus::INACTIVE->value);

        $this->assertDatabaseHas(
            'languages',
            [
                'id' => $language->id,
                'status' => LanguageStatus::INACTIVE->value,
            ]
        );

        $updated = app(ActivateLanguageAction::class)->run(language: $language);

        $this->assertTrue($updated->status == LanguageStatus::ACTIVE->value);

        $this->assertDatabaseHas(
            'languages',
            [
                'id' => $language->id,
                'status' => LanguageStatus::ACTIVE->value,
            ]
        );
    }
}
