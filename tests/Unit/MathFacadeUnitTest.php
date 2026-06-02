<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Facades\Math;
use TypeError;

describe('Math facade using FooinoMathHandler', function () {

    test('precision getter and setter', function () {

        expect(Math::getPrecision())->toBe(10);
        expect(Math::setPrecision(precision: 5)->getPrecision())->toBe(5);

        expect(math()->getPrecision())->toBe(10);
        expect(math(precision: 5)->getPrecision())->toBe(5);
    });

    test('convertScientificNumber method', function () {

        expect(Math::convertScientificNumber(11.000000))->toBe('11');
        expect(Math::convertScientificNumber(11))->toBe('11');
        expect(Math::convertScientificNumber(-11))->toBe('-11');
        expect(Math::convertScientificNumber(11.11))->toBe('11.11');

        expect(Math::convertScientificNumber('1.1e+8'))->toBe('110000000.0000000000');
        expect(Math::convertScientificNumber(1.1e+8))->toBe('110000000');
        expect(Math::convertScientificNumber('1.1e+20'))->toBe('110000000000000000000.0000000000');
        expect(Math::convertScientificNumber('1.1E-8'))->toBe('0.0000000110');
        expect(Math::convertScientificNumber('1.1e-8'))->toBe('0.0000000110');
        expect(Math::convertScientificNumber('1.1e-11'))->toBe('0.0000000000'); // Max scale: 10
        expect(Math::convertScientificNumber('-1.1e-11'))->toBe('-0.0000000000'); // Max scale: 10
        expect(Math::convertScientificNumber('20.1e+20'))->toBe('2010000000000000000000.0000000000');

        expect(Math::convertScientificNumber(null))->toBe('0');
        expect(Math::convertScientificNumber('abc1E+3xyz'))->toBe('abc1E+3xyz'); // contains 1E+3 which is valid Scientific Number but the method must not convert it
        expect(Math::convertScientificNumber('test'))->toBe('test');
    });

    test('trimTrailingZeros method', function () {

        expect(Math::trimTrailingZeros(11))->toBe('11');
        expect(Math::trimTrailingZeros(11.11))->toBe('11.11');
        expect(Math::trimTrailingZeros(11.1100000000))->toBe('11.11');
        expect(Math::trimTrailingZeros(11.0000000000))->toBe('11');
        expect(Math::trimTrailingZeros("11-0000000000", '-'))->toBe('11');
        expect(Math::trimTrailingZeros("11-2100000000", '-'))->toBe('11-21');

        expect(Math::trimTrailingZeros("1.1e+8"))->toBe('110000000');
        expect(Math::trimTrailingZeros("-1.1e+8"))->toBe('-110000000');
        expect(Math::trimTrailingZeros("1.1E-8"))->toBe('0.000000011');
        expect(Math::trimTrailingZeros(0))->toBe('0');
        expect(Math::trimTrailingZeros(0.0))->toBe('0');
        expect(Math::trimTrailingZeros(null))->toBe('0');
        expect(Math::trimTrailingZeros('test'))->toBe('test');
    });

    test('decimalPlaceNumber method', function () {
        expect(Math::decimalPlaceNumber(0.000000000100))->toBe(10);
        expect(Math::decimalPlaceNumber('0.00000000100'))->toBe(9);
        expect(Math::decimalPlaceNumber(1.1e-8))->toBe(9);
        expect(Math::decimalPlaceNumber(1))->toBe(0);
        expect(Math::decimalPlaceNumber(0))->toBe(0);
        expect(Math::decimalPlaceNumber(null))->toBe(0);
        expect(Math::decimalPlaceNumber('test'))->toBe(0);
        expect(Math::decimalPlaceNumber('0-0123', '-'))->toBe(4);
    });

    test('number method', function () {

        expect(Math::setPrecision(precision: 4)->number(0.44015042))->toBe('0.4401');

        expect(Math::number('.44015042'))->toBe('0.44015042');
        expect(Math::number(11))->toBe('11');
        expect(Math::number(-11.))->toBe('-11');
        expect(Math::number(11.000001000))->toBe('11.000001');
        expect(Math::number(1.1e+8))->toBe('110000000');
        expect(Math::number(1.101e-5))->toBe('0.00001101');
        expect(Math::number(1.1E+20))->toBe('110000000000000000000');

        expect(Math::number('test'))->toBe('test');
        expect(Math::number('foo.bar'))->toBe('foo.bar');
        expect(Math::number(null))->toBe('0');
        expect(Math::number(0))->toBe('0');
        expect(Math::number(0.0))->toBe('0');

        expect(math(precision: 4)->number(0.44015042))->toBe('0.4401');

        expect(number('.44015042'))->toBe('0.44015042');
        expect(number(11.000001000))->toBe('11.000001');
        expect(number(1.1e+8))->toBe('110000000');
        expect(number(1.101e-5))->toBe('0.00001101');
        expect(number(1.1e+20))->toBe('110000000000000000000');

        expect(number('test'))->toBe('test');
        expect(number('foo.bar'))->toBe('foo.bar');
        expect(number(null))->toBe('0');
        expect(number(0))->toBe('0');
        expect(number(0.0))->toBe('0');
    });

    test('numberFormat method', function () {

        expect(Math::numberFormat(number: null))->toBe('0');
        expect(Math::numberFormat(number: 0))->toBe('0');
        expect(Math::numberFormat(number: 0.0))->toBe('0');
        expect(Math::numberFormat(number: 1.1e-8))->toBe("0.000000011");
        expect(Math::numberFormat(number: 1.1e+8))->toBe("110,000,000");
        expect(Math::numberFormat(number: 5000000))->toBe("5,000,000");
        expect(Math::numberFormat(number: 5000000.50))->toBe("5,000,000.5");
        expect(Math::numberFormat(number: 5000000.5))->toBe("5,000,000.5");
        expect(Math::numberFormat(number: 5000000.05))->toBe("5,000,000.05");
        expect(Math::numberFormat(number: 5000000.015))->toBe("5,000,000.015");
        expect(Math::numberFormat(number: 5000000.0150))->toBe("5,000,000.015");
        expect(Math::numberFormat(number: 5000000.01501))->toBe("5,000,000.01501");
        expect(Math::numberFormat(number: 1.1e+20, thousandsSeparator: "|"))->toBe("110|000|000|000|000|000|000");
        expect(fn() => Math::numberFormat(number: 'test'))->toThrow(TypeError::class);


        expect(numberFormat(number: null))->toBe('0');
        expect(numberFormat(number: 0))->toBe('0');
        expect(numberFormat(number: 0.0))->toBe('0');
        expect(numberFormat(number: 1.1e-8))->toBe("0.000000011");
        expect(numberFormat(number: 1.1e+8))->toBe("110,000,000");
        expect(numberFormat(number: 5000000))->toBe("5,000,000");
        expect(numberFormat(number: 5000000.50))->toBe("5,000,000.5");
        expect(numberFormat(number: 5000000.5))->toBe("5,000,000.5");
        expect(numberFormat(number: 5000000.05))->toBe("5,000,000.05");
        expect(numberFormat(number: 5000000.015))->toBe("5,000,000.015");
        expect(numberFormat(number: 5000000.0150))->toBe("5,000,000.015");
        expect(numberFormat(number: 5000000.01501))->toBe("5,000,000.01501");
        expect(numberFormat(number: 1.1e+20, thousandsSeparator: "|"))->toBe("110|000|000|000|000|000|000");
        expect(fn() => numberFormat(number: 'test'))->toThrow(TypeError::class);
    });
});
