<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Enumable;

class EnumableUnitTest extends TestCase
{
    public function test_values()
    {
        $this->assertTrue(FoobarEnum::values() == ['ACTIVE', 'INACTIVE']);
    }

    public function test_random_value()
    {
        $random = FoobarEnum::randomValue();
        $this->assertTrue($random == 'ACTIVE' || $random == 'INACTIVE');
    }

    public function test_info_method()
    {
        $this->assertTrue(
            FoobarEnum::info() == [
                FoobarEnum::maker(key: 'ACTIVE'),
                FoobarEnum::maker(key: 'INACTIVE')
            ]
        );
    }

    public function test_detail_method()
    {
        $this->assertTrue(FoobarEnum::from(value: 'ACTIVE')->detail() == FoobarEnum::maker(key: 'ACTIVE'));
        $this->assertTrue(FoobarEnum::from(value: 'INACTIVE')->detail() == FoobarEnum::maker(key: 'INACTIVE'));
    }

    public function test_maker()
    {
        $this->assertTrue(
            FoobarEnum::maker()
                ==
                [
                    'key'           => 'defaultKey',
                    'name'          => 'unknown',
                    'icon_style'    => 'material-symbols-outlined',
                    'icon'          => '',
                    'color'         => '',
                ]
        );

        $this->assertTrue(
            FoobarEnum::maker(
                key: FoobarEnum::ACTIVE->value,
                query: 'status=' . FoobarEnum::ACTIVE->value,
                endpoint: "languages/1/activate",
                icon: 'icon',
                color: 'red',
                additional: [
                    'foo'   => 'bar'
                ]
            )
                ==
                [
                    'key'           => 'ACTIVE',
                    'name'          => __('msg.active'),
                    'endpoint'      => 'languages/1/activate',
                    'query'         => 'status=ACTIVE',
                    'icon_style'    => 'material-symbols-outlined',
                    'icon'          => 'icon',
                    'color'         => 'red',
                    'foo'           => 'bar'
                ]
        );
    }
}

enum FoobarEnum: string
{
    use Enumable;

    case ACTIVE   = 'ACTIVE';
    case INACTIVE = 'INACTIVE';

    public static function active()
    {
        return self::maker(
            key: self::ACTIVE->value
        );
    }
    public static function inactive()
    {
        return self::maker(
            key: self::INACTIVE->value
        );
    }
}
