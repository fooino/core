<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\Data\Datasets;
use stdClass;
use Stringable;

class CustomClass
{
    public function __construct(public int $precision = 0) {}

    public function pi(): float
    {
        return 3.14;
    }

    public function abs(int $a): int
    {
        return abs($a);
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }
};

describe('Helpers unit tests', function () {

    test('isZero returns true', function ($zero) {

        expect(isZero($zero))->toBeTrue();

        // 
    })
        ->with(Datasets::merge(
            'zeros',
            new class implements Stringable {
                public function __toString()
                {
                    return '0';
                }
            },
        ));


    test('isZero returns false', function ($nonZero) {

        expect(isZero($nonZero))->toBeFalse();

        // 
    })
        ->with(Datasets::merge(
            'nonZero',
            true,
            false,
            fn() => [],
            fn() => [0],
            fn() => fn() => 0,
            new stdClass,
            new class implements Stringable {
                public function __toString()
                {
                    return 'foobar';
                }
            }
        ));

    test('nullIfBlank returns value when it is filled', function () {

        expect(nullIfBlank(value: 0, fallback: 'fooino'))->toBe(0);
        expect(nullIfBlank(value: 5, fallback: 'fooino'))->toBe(5);
        expect(nullIfBlank(value: 5_000_000_000_000, fallback: 'fooino'))->toBe(5_000_000_000_000);
        expect(nullIfBlank(value: 4E+6, fallback: 'fooino'))->toBe(4E+6);

        expect(nullIfBlank(value: 0.0, fallback: 'fooino'))->toBe(0.0);
        expect(nullIfBlank(value: -5.5, fallback: 'fooino'))->toBe(-5.5);
        expect(nullIfBlank(value: 0.0000101, fallback: 'fooino'))->toBe(0.0000101);
        expect(nullIfBlank(value: 4.21E-6, fallback: 'fooino'))->toBe(4.21E-6);

        expect(nullIfBlank(value: true, fallback: 'fooino'))->toBe(true);
        expect(nullIfBlank(value: false, fallback: 'fooino'))->toBe(false);

        expect(nullIfBlank(value: '0', fallback: 'fooino'))->toBe('0');
        expect(nullIfBlank(value: '0.0', fallback: 'fooino'))->toBe('0.0');
        expect(nullIfBlank(value: 'false', fallback: 'fooino'))->toBe('false');
        expect(nullIfBlank(value: ' foobar ', fallback: 'fooino'))->toBe(' foobar ');
        expect(nullIfBlank(value: ' null"ish ', fallback: 'fooino'))->toBe(' null"ish ');

        expect(nullIfBlank(value: [null], fallback: 'fooino'))->toBe([null]);
        expect(nullIfBlank(value: [false], fallback: 'fooino'))->toBe([false]);
        expect(nullIfBlank(value: [""], fallback: 'fooino'))->toBe([""]);
        expect(nullIfBlank(value: [1, 'foobar', true], fallback: 'fooino'))->toBe([1, 'foobar', true]);

        expect(nullIfBlank(value: collect([null]),  fallback: 'fooino'))->toEqual(collect([null]));
        expect(nullIfBlank(value: collect([false]),  fallback: 'fooino'))->toEqual(collect([false]));
        expect(nullIfBlank(value: collect([""]),  fallback: 'fooino'))->toEqual(collect([""]));
        expect(nullIfBlank(value: collect([1, "foobar", true]),  fallback: 'fooino'))->toEqual(collect([1, "foobar", true]));

        expect(value(nullIfBlank(value: fn() => 'foobar',  fallback: 'fooino')))->toBe('foobar');

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        expect((string) nullIfBlank(value: $object, fallback: 'fooino'))->toBe('foobar');
    });

    test('nullIfBlank returns null when the value is blank', function () {

        expect(nullIfBlank(value: null))->toBeNull();

        expect(nullIfBlank(value: 'null'))->toBeNull();
        expect(nullIfBlank(value: 'NULL'))->toBeNull();
        expect(nullIfBlank(value: 'NULl'))->toBeNull();
        expect(nullIfBlank(value: ' NaN'))->toBeNull();
        expect(nullIfBlank(value: 'undefined '))->toBeNull();
        expect(nullIfBlank(value: '" \'null ` nan undefined'))->toBeNull();
        expect(nullIfBlank(value: 'null', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlank(value: ''))->toBeNull();
        expect(nullIfBlank(value: '      '))->toBeNull();
        expect(nullIfBlank(value: '  "" '))->toBeNull();
        expect(nullIfBlank(value: '  " '))->toBeNull();
        expect(nullIfBlank(value: "  ' "))->toBeNull();
        expect(nullIfBlank(value: "  ``` "))->toBeNull();
        expect(nullIfBlank(value: "  '' "))->toBeNull();
        expect(nullIfBlank(value: "  ' \" ' "))->toBeNull();
        expect(nullIfBlank(value: "  ' \" ' ", fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlank(value: []))->toBeNull();
        expect(nullIfBlank(value: [], fallback: 'fooino'))->toEqual('fooino');
        expect(nullIfBlank(value: collect([])))->toBeNull();

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };

        expect(nullIfBlank(value: $object))->toBeNull();
    });

    test('nullIfBlankOrZero returns value when it is filled and not zero', function () {

        expect(nullIfBlankOrZero(value: 5, fallback: 'fooino'))->toBe(5);
        expect(nullIfBlankOrZero(value: 0.0000001, fallback: 'fooino'))->toBe(0.0000001);
        expect(nullIfBlankOrZero(value: 4.12E-10, fallback: 'fooino'))->toBe(4.12E-10);
        expect(nullIfBlankOrZero(value: -5.5, fallback: 'fooino'))->toBe(-5.5);

        expect(nullIfBlankOrZero(value: true, fallback: 'fooino'))->toBe(true);
        expect(nullIfBlankOrZero(value: false, fallback: 'fooino'))->toBe(false);

        expect(nullIfBlankOrZero(value: '0.25', fallback: 'fooino'))->toBe('0.25');
        expect(nullIfBlankOrZero(value: 'false', fallback: 'fooino'))->toBe('false');
        expect(nullIfBlankOrZero(value: ' foobar ', fallback: 'fooino'))->toBe(' foobar ');

        expect(nullIfBlankOrZero(value: [null], fallback: 'fooino'))->toBe([null]);
        expect(nullIfBlankOrZero(value: [false], fallback: 'fooino'))->toBe([false]);
        expect(nullIfBlankOrZero(value: [""], fallback: 'fooino'))->toBe([""]);
        expect(nullIfBlankOrZero(value: [1, 'foobar', true], fallback: 'fooino'))->toBe([1, 'foobar', true]);
        expect(nullIfBlankOrZero(value: collect([1, 'foobar', true]),  fallback: 'fooino'))->toEqual(collect([1, 'foobar', true]));

        expect(value(nullIfBlankOrZero(value: fn() => 'foobar',  fallback: 'fooino')))->toEqual('foobar');

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        expect((string) nullIfBlankOrZero(value: $object, fallback: 'fooino'))->toEqual('foobar');
    });

    test('nullIfBlankOrZero returns null when the value is blank or zero', function () {

        expect(nullIfBlankOrZero(value: 0))->toBeNull();
        expect(nullIfBlankOrZero(value: -0))->toBeNull();
        expect(nullIfBlankOrZero(value: '0'))->toBeNull();
        expect(nullIfBlankOrZero(value: '-0'))->toBeNull();
        expect(nullIfBlankOrZero(value: '0000'))->toBeNull();
        expect(nullIfBlankOrZero(value: 0, fallback: 0))->toBe(0);
        expect(nullIfBlankOrZero(value: -0, fallback: false))->toBe(false);
        expect(nullIfBlankOrZero(value: '0', fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: 0.0))->toBeNull();
        expect(nullIfBlankOrZero(value: -0.0))->toBeNull();
        expect(nullIfBlankOrZero(value: '0.0'))->toBeNull();
        expect(nullIfBlankOrZero(value: '-0.0'))->toBeNull();
        expect(nullIfBlankOrZero(value: '0.000000'))->toBeNull();
        expect(nullIfBlankOrZero(value: '-000.000'))->toBeNull();


        expect(nullIfBlankOrZero(value: null))->toBeNull();
        expect(nullIfBlankOrZero(value: 'null'))->toBeNull();
        expect(nullIfBlankOrZero(value: 'NaN'))->toBeNull();
        expect(nullIfBlankOrZero(value: 'Undefined'))->toBeNull();

        expect(nullIfBlankOrZero(value: ''))->toBeNull();
        expect(nullIfBlankOrZero(value: '      '))->toBeNull();
        expect(nullIfBlankOrZero(value: " ` '' "))->toBeNull();
        expect(nullIfBlankOrZero(value: "  ' \" ' "))->toBeNull();
        expect(nullIfBlankOrZero(value: "  ' \" ' ", fallback: 'fooino'))->toEqual('fooino');

        expect(nullIfBlankOrZero(value: []))->toBeNull();
        expect(nullIfBlankOrZero(value: collect([])))->toBeNull();

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };

        expect(nullIfBlankOrZero(value: $object))->toBeNull();

        $object = new class implements Stringable {
            public function __toString(): string
            {
                return '0';
            }
        };

        expect(nullIfBlankOrZero(value: $object))->toBeNull();
    });

    test('removeComma returns string and array value without comma', function () {

        expect(removeComma(value: 123))->toBe(123);
        expect(removeComma(value: 123.11))->toBe(123.11);

        expect(removeComma(value: ' foobar '))->toBe(' foobar ');
        expect(removeComma(value: '123,123'))->toBe('123123');
        expect(removeComma(value: '5,000,000', replace: '_'))->toBe('5_000_000');
        expect(removeComma(value: '123,test, '))->toBe('123test ');

        expect(removeComma(value: null))->toBe(null);
        expect(removeComma(value: true))->toBe(true);
        expect(removeComma(value: false))->toBe(false);

        expect(removeComma(value: ['123,123', '123,foobar, ']))->toBe(['123123', '123foobar ']);
        expect(removeComma(value: ['5,000,000', '123,foobar, '], replace: '_'))->toBe(['5_000_000', '123_foobar_ ']);
    });

    test('removeSpace returns string and array value without space', function () {

        expect(removeSpace(value: 12))->toBe(12);
        expect(removeSpace(value: 12.12))->toBe(12.12);

        expect(removeSpace(value: '  '))->toBe('');
        expect(removeSpace(value: 'foobar'))->toBe('foobar');
        expect(removeSpace(value: ' foobar'))->toBe('foobar');
        expect(removeSpace(value: 'foobar '))->toBe('foobar');
        expect(removeSpace(value: ' foobar '))->toBe('foobar');
        expect(removeSpace(value: ' 0912 123 1234 '))->toBe('09121231234');
        expect(removeSpace(value: ' 0912 123 1234 ', replace: "_"))->toBe('_0912_123_1234_');

        expect(removeSpace(value: null))->toBe(null);
        expect(removeSpace(value: true))->toBe(true);
        expect(removeSpace(value: false))->toBe(false);

        expect(removeSpace(value: [1, ' 0912 123 1234 ']))->toBe(['1', '09121231234']);
        expect(removeSpace(value: [1, ' 0912 123 1234 '], replace: "_"))->toBe(['1', '_0912_123_1234_']);
    });

    test('sanitizeNumber remove space and comma from value',  function () {

        expect(sanitizeNumber(123))->toBe(123);
        expect(sanitizeNumber(123.123))->toBe(123.123);

        expect(sanitizeNumber('+98 912 111 2222 '))->toBe('+989121112222');
        expect(sanitizeNumber(' 1,222 333,444'))->toBe('1222333444');
        expect(sanitizeNumber(' '))->toBe('');

        expect(sanitizeNumber(null))->toBe(null);
        expect(sanitizeNumber(true))->toBe(true);
        expect(sanitizeNumber(false))->toBe(false);

        expect(sanitizeNumber([1, '123,123 ', ' 0912 123 1234 ']))->toBe(['1', '123123', '09121231234']);
    });

    test('replaceSlashToDash does the replacement when the value is string or array', function () {

        expect(replaceSlashToDash(value: 123))->toBe(123);
        expect(replaceSlashToDash(value: 123.123))->toBe(123.123);

        expect(replaceSlashToDash(value: '2023/01/02'))->toBe('2023-01-02');
        expect(replaceSlashToDash(value: ''))->toBe('');
        expect(replaceSlashToDash(value: ' foobar'))->toBe(' foobar');

        expect(replaceSlashToDash(value: null))->toBe(null);
        expect(replaceSlashToDash(value: true))->toBe(true);
        expect(replaceSlashToDash(value: false))->toBe(false);

        expect(replaceSlashToDash(value: ['hi/hello', '2023/01/02 11:00:00']))->toBe(['hi-hello', '2023-01-02 11:00:00']);
        expect(replaceSlashToDash(value: [123]))->toBe(['123']);
    });

    test('setDefaultLocale change app.locale config', function () {

        expect(config('app.locale'))->toBe('en');

        setDefaultLocale(locale: 'fa');
        expect(config('app.locale'))->toBe('fa');
        expect(getDefaultLocale())->toBe('fa');
    });

    test('getDefaultLocale get default locale from config', function () {

        expect(getDefaultLocale())->toBe('en');

        config(['app.locale' => null]);
        expect(getDefaultLocale())->toBe('fa');
    });

    test('currentDate returns current date in Y-m-d format', function () {

        expect(currentDate())->toBe(date('Y-m-d'));
    });

    test('currentDateTime returns current date in Y-m-d H:i:s format', function () {

        expect(currentDateTime())->toBe(date('Y-m-d H:i:s'));
    });

    test('callMethodIfExists call existing method or returns the fallback', function () {

        expect(callMethodIfExists(object: new CustomClass, method: 'pi', fallback: 'fooino'))->toBe(3.14);

        expect(callMethodIfExists(object: CustomClass::class, method: 'getPrecision', fallback: 'fooino'))->toBe(0);
        expect(callMethodIfExists(object: CustomClass::class, method: 'getPrecision', fallback: 'fooino', constructorArgs: ['precision' => 2]))->toBe(2);
        expect(callMethodIfExists(object: new CustomClass(2), method: 'getPrecision', fallback: 'fooino'))->toBe(2);

        expect(callMethodIfExists(object: "foobar", method: 'abs', fallback: 'NOT EXIST'))->toBe('NOT EXIST');
        expect(callMethodIfExists(object: CustomClass::class, method: 'abs', fallback: 'fooino', methodArgs: ['a' => -5]))->toBe(5);

        expect(callMethodIfExists(object: CustomClass::class, method: 'power', fallback: 'NOT EXIST'))->toBe('NOT EXIST');
        expect(callMethodIfExists(object: CustomClass::class, method: 'power', fallback: fn($a) => $a * $a, methodArgs: ['a' => 5]))->toBe(25);
    });

    test('percentageChange method', function () {

        expect(percentageChange(from: 200, to: 50))->toBe('-75');
        expect(percentageChange(from: 20, to: 40))->toBe('100');
        expect(percentageChange(from: 40, to: 20))->toBe('-50');
        expect(percentageChange(from: 10, to: 12))->toBe('20');

        expect(percentageChange(from: 12, to: 12))->toBe('0');
        expect(percentageChange(from: 12, to: -12))->toBe('-200');
        expect(percentageChange(from: 12, to: 0))->toBe('-100');
        expect(percentageChange(from: 0, to: -12))->toBe('100');
        expect(percentageChange(from: -12, to: 12))->toBe('200');

        expect(percentageChange(from: 13, to: 14))->toBe('7.69');
        expect(percentageChange(from: 13, to: 14, precision: 12))->toBe('7.6923076923');
    });
});
