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

        expect(fn() => Date::convert(date: '2026-01-01', from: 'Asia/Fooino', throwException: true))->toThrow(CanNotConvertDateException::class);

        try {

            Date::convert(date: '2026-01-01', from: 'Asia/Fooino', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toBe('msg.canNotConvertDateExceptionInvalidTimezone');
            expect($e->getCode())->toBe(1001);

            expect($e->getLevel())->toBe('error');
            expect($e->getHttpStatusCode())->toBe(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toBe(
                [
                    "invalid_timezone"          => "Asia/Fooino",
                    "original_date"             => "2026-01-01",
                    "date"                      => "2026-01-01",
                    "format"                    => STANDARD_DATE_TIME_FORMAT,
                    "from"                      => "Asia/Fooino",
                    "to"                        => "UTC",
                ]
            );
        };

        expect(fn() => Date::convert(date: '2026-01-01', to: 'Asia/Fooino', throwException: true))->toThrow(CanNotConvertDateException::class);

        try {

            Date::convert(date: '2026-01-01', to: 'Asia/Fooino', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toBe('msg.canNotConvertDateExceptionInvalidTimezone');
            expect($e->getCode())->toBe(1001);

            expect($e->getLevel())->toBe('error');
            expect($e->getHttpStatusCode())->toBe(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toBe(
                [
                    "invalid_timezone"          => "Asia/Fooino",
                    "original_date"             => "2026-01-01",
                    "date"                      => "2026-01-01",
                    "format"                    => STANDARD_DATE_TIME_FORMAT,
                    "from"                      => "UTC",
                    "to"                        => "Asia/Fooino",
                ]
            );
        };

        expect(fn() => Date::convert(date: '', throwException: true))->toThrow(CanNotConvertDateException::class);

        try {

            Date::convert(date: '', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toBe('msg.canNotConvertDateExceptionDateIsEmpty');
            expect($e->getCode())->toBe(1002);

            expect($e->getLevel())->toBe('warning');
            expect($e->getHttpStatusCode())->toBe(500);
            expect($e->reportable())->toBeFalse();

            expect($e->getWith())->toBe(
                [
                    "original_date"             => "",
                    "date"                      => null,
                    "format"                    => STANDARD_DATE_TIME_FORMAT,
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

        expect(Date::convert(date: '2022-12-24 19:27:08',                           format: STANDARD_DATE_TIME_FORMAT,      to: $iranTz))->toBe('1401-10-03 22:57:08');
        expect(Date::convert(date: '2022-12-24 20:30',                              format: STANDARD_DATE_TIME_FORMAT,      to: $iranTz))->toBe('1401-10-04 00:00:00');
        expect(Date::convert(date: '2022-12-24 19',                                 format: STANDARD_DATE_TIME_FORMAT,      to: $iranTz))->toBe('1401-10-03 03:30:00'); // it will parse to 2022-12-24 00:00:00
        expect(Date::convert(date: '2022-12-24T19:00:00',                           format: 'Y-m-d H:i:s e',                to: $iranTz))->toBe('1401-10-03 22:30:00 Asia/Tehran');
        expect(Date::convert(date: '2022-12-24 10:27:00 PM',                        format: 'Y-m-d h:i:s A',                to: $iranTz))->toBe('1401-10-04 01:57:00 قبل از ظهر'); // it goes to the next day
        expect(Date::officialCalendar()->convert(date: '2022-12-24 10:27:00 PM',    format: 'Y-m-d h:i:s A',                to: $iranTz))->toBe('1401-10-04 01:57:00 قبل از ظهر'); // it goes to the next day
        expect(Date::unofficialCalendar()->convert(date: '2022-12-24 10:27:00 PM',  format: 'Y-m-d h:i:s A',                to: $iranTz))->toBe('1401-10-04 01:57:00 قبل از ظهر'); // it goes to the next day
        expect(Date::convert(date: strtotime('2022-12-24 10:27:00 PM'),             format: 'Y-m-d h:i:s A',                to: $iranTz))->toBe('1401-10-04 01:57:00 قبل از ظهر'); // it goes to the next day

        expect(Date::convert(date: '2022-12-24 19:27:08',                           format: STANDARD_DATE_TIME_FORMAT,      to: $afghanistanTz))->toBe('1401-10-03 23:57:08');
        expect(Date::convert(date: '2022-12-24 19:30',                              format: STANDARD_DATE_TIME_FORMAT,      to: $afghanistanTz))->toBe('1401-10-04 00:00:00');
        expect(Date::convert(date: '2022-12-24 19',                                 format: STANDARD_DATE_TIME_FORMAT,      to: $afghanistanTz))->toBe('1401-10-03 04:30:00'); // it will parse to 2022-12-24 00:00:00
        expect(Date::convert(date: '2022-12-24T19:00:00',                           format: 'Y-m-d H:i:s e',                to: $afghanistanTz))->toBe('1401-10-03 23:30:00 Asia/Kabul');
        expect(Date::convert(date: '2022-12-24 10:27:00 PM',                        format: 'Y-m-d h:i:s A',                to: $afghanistanTz))->toBe('1401-10-04 02:57:00 قبل از ظهر'); // it goes to the next day
        expect(Date::officialCalendar()->convert(date: '2022-12-24 10:27:00 PM',    format: 'Y-m-d h:i:s A',                to: $afghanistanTz))->toBe('1401-10-04 02:57:00 قبل از ظهر'); // it goes to the next day
        expect(Date::unofficialCalendar()->convert(date: '2022-12-24 10:27:00 PM',  format: 'Y-m-d h:i:s A',                to: $afghanistanTz))->toBe('1401-10-04 02:57:00 قبل از ظهر'); // it goes to the next day
        expect(Date::convert(date: strtotime('2022-12-24 10:27:00 PM'),             format: 'Y-m-d h:i:s A',                to: $afghanistanTz))->toBe('1401-10-04 02:57:00 قبل از ظهر'); // it goes to the next day

        expect(Date::convert(date: '00:00:00',  format: 'H:i:s',                        to: $iranTz))->toBe('03:30:00');
        expect(Date::convert(date: '21:27:09',  format: 'h:i:s A',                      to: $iranTz))->toBe('12:57:09 قبل از ظهر');
        expect(Date::convert(date: '19:27:00',  format: 'H:i:s',                        to: $iranTz))->toBe('22:57:00');
        expect(Date::convert(date: '21:10',     format: STANDARD_DATE_TIME_FORMAT,      to: $iranTz))->toBe(Date::convert(date: strtotime('tomorrow'), format: STANDARD_DATE_FORMAT, to: $iranTz) . ' 00:40:00'); // it goes to the next day by +3:30 iran timezone
        expect(Date::convert(date: '19',        format: 'H:i:s',                        to: $iranTz))->toBe('03:30:19'); // it will parse to 1970-00-00 00:00:19

        expect(Date::convert(date: '00:00:00',  format: 'H:i:s',                        to: $afghanistanTz))->toBe('04:30:00');
        expect(Date::convert(date: '21:27:09',  format: 'h:i:s A',                      to: $afghanistanTz))->toBe('01:57:09 قبل از ظهر');
        expect(Date::convert(date: '19:27:00',  format: 'H:i:s',                        to: $afghanistanTz))->toBe('23:57:00');
        expect(Date::convert(date: '19:10',     format: STANDARD_DATE_TIME_FORMAT,      to: $afghanistanTz))->toBe(Date::convert(date: date(STANDARD_DATE_FORMAT), format: STANDARD_DATE_FORMAT, to: $afghanistanTz) . ' 23:40:00');
        expect(Date::convert(date: '19',        format: 'H:i:s',                        to: $afghanistanTz))->toBe('04:30:19'); // it will parse to 1970-00-00 00:00:19

        expect(Date::convert(date: '2022-12-24 19:27:00',   format: 'j F Y ساعت H:i:s', to: $iranTz))->toBe('3 دی 1401 ساعت 22:57:00');
        expect(dateConvert(date: '2022-12-24 19:27:00',     format: 'j F Y ساعت H:i:s', to: $iranTz))->toBe('3 دی 1401 ساعت 22:57:00');

        expect(Date::convert(date: '2022-12-24 19:27:00',   format: 'j F Y ساعت H:i:s', to: $afghanistanTz))->toBe('3 دی 1401 ساعت 23:57:00');
        expect(dateConvert(date: '2022-12-24 19:27:00',     format: 'j F Y ساعت H:i:s', to: $afghanistanTz))->toBe('3 دی 1401 ساعت 23:57:00');


        expect(fn() => Date::convert(date: 'test', to: $iranTz, throwException: true))->toThrow(CanNotConvertDateException::class);

        try {

            Date::convert(date: 'test', to: $iranTz, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(1003);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => STANDARD_DATE_TIME_FORMAT,
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

        expect(Date::convert(date: '1401-10-03 22:57:09',                       format: STANDARD_DATE_TIME_FORMAT,      from: $iranTz))->toBe('2022-12-24 19:27:09');
        expect(Date::convert(date: '1401-10-03 02:40',                          format: STANDARD_DATE_TIME_FORMAT,      from: $iranTz))->toBe('2022-12-23 23:10:00');
        expect(Date::convert(date: '1401-10-03 04',                             format: STANDARD_DATE_TIME_FORMAT,      from: $iranTz))->toBe('2022-12-24 00:00:00'); // it will parse to 1401-10-03 00:00:00
        expect(Date::convert(date: '1401-10-03T22:30:00',                       format: 'Y-m-d H:i:s e',    from: $iranTz))->toBe('2022-12-24 19:00:00 UTC');
        expect(Date::officialCalendar()->convert(date: '1401-10-03T22:30:00',   format: 'Y-m-d H:i:s e',    from: $iranTz))->toBe('2022-12-24 19:00:00 UTC');
        expect(Date::unofficialCalendar()->convert(date: '1401-10-03T22:30:00', format: 'Y-m-d H:i:s e',    from: $iranTz))->toBe('2022-12-24 19:00:00 UTC');
        expect(Date::convert(date: '1401-10-04 01:57:00 AM',                    format: 'Y-m-d h:i:s A',    from: $iranTz))->toBe('2022-12-24 10:27:00 PM'); // it goes to the last day
        expect(dateConvert(date: '1401-10-03 22:57:00',                         format: STANDARD_DATE_TIME_FORMAT,      from: $iranTz))->toBe('2022-12-24 19:27:00');

        expect(Date::convert(date: '1401-10-03 22:57:09',                       format: STANDARD_DATE_TIME_FORMAT,      from: $afghanistanTz))->toBe('2022-12-24 18:27:09');
        expect(Date::convert(date: '1401-10-03 02:40',                          format: STANDARD_DATE_TIME_FORMAT,      from: $afghanistanTz))->toBe('2022-12-23 22:10:00');
        expect(Date::convert(date: '1401-10-03 04',                             format: STANDARD_DATE_TIME_FORMAT,      from: $afghanistanTz))->toBe('2022-12-24 00:00:00'); // it will parse to 1401-10-03 00:00:00
        expect(Date::officialCalendar()->convert(date: '1401-10-03T22:30:00',   format: 'Y-m-d H:i:s e',    from: $afghanistanTz))->toBe('2022-12-24 18:00:00 UTC');
        expect(Date::unofficialCalendar()->convert(date: '1401-10-03T22:30:00',   format: 'Y-m-d H:i:s e',  from: $afghanistanTz))->toBe('2022-12-24 18:00:00 UTC');
        expect(Date::convert(date: '1401-10-03T22:30:00',                       format: 'Y-m-d H:i:s e',    from: $afghanistanTz))->toBe('2022-12-24 18:00:00 UTC');
        expect(Date::convert(date: '1401-10-04 01:57:00 AM',                    format: 'Y-m-d h:i:s A',    from: $afghanistanTz))->toBe('2022-12-24 09:27:00 PM'); // it goes to the last day

        expect(Date::convert(date: '00:00:00',      format: 'H:i:s',            from: $iranTz))->toBe('20:30:00');
        expect(Date::convert(date: '22:57:09',      format: 'h:i:s A',          from: $iranTz))->toBe('07:27:09 PM');
        expect(Date::convert(date: '02:40',         format: STANDARD_DATE_TIME_FORMAT,      from: $iranTz))->toBeIn([
            Date::convert(date: explode(" ", Date::convert(date: date(STANDARD_DATE_TIME_FORMAT), to: $iranTz))[0], format: STANDARD_DATE_FORMAT, from: $iranTz) . ' 23:10:00',
            Date::convert(date: explode(" ", Date::convert(date: date(STANDARD_DATE_TIME_FORMAT, strtotime('yesterday')), to: $iranTz))[0], format: STANDARD_DATE_FORMAT, from: $iranTz) . ' 23:10:00',
        ]);
        expect(Date::convert(date: '19',            format: 'H:i:s',            from: $iranTz))->toBe('20:30:19'); // it will parse to 1970-00-00 00:00:19

        expect(Date::convert(date: '00:00:00',      format: 'H:i:s',            from: $afghanistanTz))->toBe('19:30:00');
        expect(Date::convert(date: '22:57:09',      format: 'h:i:s A',          from: $afghanistanTz))->toBe('06:27:09 PM');
        expect(Date::convert(date: '15:40',         format: STANDARD_DATE_TIME_FORMAT,      from: $afghanistanTz))->toBe(Date::convert(date: explode(" ", Date::convert(date: date(STANDARD_DATE_TIME_FORMAT), to: $afghanistanTz))[0], format: STANDARD_DATE_FORMAT, from: $afghanistanTz) . ' 11:10:00');
        expect(Date::convert(date: '19',            format: 'H:i:s',            from: $afghanistanTz))->toBe('19:30:19'); // it will parse to 1970-00-00 00:00:19

        try {

            Date::convert(date: 'test', from: $iranTz, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(1003);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => STANDARD_DATE_TIME_FORMAT,
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

        expect(Date::convert(date: 'test',                                                  format: 'Y/m/d H:i:s',      from: $newYorkTz, fallback: 'fooino'))->toBe('fooino');

        expect(Date::convert(date: '2026-06-01 21:00:00',                                   format: 'Y/m/d H:i:s',      from: $newYorkTz))->toBe('2026/06/02 01:00:00');
        expect(Date::convert(date: strtotime('2026-06-01 21:00:00'),                        format: 'Y/m/d H:i:s',      from: $newYorkTz))->toBe('2026/06/02 01:00:00');
        expect(Date::convert(date: '2022-12-24 08:25:05',                                   format: 'Y/m/d H:i:s',      from: $newYorkTz))->toBe('2022/12/24 13:25:05'); // in some months the New-York timezone is -5:00 from UTC
        expect(Date::convert(date: '2022-12-24 21:30:00',                                   format: 'Y/m/d H:i:s',      from: $tokyoTz))->toBe('2022/12/24 12:30:00');
        expect(Date::officialCalendar()->convert(date: '2022-12-24 21:30:00',               format: 'Y/m/d H:i:s',      from: $tokyoTz))->toBe('2022/12/24 12:30:00');
        expect(Date::unofficialCalendar()->convert(date: '2022-12-24 21:30:00',             format: 'Y/m/d H:i:s',      from: $tokyoTz))->toBe('2022/12/24 12:30:00');
        expect(Date::convert(date: strtotime('2022-12-25 12:30:10 AM'),                     format: 'Y/m/d h:i:s A',    from: $tokyoTz))->toBe('2022/12/24 03:30:10 PM');

        expect(Date::convert(date: '2022-12-25 00:30:10',                                   format: 'Y/m/d h:i:s A',    from: $tokyoTz,   to: $iranTz))->toBe('1401/10/03 07:00:10 بعد از ظهر'); // 2022-12-25 00:30:10 Tokyo ---> 2022-12-24 15:30:10 UTC ---> 1401-10-03 19:00:10 Tehran
        expect(Date::convert(date: '2022-12-25 00:30:10',                                   format: 'Y/m/d H:i:s',      from: $tokyoTz,   to: $afghanistanTz))->toBe('1401/10/03 20:00:10');


        expect(Date::convert(date: '00:00:00',  format: 'H:i:s',        from: $newYorkTz))->toBeIn(['04:00:00', '05:00:00']);
        expect(Date::convert(date: '15:30:00',  format: 'H:i:s',        from: $newYorkTz))->toBeIn(['19:30:00', '20:30:00']);
        expect(Date::convert(date: '15:30',     format: 'H:i:s',        from: $newYorkTz))->toBeIn(['19:30:00', '20:30:00']);
        expect(Date::convert(date: '15',        format: 'H:i:s',        from: $newYorkTz))->toBeIn(['04:00:15', '05:00:15']);
        expect(Date::convert(date: '21:00:10',  format: STANDARD_DATE_TIME_FORMAT,  from: $newYorkTz))->toBeIn([
            date(STANDARD_DATE_FORMAT, strtotime('tomorrow')) . ' 01:00:10',
            date(STANDARD_DATE_FORMAT, strtotime('tomorrow')) . ' 02:00:10',
            date(STANDARD_DATE_FORMAT, strtotime('+2 days')) . ' 01:00:10',
            date(STANDARD_DATE_FORMAT, strtotime('+2 days')) . ' 02:00:10',
        ]);
        expect(Date::convert(date: '15:30',     format: 'H:i:s',        from: $newYorkTz, to: $iranTz))->toBeIn(['23:00:00', '00:00:00']);

        try {

            Date::convert(date: 'test', from: $tokyoTz, to: $iranTz, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(1003);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => STANDARD_DATE_TIME_FORMAT,
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

        expect(Date::convert(date: 'test',                                              format: 'Y/m/d H:i:s',      to: $newYorkTz, fallback: 'fooino'))->toBe('fooino');

        expect(Date::convert(date: '2026/06/02 01:00:00',                               format: 'Y/m/d H:i:s',      to: $newYorkTz))->toBe('2026/06/01 21:00:00');
        expect(Date::convert(date: strtotime('2026/06/02 01:00:00'),                    format: 'Y/m/d H:i:s',      to: $newYorkTz))->toBe('2026/06/01 21:00:00');
        expect(Date::convert(date: '2022/12/24 13:25:05',                               format: 'Y/m/d H:i:s',      to: $newYorkTz))->toBe('2022/12/24 08:25:05'); // in some months the New-York timezone is -5:00 from UTC
        expect(Date::convert(date: '2022/12/24 12:30:00',                               format: 'Y/m/d H:i:s',      to: $tokyoTz))->toBe('2022/12/24 21:30:00');
        expect(Date::officialCalendar()->convert(date: '2022/12/24 12:30:00',           format: 'Y/m/d H:i:s',      to: $tokyoTz))->toBe('2022/12/24 21:30:00');
        expect(Date::unofficialCalendar()->convert(date: '2022/12/24 12:30:00',         format: 'Y/m/d H:i:s',      to: $tokyoTz))->toBe('2022/12/24 21:30:00');
        expect(Date::convert(date: strtotime('2022/12/24 03:30:10 PM'),                 format: 'Y/m/d h:i:s A',    to: $tokyoTz))->toBe('2022/12/25 12:30:10 AM');

        expect(Date::convert(date: '1401/10/03 19:00:10',                               format: 'Y/m/d h:i:s A',    from: $iranTz,          to: $tokyoTz))->toBe('2022/12/25 12:30:10 AM'); // 1401-10-03 19:00:10 Tehran ---> 2022-12-24 15:30:10 UTC ---> 2022-12-25 00:30:10 Tokyo
        expect(Date::convert(date: '1401/10/03 20:00:10',                               format: 'Y/m/d H:i:s',      from: $afghanistanTz,   to: $tokyoTz))->toBe('2022/12/25 00:30:10');

        expect(Date::convert(date: '00:00:00',  format: 'H:i:s',        to: $newYorkTz))->toBeIn(['20:00:00', '19:00:00']);
        expect(Date::convert(date: '15:30:00',  format: 'H:i:s',        to: $newYorkTz))->toBeIn(['11:30:00', '10:30:00']);
        expect(Date::convert(date: '15:30',     format: 'H:i:s',        to: $newYorkTz))->toBeIn(['11:30:00', '10:30:00']);
        expect(Date::convert(date: '15',        format: 'H:i:s',        to: $newYorkTz))->toBeIn(['20:00:15', '19:00:15']);
        expect(Date::convert(date: '02:00:10',  format: STANDARD_DATE_TIME_FORMAT,  to: $newYorkTz))->toBeIn([date(STANDARD_DATE_FORMAT, strtotime('yesterday')) . ' 22:00:10', date(STANDARD_DATE_FORMAT, strtotime('yesterday')) . ' 21:00:10']);
        expect(Date::convert(date: '15:30',     format: 'H:i:s',        from: $iranTz, to: $newYorkTz))->toBeIn(['08:00:00', '07:00:00']);

        try {

            Date::convert(date: 'test', from: $iranTz, to: $tokyoTz, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(1003);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => STANDARD_DATE_TIME_FORMAT,
                    "from"              => "Asia/Tehran",
                    "to"                => "Asia/Tokyo",
                ]
            );
        };
    });

    test('UTCToUTC change the format', function () {


        expect(Date::convert(date: '2024-01-10 15:20',              format: 'Y-m-d h:i:s A'))->toBe('2024-01-10 03:20:00 PM');
        expect(Date::convert(date: strtotime('2024-01-10 15:20'),   format: 'Y-m-d h:i:s A'))->toBe('2024-01-10 03:20:00 PM');

        try {

            Date::convert(date: 'test', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(1003);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => STANDARD_DATE_TIME_FORMAT,
                    "from"              => "UTC",
                    "to"                => "UTC",
                ]
            );
        };
    });

    test('from UTC to hijri', function () {

        $UAE = 'Asia/Dubai'; // +4:00
        $riyadh = 'Asia/Riyadh'; // +3:00

        expect(Date::unofficialCalendar()->convert(date: 'test', to: $UAE, fallback: 'fooino'))->toBe('fooino');

        expect(Date::unofficialCalendar()->convert(date: '2026-06-02',              format: STANDARD_DATE_FORMAT,            to: $UAE))->toBe('1447-12-16');
        expect(Date::unofficialCalendar()->convert(date: '2026-06-02',              format: 'Y/m/d',            to: $riyadh))->toBe('1447/12/16');
        expect(Date::unofficialCalendar()->convert(date: strtotime('2026-06-02'),   format: 'Y/m/d H:i:s',      to: $riyadh))->toBe('1447/12/16 03:00:00');

        expect(Date::officialCalendar()->convert(date: '2026-06-02 12:30:08',       format: STANDARD_DATE_TIME_FORMAT,      to: $UAE))->toBe('2026-06-02 16:30:08');
        expect(Date::unofficialCalendar()->convert(date: '2026-06-02 12:30:08',     format: STANDARD_DATE_TIME_FORMAT,      to: $UAE))->toBe('1447-12-16 16:30:08');
        expect(Date::unofficialCalendar()->convert(date: '2026-06-02 10:30:08 PM',  format: 'Y-m-d h:i:s A',    to: $UAE))->toBe('1447-12-17 02:30:08 AM');
        expect(Date::unofficialCalendar()->convert(date: '2026-06-02 12:30:08',     format: 'Y/m/d H:i:s',      to: $riyadh))->toBe('1447/12/16 15:30:08');
        expect(Date::unofficialCalendar()->convert(date: '2026-06-02 21:00:00',     format: 'Y/m/d h:i:s A e',  to: $riyadh))->toBe('1447/12/17 12:00:00 AM Asia/Riyadh');

        expect(Date::unofficialCalendar()->convert(date: '00:00:00',                format: 'H:i:s',            to: $riyadh))->toBe('03:00:00');
        expect(Date::unofficialCalendar()->convert(date: '22:00:00',                format: 'h:i:s A',          to: $riyadh))->toBe('01:00:00 AM');
        expect(Date::unofficialCalendar()->convert(date: '19:38',                   format: 'H:i:s',            to: $riyadh))->toBe('22:38:00');
        expect(Date::unofficialCalendar()->convert(date: '19',                      format: 'H:i:s',            to: $riyadh))->toBe('03:00:19');
        expect(Date::unofficialCalendar()->convert(date: '19:38',                   format: 'H:i:s',            to: $UAE))->toBe('23:38:00');

        try {

            Date::unofficialCalendar()->convert(date: 'test', to: $UAE, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(1003);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => STANDARD_DATE_TIME_FORMAT,
                    "from"              => "UTC",
                    "to"                => "Asia/Dubai",
                ]
            );
        };
    });

    test('from hijri to UTC', function () {

        $UAE = 'Asia/Dubai'; // +4:00
        $riyadh = 'Asia/Riyadh'; // +3:00

        expect(Date::unofficialCalendar()->convert(date: 'test', from: $UAE, fallback: 'fooino'))->toBe('fooino');

        expect(Date::unofficialCalendar()->convert(date: '1447-12-16',              format: STANDARD_DATE_FORMAT,            from: $UAE))->toBe('2026-06-01');
        expect(Date::unofficialCalendar()->convert(date: '1447/12/16',              format: 'Y/m/d',            from: $riyadh))->toBe('2026/06/01');

        expect(Date::officialCalendar()->convert(date: '2026-06-02 16:30:08',       format: STANDARD_DATE_TIME_FORMAT,      from: $UAE))->toBe('2026-06-02 12:30:08');
        expect(Date::unofficialCalendar()->convert(date: '1447-12-16 16:30:08',     format: STANDARD_DATE_TIME_FORMAT,      from: $UAE))->toBe('2026-06-02 12:30:08');
        expect(Date::unofficialCalendar()->convert(date: '1447-12-17 02:30:08 AM',  format: 'Y-m-d h:i:s A',    from: $UAE))->toBe('2026-06-02 10:30:08 PM');
        expect(Date::unofficialCalendar()->convert(date: '1447/12/16 15:30:08',     format: 'Y/m/d H:i:s',      from: $riyadh))->toBe('2026/06/02 12:30:08');
        expect(Date::unofficialCalendar()->convert(date: '1447/12/17 12:00:00 AM',  format: 'Y/m/d h:i:s A e',  from: $riyadh))->toBe('2026/06/02 09:00:00 PM UTC');

        expect(Date::unofficialCalendar()->convert(date: '00:00:00',                format: 'H:i:s',            from: $riyadh))->toBe('21:00:00');
        expect(Date::unofficialCalendar()->convert(date: '02:00:00',                format: 'h:i:s A',          from: $riyadh))->toBe('11:00:00 PM');
        expect(Date::unofficialCalendar()->convert(date: '19:38',                   format: 'H:i:s',            from: $riyadh))->toBe('16:38:00');
        expect(Date::unofficialCalendar()->convert(date: '19',                      format: 'H:i:s',            from: $riyadh))->toBe('21:00:19');
        expect(Date::unofficialCalendar()->convert(date: '19:38',                   format: 'H:i:s',            from: $UAE))->toBe('15:38:00');

        try {

            Date::unofficialCalendar()->convert(date: 'test', from: $UAE, throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidDate');
            expect($e->getCode())->toEqual(1003);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "original_date"     => "test",
                    "date"              => "test",
                    "format"            => STANDARD_DATE_TIME_FORMAT,
                    "from"              => "Asia/Dubai",
                    "to"                => "UTC",
                ]
            );
        };
    });

    test('validateTimezone method', function () {

        expect(Date::getTimezones())->toEqual(DateTimeZone::listIdentifiers());

        expect(Date::validateTimezone(timezone: 'Asia/Tehran'))->toBeTrue();
        expect(Date::validateTimezone(timezone: 'Asia/Fooino'))->toBeFalse();
        expect(Date::validateTimezone(timezone: 'asia/tehran'))->toBeFalse();
        expect(Date::validateTimezone(timezone: 'Asia/Tehran'))->toBeTrue();
    });
});
