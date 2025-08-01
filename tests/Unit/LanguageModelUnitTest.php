<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Enums\LanguageState;
use Fooino\Core\Enums\LanguageStatus;
use Fooino\Core\Enums\Direction;
use Fooino\Core\Models\Language;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

class LanguageModelUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('sync:languages');
    }

    public function test_code_accessor_and_mutator()
    {
        Language::create([
            'name'      => 'foobar',
            'code'      => 'AF',
            'direction' => 'RTL',
            'status'    => 'ACTIVE',
            'state'     => 'UNDEFAULT'
        ]);

        $this->assertEquals(Language::latest('id')->first()->code, 'af');
        $this->assertDatabaseHas('languages', [
            'name'      => 'foobar',
            'code'      => 'af',
            'direction' => 'RTL',
            'status'    => 'ACTIVE',
            'state'     => 'UNDEFAULT'
        ]);

        Language::insert([
            [
                'name'      => 'foobar',
                'code'      => 'AN',
                'direction' => 'RTL',
                'status'    => 'ACTIVE',
                'state'     => 'UNDEFAULT'
            ]
        ]);
        $this->assertDatabaseHas('languages', [
            'name'      => 'foobar',
            'code'      => 'AN',
            'direction' => 'RTL',
            'status'    => 'ACTIVE',
            'state'     => 'UNDEFAULT'
        ]);

        $this->assertEquals(Language::latest('id')->first()->code, 'an');
    }

    public function test_the_direction_detail_attribute()
    {
        $language = Language::codeFilter('fa')->first();
        $this->assertTrue($language->direction_detail == Direction::rtl());

        $language = Language::codeFilter('en')->first();
        $this->assertTrue($language->direction_detail == Direction::ltr());
    }

    public function test_status_accessor()
    {
        $language = Language::first();

        $this->assertEquals($language->status_detail, LanguageStatus::from(value: $language->status)->detail());
        $this->assertEquals($language->status_detail['key'], LanguageStatus::ACTIVE->value);
        $this->assertEquals($language->statuses, LanguageStatus::statuses(id: $language->id));
        $this->assertEquals($language->statuses[0]['key'], LanguageStatus::ACTIVE->value);
    }

    public function test_state_accessor()
    {
        $language = Language::first();

        $this->assertEquals($language->state_detail, LanguageState::from(value: $language->state)->detail());
        $this->assertEquals($language->state_detail['key'], LanguageState::DEFAULT->value);
    }

    public function test_editable_accessor()
    {
        $this->assertTrue(Language::undefault()->first()->editable == 1);
        $this->assertTrue(Language::default()->first()->editable == 0);
    }

    public function test_the_code_scope()
    {
        $this->assertTrue(filled(Language::codeFilter('en')->first()));
        $this->assertTrue(blank(Language::codeFilter('foobar')->first()));

        $this->assertTrue(Language::codeFilter(null)->count() == Language::count());
    }

    public function test_the_search_scope()
    {
        $this->assertTrue(filled(Language::search('en')->first()));
        $this->assertTrue(filled(Language::search('English')->first()));
        $this->assertTrue(blank(Language::search('foobar')->first()));

        $this->assertTrue(Language::search(null)->count() == Language::count());
    }

    public function test_direction_scope()
    {
        $this->assertTrue(Language::direction(null)->count() == Language::count());
        $this->assertTrue(Language::direction('RTL')->first()->code == 'fa');
        $this->assertTrue(Language::direction('LTR')->first()->code == 'en');
        $this->assertTrue(Language::RTL()->first()->code == 'fa');
        $this->assertTrue(Language::LTR()->first()->code == 'en');
    }

    public function test_status_scope()
    {
        Language::find(1)->update([
            'state'         => LanguageState::DEFAULT->value,
            'status'        => LanguageStatus::ACTIVE->value
        ]);

        $this->assertEquals(Language::status('ACTIVE')->first()->id, 1);
        $this->assertEquals(Language::status('INACTIVE')->first()->id, 2);
        $this->assertEquals(Language::status(null)->count(), Language::count());

        $this->assertEquals(Language::active()->first()->id, 1);
        $this->assertEquals(Language::inactive()->first()->id, 2);
    }

    public function test_state_scope()
    {
        Language::find(1)->update([
            'state'         => LanguageState::DEFAULT->value,
            'status'        => LanguageStatus::ACTIVE->value
        ]);

        $this->assertEquals(Language::state('DEFAULT')->first()->id, 1);
        $this->assertEquals(Language::state('UNDEFAULT')->first()->id, 2);
        $this->assertEquals(Language::state(null)->count(), Language::count());

        $this->assertEquals(Language::default()->first()->id, 1);
        $this->assertEquals(Language::undefault()->first()->id, 2);
    }
}
