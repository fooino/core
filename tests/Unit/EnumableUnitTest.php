<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Enumable;

class EnumableUnitTest extends TestCase
{
    public function test_values()
    {
        $this->assertTrue(Foobar::values() == ['ACTIVE', 'INACTIVE']);
    }

    public function test_random_value()
    {
        $random = Foobar::randomValue();
        $this->assertTrue($random == 'ACTIVE' || $random == 'INACTIVE');
    }

    public function test_info_method()
    {
        $this->assertTrue(
            Foobar::info() == [
                Foobar::maker(key: 'ACTIVE'),
                Foobar::maker(key: 'INACTIVE')
            ]
        );
    }

    public function test_detail_method()
    {
        $this->assertTrue(Foobar::from(value: 'ACTIVE')->detail() == Foobar::maker(key: 'ACTIVE'));
        $this->assertTrue(Foobar::from(value: 'INACTIVE')->detail() == Foobar::maker(key: 'INACTIVE'));
    }

    public function test_maker()
    {
        $this->assertTrue(
            Foobar::maker()
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
            Foobar::maker(
                key: Foobar::ACTIVE->value,
                query: 'status=' . Foobar::ACTIVE->value,
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

enum Foobar: string
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
