<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Facades\Math;

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
});
