<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\CanNotConvertDateException;
use Fooino\Core\Facades\Date;
use DateTimeZone;
use Exception;

describe('Date facade using FooinoDateHandler', function () {

    test('convert method can handle exceptions and errors', function () {

        $iranTz = 'Asia/Tehran';

        expect(Date::convert(date: null))->toEqual('');
        expect(Date::convert(date: '', fallback: 'fooino'))->toEqual('fooino');
        expect(Date::convert(date: 'null', fallback: 'fooino'))->toEqual('fooino');

        try {

            Date::convert(date: '2026-01-01', from: 'Asia/Fooino', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionInvalidTimezone');
            expect($e->getCode())->toEqual(10051);

            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "invalid_timezone"  => "Asia/Fooino",
                    "date"              => "2026-01-01",
                    "format"            => "Y-m-d H:i:s",
                    "from"              => "Asia/Fooino",
                    "to"                => "UTC",
                ]
            );
        };

        try {

            Date::convert(date: '', throwException: true);

            // 
        } catch (CanNotConvertDateException | Exception $e) {

            expect($e->getMessage())->toEqual('msg.canNotConvertDateExceptionTheDateIsEmpty');
            expect($e->getCode())->toEqual(10052);

            expect($e->getLevel())->toEqual('warning');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect($e->reportable())->toBeTrue();

            expect($e->getWith())->toEqual(
                [
                    "date"              => "",
                    "format"            => "Y-m-d H:i:s",
                    "from"              => "UTC",
                    "to"                => "UTC",
                ]
            );
        };

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
                    "date"              => "test",
                    "format"            => "Y-m-d H:i:s",
                    "from"              => "Asia/Tehran",
                    "to"                => "UTC",
                ]
            );
        };
    });

    test('convert method change date from a timezone to other timezone', function () {

        $utc = 'UTC';
        $iranTz = 'Asia/Tehran';

        expect(Date::convert(date: 'test', to: $iranTz, fallback: 'fooino'))->toEqual('fooino');
        expect(Date::convert(date: '2022-12-24', format: 'Y/m/d', to: $iranTz))->toEqual('1401/10/03');
        expect(Date::convert(date: '2022/12/24', format: 'Y/m/d', to: $iranTz))->toEqual('1401/10/03');
        expect(Date::convert(date: '2021/03/20', format: 'Y/m/d', to: $iranTz))->toEqual('1399/12/30');
        expect(Date::convert(date: '2022-12-24 19:27:00', to: $iranTz))->toEqual('1401-10-03 22:57:00');
        expect(Date::convert(date: '2022-12-24 19:10', to: $iranTz))->toEqual('1401-10-03 22:40:00');
        expect(Date::convert(date: '2022-12-24 19', to: $iranTz))->toEqual('1401-10-03 03:30:00');
        expect(Date::convert(date: '2022-12-24 time 19:27:00', to: $iranTz))->toEqual('1401-10-03 22:57:00');
        expect(Date::convert(date: '2022-12-24 19:27:00', format: 'j F Y ساعت H:i:s', to: $iranTz))->toEqual('3 دی 1401 ساعت 22:57:00');
        expect(dateConvert(date: '2022-12-24 19:27:00', format: 'j F Y ساعت H:i:s', to: $iranTz))->toEqual('3 دی 1401 ساعت 22:57:00');

        expect(Date::convert(date: null, from: $iranTz))->toEqual('');
        expect(Date::convert(date: 'test', from: $iranTz))->toEqual('');
        expect(Date::convert(date: '1401/10/03', format: 'Y/m/d', from: $iranTz))->toEqual('2022/12/24');
        expect(Date::convert(date: '1401/10/03', format: 'Y-m-d', from: $iranTz))->toEqual('2022-12-24');
        expect(Date::convert(date: '1399/12/30', format: 'Y/m/d', from: $iranTz))->toEqual('2021/03/20');
        expect(Date::convert(date: '1401/10/03 22:57:00', from: $iranTz))->toEqual('2022-12-24 19:27:00');
        expect(Date::convert(date: '1401/10/03', from: $iranTz))->toEqual('2022-12-24 00:00:00');
        expect(Date::convert(date: '1401/10/03 22:57:00', format: 'j F Y H:i:s', from: $iranTz))->toEqual('24 December 2022 19:27:00');
        expect(Date::convert(date: '1401/10/03', format: 'j F Y', from: $iranTz))->toEqual('24 December 2022');
        expect(Date::convert(date: '1401-10/03 ساعت 22:57:00', from: $iranTz))->toEqual('2022-12-24 19:27:00');
        expect(dateConvert(date: '1401-10/03 ساعت 22:57:00', from: $iranTz))->toEqual('2022-12-24 19:27:00');
    });


    test('validateTimezone method', function () {

        expect(Date::getTimezones())->toEqual(DateTimeZone::listIdentifiers());

        expect(Date::validateTimezone('Asia/Tehran'))->toBeTrue();
        expect(Date::validateTimezone('Asia/Fooino'))->toBeFalse();
        expect(Date::validateTimezone('Asia/Tehran'))->toBeTrue();
    });
});
