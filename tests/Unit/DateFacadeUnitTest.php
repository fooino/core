<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\CanNotConvertDateException;
use Fooino\Core\Facades\Date;
use DateTimeZone;
use Exception;

describe('Date facade using FooinoDateHandler', function () {

    test('convert method can handle exceptions and errors', function () {

        expect(Date::convert(date: null))->toBe('');
        expect(Date::convert(date: 0))->toBe('1970-01-01 00:00:00');
        expect(Date::convert(date: '0'))->toBe('1970-01-01 00:00:00');
        expect(Date::convert(date: '', fallback: 'fooino'))->toBe('fooino');
        expect(Date::convert(date: 'null', fallback: 'fooino'))->toBe('fooino');

        try {

            Date::convert(date: '2026-01-01', from: 'Asia/Fooino', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toBe('msg.canNotConvertDateExceptionInvalidTimezone');
            expect($e->getCode())->toBe(10051);

            expect($e->getLevel())->toBe('error');
            expect($e->getHttpStatusCode())->toBe(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toBe(
                [
                    "invalid_timezone"          => "Asia/Fooino",
                    "original_date"             => "2026-01-01",
                    "date"                      => "2026-01-01",
                    "format"                    => "Y-m-d H:i:s",
                    "from"                      => "Asia/Fooino",
                    "to"                        => "UTC",
                ]
            );
        };

        try {

            Date::convert(date: '2026-01-01', to: 'Asia/Fooino', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toBe('msg.canNotConvertDateExceptionInvalidTimezone');
            expect($e->getCode())->toBe(10051);

            expect($e->getLevel())->toBe('error');
            expect($e->getHttpStatusCode())->toBe(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toBe(
                [
                    "invalid_timezone"          => "Asia/Fooino",
                    "original_date"             => "2026-01-01",
                    "date"                      => "2026-01-01",
                    "format"                    => "Y-m-d H:i:s",
                    "from"                      => "UTC",
                    "to"                        => "Asia/Fooino",
                ]
            );
        };

        try {

            Date::convert(date: '', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toBe('msg.canNotConvertDateExceptionTheDateIsEmpty');
            expect($e->getCode())->toBe(10052);

            expect($e->getLevel())->toBe('warning');
            expect($e->getHttpStatusCode())->toBe(500);
            expect($e->reportable())->toBeFalse();

            expect($e->getWith())->toBe(
                [
                    "original_date"             => "",
                    "date"                      => null,
                    "format"                    => "Y-m-d H:i:s",
                    "from"                      => "UTC",
                    "to"                        => "UTC",
                ]
            );
        };
    });

    test('from UTC to Jalali', function () {

        $iranTz = 'Asia/Tehran'; // +3:30
        $afghanistanTz = 'Asia/Kabul'; // +4:30

        expect(Date::convert(date: 'test', to: $iranTz, fallback: 'fooino'))->toBe('fooino');

        expect(Date::convert(date: '2022-12-24', format: 'Y/m/d', to: $iranTz))->toBe('1401/10/03');
        expect(Date::convert(date: '2022/12/24', format: 'Y/m/d', to: $iranTz))->toBe('1401/10/03');
        expect(Date::convert(date: 1671840000,   format: 'Y/m/d', to: $iranTz))->toBe('1401/10/03');
        expect(Date::convert(date: '1671840000', format: 'Y/m/d', to: $iranTz))->toBe('1401/10/03');

        expect(Date::convert(date: '2022-12-24', format: 'Y/m/d', to: $afghanistanTz))->toBe('1401/10/03');
        expect(Date::convert(date: '2022/12/24', format: 'Y/m/d', to: $afghanistanTz))->toBe('1401/10/03');
        expect(Date::convert(date: 1671840000,   format: 'Y/m/d', to: $afghanistanTz))->toBe('1401/10/03');
        expect(Date::convert(date: '1671840000', format: 'Y/m/d', to: $afghanistanTz))->toBe('1401/10/03');

        expect(Date::convert(date: '2022/03/20', format: 'Y/m/d', to: $iranTz))->toBe('1400/12/29'); // not leap year
        expect(Date::convert(date: '2021/03/20', format: 'Y/m/d', to: $iranTz))->toBe('1399/12/30'); // leap year

        expect(Date::convert(date: '2022/03/20', format: 'Y/m/d', to: $afghanistanTz))->toBe('1400/12/29'); // not leap year
        expect(Date::convert(date: '2021/03/20', format: 'Y/m/d', to: $afghanistanTz))->toBe('1399/12/30'); // leap year

        expect(Date::convert(date: '2022-12-24 19:27:08',                   format: 'Y-m-d H:i:s',      to: $iranTz))->toBe('1401-10-03 22:57:08');
        expect(Date::convert(date: '2022-12-24 20:30',                      format: 'Y-m-d H:i:s',      to: $iranTz))->toBe('1401-10-04 00:00:00');
        expect(Date::convert(date: '2022-12-24 19',                         format: 'Y-m-d H:i:s',      to: $iranTz))->toBe('1401-10-03 03:30:00'); // it will parse to 2022-12-24 00:00:00
        expect(Date::convert(date: '2022-12-24T19:00:00',                   format: 'Y-m-d H:i:s e',    to: $iranTz))->toBe('1401-10-03 22:30:00 Asia/Tehran');
        expect(Date::convert(date: '2022-12-24 10:27:00 PM',                format: 'Y-m-d h:i:s A',    to: $iranTz))->toBe('1401-10-04 01:57:00 قبل از ظهر'); // it goes to the next day
        expect(Date::convert(date: strtotime('2022-12-24 10:27:00 PM'),     format: 'Y-m-d h:i:s A',    to: $iranTz))->toBe('1401-10-04 01:57:00 قبل از ظهر'); // it goes to the next day

        expect(Date::convert(date: '2022-12-24 19:27:08',                   format: 'Y-m-d H:i:s',      to: $afghanistanTz))->toBe('1401-10-03 23:57:08');
        expect(Date::convert(date: '2022-12-24 19:30',                      format: 'Y-m-d H:i:s',      to: $afghanistanTz))->toBe('1401-10-04 00:00:00');
        expect(Date::convert(date: '2022-12-24 19',                         format: 'Y-m-d H:i:s',      to: $afghanistanTz))->toBe('1401-10-03 04:30:00'); // it will parse to 2022-12-24 00:00:00
        expect(Date::convert(date: '2022-12-24T19:00:00',                   format: 'Y-m-d H:i:s e',    to: $afghanistanTz))->toBe('1401-10-03 23:30:00 Asia/Kabul');
        expect(Date::convert(date: '2022-12-24 10:27:00 PM',                format: 'Y-m-d h:i:s A',    to: $afghanistanTz))->toBe('1401-10-04 02:57:00 قبل از ظهر'); // it goes to the next day
        expect(Date::convert(date: strtotime('2022-12-24 10:27:00 PM'),     format: 'Y-m-d h:i:s A',    to: $afghanistanTz))->toBe('1401-10-04 02:57:00 قبل از ظهر'); // it goes to the next day

        expect(Date::convert(date: '00:00:00',  format: 'H:i:s',            to: $iranTz))->toBe('03:30:00');
        expect(Date::convert(date: '21:27:09',  format: 'h:i:s A',          to: $iranTz))->toBe('12:57:09 قبل از ظهر');
        expect(Date::convert(date: '19:27:00',  format: 'H:i:s',            to: $iranTz))->toBe('22:57:00');
        expect(Date::convert(date: '21:10',     format: 'Y-m-d H:i:s',      to: $iranTz))->toBe(Date::convert(date: strtotime('tomorrow'), format: 'Y-m-d', to: $iranTz) . ' 00:40:00'); // it goes to the next day by +3:30 iran timezone
        expect(Date::convert(date: '19',        format: 'H:i:s',            to: $iranTz))->toBe('03:30:19'); // it will parse to 1970-00-00 00:00:19

        expect(Date::convert(date: '00:00:00',  format: 'H:i:s',            to: $afghanistanTz))->toBe('04:30:00');
        expect(Date::convert(date: '21:27:09',  format: 'h:i:s A',          to: $afghanistanTz))->toBe('01:57:09 قبل از ظهر');
        expect(Date::convert(date: '19:27:00',  format: 'H:i:s',            to: $afghanistanTz))->toBe('23:57:00');
        expect(Date::convert(date: '19:10',     format: 'Y-m-d H:i:s',      to: $afghanistanTz))->toBe(Date::convert(date: date('Y-m-d'), format: 'Y-m-d', to: $afghanistanTz) . ' 23:40:00');
        expect(Date::convert(date: '19',        format: 'H:i:s',            to: $afghanistanTz))->toBe('04:30:19'); // it will parse to 1970-00-00 00:00:19

        expect(Date::convert(date: '2022-12-24 19:27:00',   format: 'j F Y ساعت H:i:s', to: $iranTz))->toBe('3 دی 1401 ساعت 22:57:00');
        expect(dateConvert(date: '2022-12-24 19:27:00',     format: 'j F Y ساعت H:i:s', to: $iranTz))->toBe('3 دی 1401 ساعت 22:57:00');

        expect(Date::convert(date: '2022-12-24 19:27:00',   format: 'j F Y ساعت H:i:s', to: $afghanistanTz))->toBe('3 دی 1401 ساعت 23:57:00');
        expect(dateConvert(date: '2022-12-24 19:27:00',     format: 'j F Y ساعت H:i:s', to: $afghanistanTz))->toBe('3 دی 1401 ساعت 23:57:00');

        try {

            Date::convert(date: 'test', to: $iranTz, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(10053);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => "Y-m-d H:i:s",
                    "from"              => "UTC",
                    "to"                => "Asia/Tehran",
                ]
            );
        };
    });

    test('from Jalali to UTC', function () {

        $iranTz = 'Asia/Tehran'; // +3:30
        $afghanistanTz = 'Asia/Kabul'; // +4:30

        expect(Date::convert(date: 'test',          format: 'Y/m/d',    from: $iranTz,      fallback: 'fooino'))->toBe('fooino');

        expect(Date::convert(date: '1401-10-03',    format: 'Y/m/d',    from: $iranTz))->toBe('2022/12/24');
        expect(Date::convert(date: '1401/10/03',    format: 'Y/m/d',    from: $iranTz))->toBe('2022/12/24');

        expect(Date::convert(date: '1401-10-03',    format: 'Y/m/d',    from: $afghanistanTz))->toBe('2022/12/24');
        expect(Date::convert(date: '1401/10/03',    format: 'Y/m/d',    from: $afghanistanTz))->toBe('2022/12/24');

        expect(Date::convert(date: '1400/12/29',    format: 'Y/m/d',    from: $iranTz))->toBe('2022/03/20'); // not leap year
        expect(Date::convert(date: '1399/12/30',    format: 'Y/m/d',    from: $iranTz))->toBe('2021/03/20'); // leap year

        expect(Date::convert(date: '1400/12/29',    format: 'Y/m/d',    from: $afghanistanTz))->toBe('2022/03/20'); // not leap year
        expect(Date::convert(date: '1399/12/30',    format: 'Y/m/d',    from: $afghanistanTz))->toBe('2021/03/20'); // leap year

        expect(Date::convert(date: '1401-10-03 22:57:09',       format: 'Y-m-d H:i:s',      from: $iranTz))->toBe('2022-12-24 19:27:09');
        expect(Date::convert(date: '1401-10-03 02:40',          format: 'Y-m-d H:i:s',      from: $iranTz))->toBe('2022-12-23 23:10:00');
        expect(Date::convert(date: '1401-10-03 04',             format: 'Y-m-d H:i:s',      from: $iranTz))->toBe('2022-12-24 00:00:00'); // it will parse to 1401-10-03 00:00:00
        expect(Date::convert(date: '1401-10-03T22:30:00',       format: 'Y-m-d H:i:s e',    from: $iranTz))->toBe('2022-12-24 19:00:00 UTC');
        expect(Date::convert(date: '1401-10-04 01:57:00 AM',    format: 'Y-m-d h:i:s A',    from: $iranTz))->toBe('2022-12-24 10:27:00 PM'); // it goes to the last day
        expect(dateConvert(date: '1401-10-03 22:57:00',         format: 'Y-m-d H:i:s',      from: $iranTz))->toBe('2022-12-24 19:27:00');

        expect(Date::convert(date: '1401-10-03 22:57:09',       format: 'Y-m-d H:i:s',      from: $afghanistanTz))->toBe('2022-12-24 18:27:09');
        expect(Date::convert(date: '1401-10-03 02:40',          format: 'Y-m-d H:i:s',      from: $afghanistanTz))->toBe('2022-12-23 22:10:00');
        expect(Date::convert(date: '1401-10-03 04',             format: 'Y-m-d H:i:s',      from: $afghanistanTz))->toBe('2022-12-24 00:00:00'); // it will parse to 1401-10-03 00:00:00
        expect(Date::convert(date: '1401-10-03T22:30:00',       format: 'Y-m-d H:i:s e',    from: $afghanistanTz))->toBe('2022-12-24 18:00:00 UTC');
        expect(Date::convert(date: '1401-10-04 01:57:00 AM',    format: 'Y-m-d h:i:s A',    from: $afghanistanTz))->toBe('2022-12-24 09:27:00 PM'); // it goes to the last day

        expect(Date::convert(date: '00:00:00',      format: 'H:i:s',            from: $iranTz))->toBe('20:30:00');
        expect(Date::convert(date: '22:57:09',      format: 'h:i:s A',          from: $iranTz))->toBe('07:27:09 PM');
        expect(Date::convert(date: '02:40',         format: 'Y-m-d H:i:s',      from: $iranTz))->toBe(date('Y-m-d') . ' 23:10:00'); // it goes to the last day by 0:00 UTC timezone
        expect(Date::convert(date: '19',            format: 'H:i:s',            from: $iranTz))->toBe('20:30:19'); // it will parse to 1970-00-00 00:00:19

        expect(Date::convert(date: '00:00:00',      format: 'H:i:s',            from: $afghanistanTz))->toBe('19:30:00');
        expect(Date::convert(date: '22:57:09',      format: 'h:i:s A',          from: $afghanistanTz))->toBe('06:27:09 PM');
        expect(Date::convert(date: '02:40',         format: 'Y-m-d H:i:s',      from: $afghanistanTz))->toBe(date('Y-m-d') . ' 22:10:00');
        expect(Date::convert(date: '19',            format: 'H:i:s',            from: $afghanistanTz))->toBe('19:30:19'); // it will parse to 1970-00-00 00:00:19

        try {

            Date::convert(date: 'test', from: $iranTz, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(10053);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => "Y-m-d H:i:s",
                    "from"              => "Asia/Tehran",
                    "to"                => "UTC",
                ]
            );
        };
    });

    test('from gregorian to UTC and jalali', function () {

        $newYorkTz = 'America/New_York'; // -4:00
        $tokyoTz = 'Asia/Tokyo'; // +9:00
        $iranTz = 'Asia/Tehran'; // +3:30
        $afghanistanTz = 'Asia/Kabul'; // +4:30
        $pacificEaster = 'Pacific/Easter'; // +18:00

        expect(Date::convert(date: 'test',                              format: 'Y/m/d H:i:s',      from: $newYorkTz, fallback: 'fooino'))->toBe('fooino');

        expect(Date::convert(date: '2026-06-01 21:00:00',               format: 'Y/m/d H:i:s',      from: $newYorkTz))->toBe('2026/06/02 01:00:00');
        expect(Date::convert(date: strtotime('2026-06-01 21:00:00'),    format: 'Y/m/d H:i:s',      from: $newYorkTz))->toBe('2026/06/02 01:00:00');
        expect(Date::convert(date: '2022-12-24 08:25:05',               format: 'Y/m/d H:i:s',      from: $newYorkTz))->toBe('2022/12/24 13:25:05'); // in some months the New-York timezone is -5:00 from UTC
        expect(Date::convert(date: '2022-12-24 21:30:00',               format: 'Y/m/d H:i:s',      from: $tokyoTz))->toBe('2022/12/24 12:30:00');
        expect(Date::convert(date: strtotime('2022-12-25 12:30:10 AM'), format: 'Y/m/d h:i:s A',    from: $tokyoTz))->toBe('2022/12/24 03:30:10 PM');

        expect(Date::convert(date: '2022-12-25 12:30:10 AM',            format: 'Y/m/d h:i:s A',    from: $tokyoTz,   to: $iranTz))->toBe('1401/10/03 07:00:10 بعد از ظهر');
        expect(Date::convert(date: '2022-12-25 12:30:10 AM',            format: 'Y/m/d H:i:s',      from: $tokyoTz,   to: $afghanistanTz))->toBe('1401/10/03 20:00:10');

        expect(Date::convert(date: '21:00:10',  format: 'Y-m-d H:i:s', from: $newYorkTz))->toBe(date('Y-m-d', strtotime('tomorrow')) . ' 01:00:10');

        expect(Date::convert(date: '19:00:00',  format: 'Y-m-d H:i:s', from: $pacificEaster,  to: $iranTz))->toBe(Date::convert(date: date('Y-m-d H:i:s'), format: 'Y-m-d', to: $iranTz) . ' 04:30:00');

        try {

            Date::convert(date: 'test', from: $tokyoTz, to: $iranTz, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(10053);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => "Y-m-d H:i:s",
                    "from"              => "Asia/Tokyo",
                    "to"                => "Asia/Tehran",
                ]
            );
        };
    });

    test('from UTC and jalali to gregorian', function () {

        $newYorkTz = 'America/New_York'; // -4:00
        $tokyoTz = 'Asia/Tokyo'; // +9:00
        $iranTz = 'Asia/Tehran'; // +3:30
        $afghanistanTz = 'Asia/Kabul'; // +4:30

        expect(Date::convert(date: 'test',                              format: 'Y/m/d H:i:s',      to: $newYorkTz, fallback: 'fooino'))->toBe('fooino');

        expect(Date::convert(date: '2026/06/02 01:00:00',               format: 'Y/m/d H:i:s',      to: $newYorkTz))->toBe('2026/06/01 21:00:00');
        expect(Date::convert(date: strtotime('2026/06/02 01:00:00'),    format: 'Y/m/d H:i:s',      to: $newYorkTz))->toBe('2026/06/01 21:00:00');
        expect(Date::convert(date: '2022/12/24 13:25:05',               format: 'Y/m/d H:i:s',      to: $newYorkTz))->toBe('2022/12/24 08:25:05'); // in some months the New-York timezone is -5:00 from UTC
        expect(Date::convert(date: '2022/12/24 12:30:00',               format: 'Y/m/d H:i:s',      to: $tokyoTz))->toBe('2022/12/24 21:30:00');
        expect(Date::convert(date: strtotime('2022/12/24 03:30:10 PM'), format: 'Y/m/d h:i:s A',    to: $tokyoTz))->toBe('2022/12/25 12:30:10 AM');

        expect(Date::convert(date: '1401/10/03 07:00:10 PM',            format: 'Y/m/d h:i:s A',    from: $iranTz, to: $tokyoTz))->toBe('2022/12/25 12:30:10 AM');
        expect(Date::convert(date: '1401/10/03 20:00:10',               format: 'Y/m/d H:i:s',      from: $afghanistanTz, to: $tokyoTz))->toBe('2022/12/25 00:30:10');

        try {
            Date::convert(date: 'test', from: $iranTz, to: $tokyoTz, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(10053);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => "Y-m-d H:i:s",
                    "from"              => "Asia/Tehran",
                    "to"                => "Asia/Tokyo",
                ]
            );
        };
    });

    test('validateTimezone method', function () {

        expect(Date::getTimezones())->toEqual(DateTimeZone::listIdentifiers());

        expect(Date::validateTimezone('Asia/Tehran'))->toBeTrue();
        expect(Date::validateTimezone('Asia/Fooino'))->toBeFalse();
        expect(Date::validateTimezone('asia/tehran'))->toBeFalse();
        expect(Date::validateTimezone('Asia/Tehran'))->toBeTrue();
    });
});
