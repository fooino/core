<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Enums\DateType;
use Fooino\Core\Exceptions\CanNotConvertDateException;
use Fooino\Core\Facades\Date;
use Fooino\Core\Tests\TestCase;
use DateTimeZone;

class DateFacadeUnitTest extends TestCase
{
    public function test_convert_utc_to_shamsi_date_method_for_iran_timezone()
    {
        $to = new DateTimeZone('Asia/Tehran');
        $this->assertEquals(Date::convert(date: null, to: $to), '');
        $this->assertEquals(Date::convert(date: 'null', to: $to), '');
        $this->assertEquals(Date::convert(date: '', to: $to), '');
        $this->assertEquals(Date::convert(date: 'test', to: $to), '');
        $this->assertEquals(Date::convert(date: '2022-12-24', format: 'Y/m/d', to: $to), '1401/10/03');
        $this->assertEquals(Date::convert(date: '2022/12/24', format: 'Y/m/d', to: $to), '1401/10/03');
        $this->assertEquals(Date::convert(date: '2021/03/20', format: 'Y/m/d', to: $to), '1399/12/30');
        $this->assertEquals(Date::convert(date: '2022-12-24 19:27:00', to: $to), '1401-10-03 22:57:00');
        $this->assertEquals(Date::convert(date: '2022-12-24 19:10', to: $to), '1401-10-03 22:40:00');
        $this->assertEquals(Date::convert(date: '2022-12-24 19', to: $to), '1401-10-03 03:30:00');
        $this->assertEquals(Date::convert(date: '2022-12-24 time 19:27:00', to: $to), '1401-10-03 22:57:00');
        $this->assertEquals(Date::convert(date: '2022-12-24 19:27:00', format: 'j F Y ساعت H:i:s', to: $to), '3 دی 1401 ساعت 22:57:00');

        $this->assertThrows(
            fn() => Date::convert(date: 'test', to: $to, throwException: true),
            CanNotConvertDateException::class,
            'strtotime can not convert date to timestamp'
        );
        $this->assertThrows(
            fn() => Date::convert(date: null, to: $to, throwException: true),
            CanNotConvertDateException::class,
            'The date is empty'
        );
        $this->assertThrows(
            fn() => Date::convert(date: '', to: $to, throwException: true),
            CanNotConvertDateException::class,
            'The date is empty'
        );
    }

    public function test_convert_utc_to_shamsi_date_method_for_afghanistan_timezone()
    {
        $to = new DateTimeZone('Asia/Kabul');
        $this->assertEquals(Date::convert(date: null, to: $to), '');
        $this->assertEquals(Date::convert(date: 'test', to: $to), '');
        $this->assertEquals(Date::convert(date: '2022-12-24', format: 'Y/m/d', to: $to), '1401/10/03');
        $this->assertEquals(Date::convert(date: '2022/12/24', format: 'Y/m/d', to: $to), '1401/10/03');
        $this->assertEquals(Date::convert(date: '2021/03/20', format: 'Y/m/d', to: $to), '1399/12/30');
        $this->assertEquals(Date::convert(date: '2022-12-24 19:27:00', to: $to), '1401-10-03 23:57:00');
        $this->assertEquals(Date::convert(date: '2022-12-24 19:27:00', format: 'j F Y ساعت H:i:s', to: $to), '3 دی 1401 ساعت 23:57:00');

        $this->assertThrows(
            fn() => Date::convert(date: 'test', to: $to, throwException: true),
            CanNotConvertDateException::class,
            'strtotime can not convert date to timestamp'
        );
        $this->assertThrows(
            fn() => Date::convert(date: null, to: $to, throwException: true),
            CanNotConvertDateException::class,
            'The date is empty'
        );
        $this->assertThrows(
            fn() => Date::convert(date: '', to: $to, throwException: true),
            CanNotConvertDateException::class,
            'The date is empty'
        );
    }

    public function test_convert_shamsi_to_utc_date_method_for_iran_timezone()
    {
        $from = new DateTimeZone('Asia/Tehran');

        $this->assertEquals(Date::convert(date: null, from: $from), '');
        $this->assertEquals(Date::convert(date: 'test', from: $from), '');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y/m/d', from: $from), '2022/12/24');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y-m-d', from: $from), '2022-12-24');
        $this->assertEquals(Date::convert(date: '1399/12/30', format: 'Y/m/d', from: $from), '2021/03/20');
        $this->assertEquals(Date::convert(date: '1401/10/03 22:57:00', from: $from), '2022-12-24 19:27:00');
        $this->assertEquals(Date::convert(date: '1401/10/03', from: $from), '2022-12-24 00:00:00');
        $this->assertEquals(Date::convert(date: '1401/10/03 22:57:00', format: 'j F Y H:i:s', from: $from), '24 December 2022 19:27:00');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'j F Y', from: $from), '24 December 2022');
        $this->assertEquals(Date::convert(date: '1401-10/03 ساعت 22:57:00', from: $from), '2022-12-24 19:27:00');

        $this->assertThrows(
            fn() => Date::convert(date: 'test', from: $from, throwException: true),
            CanNotConvertDateException::class,
            'The date is empty'
        );
        $this->assertThrows(
            fn() => Date::convert(date: null, from: $from, throwException: true),
            CanNotConvertDateException::class,
            'The date is empty'
        );
        $this->assertThrows(
            fn() => Date::convert(date: '', from: $from, throwException: true),
            CanNotConvertDateException::class,
            'The date is empty'
        );
    }

    public function test_convert_shamsi_to_utc_date_method_for_afghanistan_timezone()
    {
        $from = new DateTimeZone('Asia/Kabul');
        $this->assertEquals(Date::convert(date: null, from: $from), '');
        $this->assertEquals(Date::convert(date: 'test', from: $from), '');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y/m/d', from: $from), '2022/12/24');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y-m-d', from: $from), '2022-12-24');
        $this->assertEquals(Date::convert(date: '1401/10/03 13:20:00', from: $from), '2022-12-24 08:50:00');
        $this->assertEquals(Date::convert(date: '1401/10/03 13:20:00', format: 'j F Y H:i:s', from: $from), '24 December 2022 08:50:00');
        $this->assertThrows(fn() => Date::convert(date: 'test', from: $from, throwException: true), CanNotConvertDateException::class);
    }

    public function test_iran_shamsi_to_iran_shamsi()
    {
        $from = new DateTimeZone('Asia/Tehran');
        $to = new DateTimeZone('Asia/Tehran');
        $this->assertEquals(Date::convert(date: null, from: $from, to: $to), '');
        $this->assertEquals(Date::convert(date: 'test', from: $from, to: $to), '');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y/m/d', from: $from, to: $to), '1401/10/03');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y-m-d', from: $from, to: $to), '1401-10-03');
        $this->assertEquals(Date::convert(date: '1401/10/03 22:57:00', from: $from, to: $to), '1401-10-03 22:57:00');

        $this->assertThrows(fn() => Date::convert(date: 'test', from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
        $this->assertThrows(fn() => Date::convert(date: null, from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
        $this->assertThrows(fn() => Date::convert(date: '', from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
    }

    public function test_iran_shamsi_to_afghanistan_shamsi()
    {
        $from = new DateTimeZone('Asia/Tehran');
        $to = new DateTimeZone('Asia/Kabul');
        $this->assertEquals(Date::convert(date: null, from: $from, to: $to), '');
        $this->assertEquals(Date::convert(date: 'test', from: $from, to: $to), '');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y/m/d', from: $from, to: $to), '1401/10/03');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y-m-d', from: $from, to: $to), '1401-10-03');
        $this->assertEquals(Date::convert(date: '1401/10/03 22:57:00', from: $from, to: $to), '1401-10-03 23:57:00');

        $this->assertThrows(fn() => Date::convert(date: 'test', from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
        $this->assertThrows(fn() => Date::convert(date: null, from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
        $this->assertThrows(fn() => Date::convert(date: '', from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
    }

    public function test_afghanistan_shamsi_to_iran_shamsi()
    {
        $from = new DateTimeZone('Asia/Kabul');
        $to = new DateTimeZone('Asia/Tehran');
        $this->assertEquals(Date::convert(date: null, from: $from, to: $to), '');
        $this->assertEquals(Date::convert(date: 'test', from: $from, to: $to), '');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y/m/d', from: $from, to: $to), '1401/10/03');
        $this->assertEquals(Date::convert(date: '1401/10/03', format: 'Y-m-d', from: $from, to: $to), '1401-10-03');
        $this->assertEquals(Date::convert(date: '1401/10/03 22:57:00', from: $from, to: $to), '1401-10-03 21:57:00');

        $this->assertThrows(fn() => Date::convert(date: 'test', from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
        $this->assertThrows(fn() => Date::convert(date: null, from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
        $this->assertThrows(fn() => Date::convert(date: '', from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
    }

    // public function test_convert_utc_to_hijri_date_method_for_oman_timezone()
    // {
    //     $to = new DateTimeZone('Asia/Muscat');
    //     $this->assertEquals(Date::convert(date: null, to: $to), '');
    //     $this->assertEquals(Date::convert(date: 'test', to: $to), '');
    //     $this->assertEquals(Date::convert(date: '2023-06-09', format: 'Y/m/d', to: $to), '1444/11/20');
    //     $this->assertEquals(Date::convert(date: '2023-06-09', format: 'Y-m-d', to: $to), '1444-11-20');

    //     $this->assertEquals(Date::convert(date: '2023-06-09 13:57:10', format: 'Y/m/d H:i:s', to: $to), '1444/11/20 17:57:10');
    //     $this->assertEquals(Date::convert(date: '2023-06-09 13:57:10', format: 'Y-m-d H:i:s', to: $to), '1444-11-20 17:57:10');

    //     $this->assertThrows(fn () => Date::convert(date: 'test', to: $to, throwException: true), CanNotConvertDateException::class);
    //     $this->assertThrows(fn () => Date::convert(date: null, to: $to, throwException: true), CanNotConvertDateException::class);
    //     $this->assertThrows(fn () => Date::convert(date: '', to: $to, throwException: true), CanNotConvertDateException::class);
    // }

    // public function test_convert_shamsi_iran_to_hijri_oman_timezone()
    // {
    //     $from = new DateTimeZone('Asia/Tehran');
    //     $to = new DateTimeZone('Asia/Muscat');

    //     $this->assertEquals(Date::convert(date: null, from: $from, to: $to), '');
    //     $this->assertEquals(Date::convert(date: 'test', from: $from, to: $to), '');
    //     $this->assertEquals(Date::convert(date: '1402-03-19', format: 'Y-m-d', from: $from, to: $to), '1444-11-20');
    //     $this->assertEquals(Date::convert(date: '1402-03-19 19:57:10', format: 'Y-m-d H:i:s', from: $from, to: $to), '1444-11-20 20:27:10');

    //     $this->assertThrows(fn () => Date::convert(date: 'test', from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
    //     $this->assertThrows(fn () => Date::convert(date: null, from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
    //     $this->assertThrows(fn () => Date::convert(date: '', from: $from, to: $to, throwException: true), CanNotConvertDateException::class);
    // }

    // public function test_convert_hijri_oman_to_utc_timezone()
    // {
    //     $from = new DateTimeZone('Asia/Muscat');
    //     $this->assertEquals(Date::convert(date: null, from: $from), '');
    //     $this->assertEquals(Date::convert(date: 'test', from: $from), '');
    //     $this->assertEquals(Date::convert(date: '1444/11/20', format: 'Y/m/d', from: $from), '2023/06/09');
    //     $this->assertEquals(Date::convert(date: '1444/09/06', format: 'Y/m/d', from: $from), '2023/03/28');

    //     $this->assertEquals(Date::convert(date: '1444/11/20 17:57:10', format: 'Y/m/d H:i:s', from: $from), '2023/06/09 13:57:10');
    //     $this->assertEquals(Date::convert(date: '1444-11-20 17:57:05', format: 'Y-m-d H:i:s', from: $from), '2023-06-09 13:57:05');

    //     $this->assertThrows(fn () => Date::convert(date: 'test', from: $from, throwException: true), CanNotConvertDateException::class);
    //     $this->assertThrows(fn () => Date::convert(date: null, from: $from, throwException: true), CanNotConvertDateException::class);
    //     $this->assertThrows(fn () => Date::convert(date: '', from: $from, throwException: true), CanNotConvertDateException::class);
    // }

    // public function test_convert_hijri_oman_to_shamsi_iran_timezone()
    // {
    //     $from = new DateTimeZone('Asia/Muscat');
    //     $to = new DateTimeZone('Asia/Tehran');
    //     $this->assertEquals(Date::convert(date: null, from: $from, to: $to), '');
    //     $this->assertEquals(Date::convert(date: 'test', from: $from, to: $to), '');
    //     $this->assertEquals(Date::convert(date: '1444/11/20', format: 'Y/m/d', from: $from, to: $to), '1402/03/19');
    //     $this->assertEquals(Date::convert(date: '1444/09/06', format: 'Y/m/d', from: $from, to: $to), '1402/01/08');

    //     $this->assertEquals(Date::convert(date: '1444/11/20 17:57:10', format: 'Y/m/d H:i:s', from: $from, to: $to), '1402/03/19 17:27:10');
    //     $this->assertEquals(Date::convert(date: '1444-11-20 17:57:05', format: 'Y-m-d H:i:s', from: $from, to: $to), '1402-03-19 17:27:05');

    //     $this->assertThrows(fn () => Date::convert(date: 'test', from: $from, throwException: true), CanNotConvertDateException::class);
    //     $this->assertThrows(fn () => Date::convert(date: null, from: $from, throwException: true), CanNotConvertDateException::class);
    //     $this->assertThrows(fn () => Date::convert(date: '', from: $from, throwException: true), CanNotConvertDateException::class);
    // }


    public function test_convert_utc_to_gregorian_method()
    {
        $this->assertEquals(Date::convert(date: '2022-12-24 04:00:00', format: 'Y/m/d H:i:s', to: new DateTimeZone('America/New_York')), '2022/12/23 23:00:00');
        $this->assertEquals(Date::convert(date: '2022-12-24 18:15:00', format: 'Y/m/d H:i:s', to: new DateTimeZone('Asia/Tokyo')), '2022/12/25 03:15:00');
        $this->assertEquals(Date::convert(date: '2022-12-24 07:45:00', format: 'Y/m/d H:i:s', from: new DateTimeZone('America/New_York'), to: new DateTimeZone("Asia/Tokyo")), '2022/12/24 21:45:00');
    }

    public function test_convert_gregorian_to_utc_method()
    {
        $this->assertEquals(Date::convert(date: '2022-12-24 23:00:00', format: 'Y/m/d H:i:s', from: new DateTimeZone('America/New_York')), '2022/12/25 04:00:00');
        $this->assertEquals(Date::convert(date: '2022-12-24 03:15:00', format: 'Y/m/d H:i:s', from: new DateTimeZone('Asia/Tokyo')), '2022/12/23 18:15:00');
    }

    public function test_convert_shamsi_iran_to_gregorian_america()
    {
        $this->assertEquals(Date::convert(date: '1402-03-19 21:50:00', format: 'Y/m/d H:i:s', from: new DateTimeZone('Asia/Tehran'), to: new DateTimeZone('America/New_York')), '2023/06/09 14:20:00');
    }

    public function test_gregorian_validate_method()
    {
        $type = DateType::GREGORIAN;
        $this->assertTrue(Date::validate(date: '2022-12-24', type: $type));
        $this->assertTrue(Date::validate(date: '2022-12-24 13:13:11', type: $type));
        $this->assertTrue(Date::validate(date: '2022/12/24 13:13:11', type: $type));
        $this->assertTrue(Date::validate(date: '2024-02-29 12:12:11', type: $type)); // leap year
        $this->assertTrue(Date::validate(date: '2024/02/29 12:12:11', type: $type)); // leap year

        $this->assertFalse(Date::validate(date: '2022-12-24 12:12:11:test', type: $type));
        $this->assertFalse(Date::validate(date: '22-12-24 12:12:11', type: $type));
        $this->assertFalse(Date::validate(date: '2022-13-24 12:12:11', type: $type));
        $this->assertFalse(Date::validate(date: '2022-02-30 12:12:11', type: $type));
        $this->assertFalse(Date::validate(date: '2022-02-28 25:12:11', type: $type));
        $this->assertFalse(Date::validate(date: '2022-02-28 12:62:11', type: $type));
        $this->assertFalse(Date::validate(date: '2022-02-28 12:12:63', type: $type));
    }

    public function test_shamsi_validate_method()
    {
        $type = DateType::SHAMSI;
        $this->assertTrue(Date::validate(date: '1401-12-24', type: $type));
        $this->assertTrue(Date::validate(date: '1401-12-29 13:13:13', type: $type));
        $this->assertTrue(Date::validate(date: '1401-01-02 03:04:08', type: $type));
        $this->assertTrue(Date::validate(date: '1403-12-30 13:13:13', type: $type)); // leap year
        $this->assertTrue(Date::validate(date: '1403/12/30 13:13:13', type: $type)); // leap year

        $this->assertFalse(Date::validate(date: '1401/13/29 13:13:13', type: $type));
        $this->assertFalse(Date::validate(date: '1401-13-29 13:13:13', type: $type));
        $this->assertFalse(Date::validate(date: '1403-12-31 13:13:13', type: $type));
        $this->assertFalse(Date::validate(date: '1403-12-20 25:13:13', type: $type));
        $this->assertFalse(Date::validate(date: '1403-12-20 21:61:13', type: $type));
        $this->assertFalse(Date::validate(date: '1403-12-20 21:12:62', type: $type));
    }


    public function test_hijri_validate_method()
    {
        $type = DateType::HIJRI;
        $this->assertTrue(Date::validate(date: '1401-12-24', type: $type));
        $this->assertTrue(Date::validate(date: '1401-12-29 13:13:13', type: $type));
        $this->assertTrue(Date::validate(date: '1401-01-02 03:04:08', type: $type));
        $this->assertTrue(Date::validate(date: '1401/01/02 03:04:08', type: $type));

        $this->assertFalse(Date::validate(date: '1401-13-29 13:13:13', type: $type));
        $this->assertFalse(Date::validate(date: '1403-12-20 25:13:13', type: $type));
        $this->assertFalse(Date::validate(date: '1403-12-20 21:61:13', type: $type));
        $this->assertFalse(Date::validate(date: '1403-12-20 21:12:62', type: $type));
        $this->assertFalse(Date::validate(date: '1403/12/20 21:12:62', type: $type));
    }
}
