<?php

namespace Fooino\Core\Tests\Unit;

use stdClass;
use Stringable;

class CustomClass
{
    public function pi()
    {
        return 3.14;
    }

    public function abs(int $a)
    {
        return abs($a);
    }
};

describe('Helpers unit tests', function () {

    test('nullIfBlank returns value when it is filled', function () {

        expect(nullIfBlank(value: 0, fallback: 'fooino'))->toEqual(0);
        expect(nullIfBlank(value: 5, fallback: 'fooino'))->toEqual(5);
        expect(nullIfBlank(value: -5.5, fallback: 'fooino'))->toEqual(-5.5);

        expect(nullIfBlank(value: true, fallback: 'fooino'))->toEqual(true);
        expect(nullIfBlank(value: false, fallback: 'fooino'))->toEqual(false);

        expect(nullIfBlank(value: '0', fallback: 'fooino'))->toEqual('0');
        expect(nullIfBlank(value: '0.0', fallback: 'fooino'))->toEqual('0.0');
        expect(nullIfBlank(value: ' foobar ', fallback: 'fooino'))->toEqual(' foobar ');

        expect(nullIfBlank(value: [1, 'foobar', true], fallback: 'fooino'))->toEqual([1, 'foobar', true]);
        expect(nullIfBlank(value: collect([1, 'foobar', true]),  fallback: 'fooino'))->toEqual(collect([1, 'foobar', true]));

        expect(value(nullIfBlank(value: fn() => 'foobar')))->toEqual('foobar');

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        expect((string) nullIfBlank(value: $object, fallback: 'fooino'))->toEqual('foobar');
    });

    test('nullIfBlank returns null when the value is blank', function () {

        expect(nullIfBlank(value: null))->toEqual(null);
        expect(nullIfBlank(value: 'null'))->toEqual(null);
        expect(nullIfBlank(value: 'NULL'))->toEqual(null);
        expect(nullIfBlank(value: 'NULl'))->toEqual(null);
        expect(nullIfBlank(value: 'NULl', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlank(value: ''))->toEqual(null);
        expect(nullIfBlank(value: '      '))->toEqual(null);
        expect(nullIfBlank(value: '  "" '))->toEqual(null);
        expect(nullIfBlank(value: '  " '))->toEqual(null);
        expect(nullIfBlank(value: "  ' "))->toEqual(null);
        expect(nullIfBlank(value: "  '' "))->toEqual(null);
        expect(nullIfBlank(value: "  ' \" ' "))->toEqual(null);
        expect(nullIfBlank(value: "  ' \" ' ", fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlank(value: []))->toEqual(null);
        expect(nullIfBlank(value: [], fallback: 'fooino'))->toEqual('fooino');
        expect(nullIfBlank(value: collect([])))->toEqual(null);

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };

        expect(nullIfBlank(value: $object))->toEqual(null);
    });

    test('nullIfBlankOrZero returns value when it is filled and not zero', function () {

        expect(nullIfBlankOrZero(value: 5, fallback: 'fooino'))->toEqual(5);
        expect(nullIfBlankOrZero(value: -5.5, fallback: 'fooino'))->toEqual(-5.5);

        expect(nullIfBlankOrZero(value: true, fallback: 'fooino'))->toEqual(true);
        expect(nullIfBlankOrZero(value: false, fallback: 'fooino'))->toEqual(false);

        expect(nullIfBlankOrZero(value: ' foobar ', fallback: 'fooino'))->toEqual(' foobar ');

        expect(nullIfBlankOrZero(value: [1, 'foobar', true], fallback: 'fooino'))->toEqual([1, 'foobar', true]);
        expect(nullIfBlankOrZero(value: collect([1, 'foobar', true]),  fallback: 'fooino'))->toEqual(collect([1, 'foobar', true]));

        expect(value(nullIfBlankOrZero(value: fn() => 'foobar')))->toEqual('foobar');

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        expect((string) nullIfBlankOrZero(value: $object, fallback: 'fooino'))->toEqual('foobar');
    });

    test('nullIfBlankOrZero returns null when the value is blank or zero', function () {

        expect(nullIfBlankOrZero(value: 0))->toEqual(null);
        expect(nullIfBlankOrZero(value: 0.0))->toEqual(null);
        expect(nullIfBlankOrZero(value: '0'))->toEqual(null);
        expect(nullIfBlankOrZero(value: '0.0'))->toEqual(null);
        expect(nullIfBlankOrZero(value: '0', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: null))->toEqual(null);
        expect(nullIfBlankOrZero(value: 'null'))->toEqual(null);
        expect(nullIfBlankOrZero(value: 'NULL'))->toEqual(null);
        expect(nullIfBlankOrZero(value: 'NULl'))->toEqual(null);
        expect(nullIfBlankOrZero(value: 'NULl', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: ''))->toEqual(null);
        expect(nullIfBlankOrZero(value: '      '))->toEqual(null);
        expect(nullIfBlankOrZero(value: "  '' "))->toEqual(null);
        expect(nullIfBlankOrZero(value: "  ' \" ' "))->toEqual(null);
        expect(nullIfBlankOrZero(value: "  ' \" ' ", fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: []))->toEqual(null);
        expect(nullIfBlankOrZero(value: [], fallback: 'fooino'))->toEqual('fooino');
        expect(nullIfBlankOrZero(value: collect([])))->toEqual(null);

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };

        expect(nullIfBlankOrZero(value: $object))->toEqual(null);
    });

    test('removeComma returns string and array value without comma', function () {

        expect(removeComma(123))->toEqual(123);
        expect(removeComma(123.11))->toEqual(123.11);

        expect(removeComma(' foobar '))->toEqual(' foobar ');
        expect(removeComma('123,123'))->toEqual('123123');
        expect(removeComma('123,test, '))->toEqual('123test ');

        expect(removeComma(null))->toEqual(null);
        expect(removeComma(true))->toEqual(true);
        expect(removeComma(false))->toEqual(false);

        $stdClass = new stdClass;
        expect(removeComma(['123,123', '123,foobar, ']))->toEqual(['123123', '123foobar ']);
        expect(removeComma(collect([1, 2])))->toEqual(collect([1, 2]));
        expect(removeComma($stdClass))->toEqual($stdClass);
    });

    test('removeSpace returns string and array value without space', function () {

        expect(removeSpace(12))->toEqual(12);
        expect(removeSpace(12.12))->toEqual(12.12);

        expect(removeSpace('  '))->toEqual('');
        expect(removeSpace('foobar'))->toEqual('foobar');
        expect(removeSpace(' foobar'))->toEqual('foobar');
        expect(removeSpace('foobar '))->toEqual('foobar');
        expect(removeSpace(' foobar '))->toEqual('foobar');
        expect(removeSpace(' 0912 123 1234 '))->toEqual('09121231234');

        expect(removeSpace(null))->toEqual(null);
        expect(removeSpace(true))->toEqual(true);
        expect(removeSpace(false))->toEqual(false);

        $stdClass = new stdClass;
        expect(removeSpace([1, ' 0912 123 1234 ']))->toEqual([1, '09121231234']);
        expect(removeSpace(collect([1, 2])))->toEqual(collect([1, 2]));
        expect(removeSpace($stdClass))->toEqual($stdClass);
    });

    test('sanitizeNumber remove space and comma from value',  function () {

        expect(sanitizeNumber(123))->toEqual(123);
        expect(sanitizeNumber(123.123))->toEqual(123.123);

        expect(sanitizeNumber('+98 912 111 2222 '))->toEqual('+989121112222');
        expect(sanitizeNumber(' 1,222 333,444'))->toEqual('1222333444');
        expect(sanitizeNumber(' '))->toEqual('');


        expect(sanitizeNumber(null))->toEqual(null);
        expect(sanitizeNumber(true))->toEqual(true);
        expect(sanitizeNumber(false))->toEqual(false);

        $stdClass = new stdClass;
        expect(sanitizeNumber(['123,123 ', ' 0912 123 1234 ']))->toEqual(['123123', '09121231234']);
        expect(sanitizeNumber(collect([1, 2])))->toEqual(collect([1, 2]));
        expect(sanitizeNumber($stdClass))->toEqual($stdClass);
    });

    test('replaceSlashToDash does the replacement when the value is string or array', function () {

        expect(replaceSlashToDash(value: 123))->toEqual(123);
        expect(replaceSlashToDash(value: 123.123))->toEqual(123.123);

        expect(replaceSlashToDash(value: '2023/01/02'))->toEqual('2023-01-02');
        expect(replaceSlashToDash(value: ''))->toEqual('');
        expect(replaceSlashToDash(value: ' foobar'))->toEqual(' foobar');

        expect(replaceSlashToDash(value: null))->toEqual(null);
        expect(replaceSlashToDash(value: true))->toEqual(true);
        expect(replaceSlashToDash(value: false))->toEqual(false);

        $object = new stdClass;
        expect(replaceSlashToDash(value: ['hi/hello', '2023/01/02 11:00:00']))->toEqual(['hi-hello', '2023-01-02 11:00:00']);
        expect(replaceSlashToDash(value: [123]))->toEqual([123]);
        expect(replaceSlashToDash(value: collect([123])))->toEqual(collect([123]));
        expect(replaceSlashToDash(value: $object))->toEqual($object);
    });


    test('setDefaultLocale change app.locale config', function () {

        expect(config('app.locale'))->toEqual('en');

        setDefaultLocale(locale: 'fa');
        expect(config('app.locale'))->toEqual('fa');
    });

    test('getDefaultLocale get default locale from config', function () {

        expect(getDefaultLocale())->toEqual('en');

        config(['app.locale' => null]);
        expect(getDefaultLocale())->toEqual('fa');
    });

    test('currentDate returns current date in Y-m-d format', function () {

        expect(currentDate())->toEqual(date('Y-m-d'));
    });

    test('currentDateTime returns current date in Y-m-d H:i:s format', function () {

        expect(currentDateTime())->toEqual(date('Y-m-d H:i:s'));
    });

    test('callMethodIfExists call existing method or returns the fallback', function () {

        expect(callMethodIfExists(new CustomClass, 'pi', 'fooino'))->toEqual(3.14);
        expect(callMethodIfExists(CustomClass::class, 'pi', 'fooino'))->toEqual(3.14);

        expect(callMethodIfExists(CustomClass::class, 'abs', 'fooino', -5))->toEqual(5);

        expect(callMethodIfExists(CustomClass::class, 'power', 'fooino'))->toEqual('fooino');
        expect(callMethodIfExists(CustomClass::class, 'power', fn($a) => $a * $a, 5))->toEqual(25);
    });
});
