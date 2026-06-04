<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Facades\Math;
use ValueError;

describe('Math facade using FooinoMathHandler', function () {

    test('precision getter and setter', function () {

        expect(Math::getPrecision())->toBe(12);
        expect(Math::setPrecision(precision: 5)->getPrecision())->toBe(5);

        expect(math()->getPrecision())->toBe(12);
        expect(math(precision: 5)->getPrecision())->toBe(5);

        expect(bcscale())->toBe(12);

        try {

            Math::setPrecision(precision: 20);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidPrecision');
            expect($e->getCode())->toBe(10101);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'precision' => 20,
                'bc_scale'  => 12
            ]);
        }

        try {

            Math::setPrecision(precision: -1);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidPrecision');
            expect($e->getCode())->toBe(10101);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'precision' => -1,
                'bc_scale'  => 12
            ]);
        }
    });

    test('convertScientificNumber method', function () {

        expect(Math::convertScientificNumber('null'))->toBe('0');
        expect(Math::convertScientificNumber('null.null'))->toBe('0');
        expect(Math::convertScientificNumber('""'))->toBe('0');
        expect(Math::convertScientificNumber('foobar'))->toBe('foobar');
        expect(Math::convertScientificNumber('foo.bar'))->toBe('foo.bar');
        expect(Math::convertScientificNumber('foo.bar.ino'))->toBe('foo.bar.ino');
        expect(Math::convertScientificNumber('-foo.bar.ino'))->toBe('-foo.bar.ino');
        expect(Math::convertScientificNumber('abc1E+3xyz'))->toBe('abc1E+3xyz'); // contains 1E+3 which is valid Scientific Number but the method must not convert it
        expect(Math::convertScientificNumber('test'))->toBe('test');

        expect(Math::convertScientificNumber(''))->toBe('0');
        expect(Math::convertScientificNumber('.'))->toBe('0');
        expect(Math::convertScientificNumber('+.'))->toBe('0');
        expect(Math::convertScientificNumber('-.'))->toBe('0');

        expect(Math::convertScientificNumber(0.))->toBe('0');
        expect(Math::convertScientificNumber(+0.))->toBe('0');
        expect(Math::convertScientificNumber(-0.))->toBe('0');

        expect(Math::convertScientificNumber('0.'))->toBe('0');
        expect(Math::convertScientificNumber('+0.'))->toBe('0');
        expect(Math::convertScientificNumber('-0.'))->toBe('0');

        expect(Math::convertScientificNumber(0))->toBe('0');
        expect(Math::convertScientificNumber(+0))->toBe('0');
        expect(Math::convertScientificNumber(-0))->toBe('0');

        expect(Math::convertScientificNumber('0'))->toBe('0');
        expect(Math::convertScientificNumber('+0'))->toBe('0');
        expect(Math::convertScientificNumber('-0'))->toBe('0');

        expect(Math::convertScientificNumber(0.0))->toBe('0');
        expect(Math::convertScientificNumber(+0.0))->toBe('0');
        expect(Math::convertScientificNumber(-0.0))->toBe('0');

        expect(Math::convertScientificNumber('0.0'))->toBe('0');
        expect(Math::convertScientificNumber('+0.0'))->toBe('0');
        expect(Math::convertScientificNumber('-0.0'))->toBe('0');

        expect(Math::convertScientificNumber(.0))->toBe('0');
        expect(Math::convertScientificNumber(+.0))->toBe('0');
        expect(Math::convertScientificNumber(-.0))->toBe('0');

        expect(Math::convertScientificNumber('.0'))->toBe('0');
        expect(Math::convertScientificNumber('+.0'))->toBe('0');
        expect(Math::convertScientificNumber('-.0'))->toBe('0');

        expect(Math::convertScientificNumber(' 0.0 '))->toBe('0');
        expect(Math::convertScientificNumber(' +0.0 '))->toBe('0');
        expect(Math::convertScientificNumber(' -0.0 '))->toBe('0');

        expect(Math::convertScientificNumber(11))->toBe('11');
        expect(Math::convertScientificNumber(+11))->toBe('11');
        expect(Math::convertScientificNumber(-11))->toBe('-11');

        expect(Math::convertScientificNumber('11'))->toBe('11');
        expect(Math::convertScientificNumber('+11'))->toBe('11');
        expect(Math::convertScientificNumber('-11'))->toBe('-11');

        expect(Math::convertScientificNumber(11.000000))->toBe('11');
        expect(Math::convertScientificNumber(+11.000000))->toBe('11');
        expect(Math::convertScientificNumber(-11.000000))->toBe('-11');

        expect(Math::convertScientificNumber('11.000000'))->toBe('11');
        expect(Math::convertScientificNumber('+11.000000'))->toBe('11');
        expect(Math::convertScientificNumber('-11.000000'))->toBe('-11');

        expect(Math::convertScientificNumber(11.011000))->toBe('11.011');
        expect(Math::convertScientificNumber(+11.011000))->toBe('11.011');
        expect(Math::convertScientificNumber(-11.011000))->toBe('-11.011');

        expect(Math::convertScientificNumber('11.011000'))->toBe('11.011000');
        expect(Math::convertScientificNumber('+11.011000'))->toBe('11.011000');
        expect(Math::convertScientificNumber('-11.011000'))->toBe('-11.011000');

        expect(Math::convertScientificNumber(0.011000))->toBe('0.011');
        expect(Math::convertScientificNumber(.011000))->toBe('0.011');
        expect(Math::convertScientificNumber(+.011000))->toBe('0.011');
        expect(Math::convertScientificNumber(-.011000))->toBe('-0.011');

        expect(Math::convertScientificNumber('.011000'))->toBe('0.011000');
        expect(Math::convertScientificNumber('+.011000'))->toBe('0.011000');
        expect(Math::convertScientificNumber('-.011000'))->toBe('-0.011000');
        expect(Math::convertScientificNumber('-0.011000'))->toBe('-0.011000');

        expect(Math::convertScientificNumber(PHP_INT_MAX))->toBe("" . PHP_INT_MAX . "");
        expect(Math::convertScientificNumber(PHP_INT_MAX . '.' . PHP_INT_MAX))->toBe(PHP_INT_MAX . '.' . PHP_INT_MAX);

        expect(Math::convertScientificNumber(PHP_INT_MIN))->toBe("" . PHP_INT_MIN . "");

        expect(Math::convertScientificNumber(bcadd(PHP_INT_MAX, 1000, 0)))->toBe(bcadd(PHP_INT_MAX, 1000, 0));
        expect(Math::convertScientificNumber(bcsub(PHP_INT_MIN, 1000, 0)))->toBe(bcsub(PHP_INT_MIN, 1000, 0));


        expect(Math::convertScientificNumber(0.e+8))->toBe('0');
        expect(Math::convertScientificNumber(+0.e+8))->toBe('0');
        expect(Math::convertScientificNumber(-0.e+8))->toBe('0');

        expect(Math::convertScientificNumber('0.e+8'))->toBe('0');
        expect(Math::convertScientificNumber('+0.e+8'))->toBe('0');
        expect(Math::convertScientificNumber('-0.e+8'))->toBe('0');

        expect(Math::convertScientificNumber('.e+8'))->toBe('0');
        expect(Math::convertScientificNumber('+.e+8'))->toBe('0');
        expect(Math::convertScientificNumber('-.e+8'))->toBe('0');

        expect(Math::convertScientificNumber('e+8'))->toBe('0');
        expect(Math::convertScientificNumber('+e+8'))->toBe('0');
        expect(Math::convertScientificNumber('-e+8'))->toBe('0');

        expect(Math::convertScientificNumber('e8'))->toBe('0');
        expect(Math::convertScientificNumber('+e8'))->toBe('0');
        expect(Math::convertScientificNumber('-e8'))->toBe('0');

        expect(Math::convertScientificNumber('e'))->toBe('e');
        expect(Math::convertScientificNumber('+e'))->toBe('+e');
        expect(Math::convertScientificNumber('-e'))->toBe('-e');

        expect(Math::convertScientificNumber('E'))->toBe('E');
        expect(Math::convertScientificNumber('+E'))->toBe('+E');
        expect(Math::convertScientificNumber('-E'))->toBe('-E');

        expect(Math::convertScientificNumber(1e8))->toBe('100000000');
        expect(Math::convertScientificNumber(+1e8))->toBe('100000000');
        expect(Math::convertScientificNumber(-1e8))->toBe('-100000000');

        expect(Math::convertScientificNumber('1e8'))->toBe('100000000');
        expect(Math::convertScientificNumber('+1e8'))->toBe('100000000');
        expect(Math::convertScientificNumber('-1e8'))->toBe('-100000000');

        expect(Math::convertScientificNumber(0.1e8))->toBe('10000000');
        expect(Math::convertScientificNumber(.1e8))->toBe('10000000');
        expect(Math::convertScientificNumber(+.1e8))->toBe('10000000');
        expect(Math::convertScientificNumber(-.1e8))->toBe('-10000000');

        expect(Math::convertScientificNumber('0.1e8'))->toBe('10000000');
        expect(Math::convertScientificNumber('.1e8'))->toBe('10000000');
        expect(Math::convertScientificNumber('+.1e8'))->toBe('10000000');
        expect(Math::convertScientificNumber('-.1e8'))->toBe('-10000000');

        expect(Math::convertScientificNumber(1e+8))->toBe('100000000');
        expect(Math::convertScientificNumber(+1e+8))->toBe('100000000');
        expect(Math::convertScientificNumber(-1e+8))->toBe('-100000000');

        expect(Math::convertScientificNumber('1e+8'))->toBe('100000000');
        expect(Math::convertScientificNumber('+1e+8'))->toBe('100000000');
        expect(Math::convertScientificNumber('-1e+8'))->toBe('-100000000');

        expect(Math::convertScientificNumber(0.1e+8))->toBe('10000000');
        expect(Math::convertScientificNumber(.1e+8))->toBe('10000000');
        expect(Math::convertScientificNumber(+.1e+8))->toBe('10000000');
        expect(Math::convertScientificNumber(-.1e+8))->toBe('-10000000');

        expect(Math::convertScientificNumber('0.1e+8'))->toBe('10000000');
        expect(Math::convertScientificNumber('.1e+8'))->toBe('10000000');
        expect(Math::convertScientificNumber('+.1e+8'))->toBe('10000000');
        expect(Math::convertScientificNumber('-.1e+8'))->toBe('-10000000');

        expect(Math::convertScientificNumber(1e-8))->toBe('0.00000001');
        expect(Math::convertScientificNumber(+1e-8))->toBe('0.00000001');
        expect(Math::convertScientificNumber(-1e-8))->toBe('-0.00000001');

        expect(Math::convertScientificNumber('1e-8'))->toBe('0.00000001');
        expect(Math::convertScientificNumber('+1e-8'))->toBe('0.00000001');
        expect(Math::convertScientificNumber('-1e-8'))->toBe('-0.00000001');

        expect(Math::convertScientificNumber(0.1e-8))->toBe('0.000000001');
        expect(Math::convertScientificNumber(.1e-8))->toBe('0.000000001');
        expect(Math::convertScientificNumber(+.1e-8))->toBe('0.000000001');
        expect(Math::convertScientificNumber(-.1e-8))->toBe('-0.000000001');

        expect(Math::convertScientificNumber('0.1e-8'))->toBe('0.000000001');
        expect(Math::convertScientificNumber('.1e-8'))->toBe('0.000000001');
        expect(Math::convertScientificNumber('+.1e-8'))->toBe('0.000000001');
        expect(Math::convertScientificNumber('-.1e-8'))->toBe('-0.000000001');

        expect(Math::convertScientificNumber(1.1e+8))->toBe('110000000');
        expect(Math::convertScientificNumber(+1.1e+8))->toBe('110000000');
        expect(Math::convertScientificNumber(-1.1e+8))->toBe('-110000000');

        expect(Math::convertScientificNumber('1.1e+8'))->toBe('110000000');
        expect(Math::convertScientificNumber('+1.1e+8'))->toBe('110000000');
        expect(Math::convertScientificNumber('-1.1e+8 '))->toBe('-110000000');

        expect(Math::convertScientificNumber(1.1E-8))->toBe('0.000000011');
        expect(Math::convertScientificNumber(+1.1E-8))->toBe('0.000000011');
        expect(Math::convertScientificNumber(-1.1E-8))->toBe('-0.000000011');

        expect(Math::convertScientificNumber('1.1E-8'))->toBe('0.000000011');
        expect(Math::convertScientificNumber('+1.1E-8'))->toBe('0.000000011');
        expect(Math::convertScientificNumber('-1.1E-8'))->toBe('-0.000000011');

        expect(Math::convertScientificNumber('312.12E-2'))->toBe('3.1212');
        expect(Math::convertScientificNumber('+312.12E-2'))->toBe('3.1212');
        expect(Math::convertScientificNumber('-312.12E-2'))->toBe('-3.1212');

        expect(Math::convertScientificNumber('312.120E-2'))->toBe('3.1212');
        expect(Math::convertScientificNumber('31213141516171819.20E-14'))->toBe('312.131415161718192');

        expect(Math::convertScientificNumber('1.1e-20'))->toBe('0.000000000000000000011');
        expect(Math::convertScientificNumber('-1.1e-20'))->toBe('-0.000000000000000000011');

        expect(Math::convertScientificNumber('1.1e+20'))->toBe('110000000000000000000');
        expect(Math::convertScientificNumber('20.1e+20'))->toBe('2010000000000000000000');
    });

    test('trimTrailingZeros method', function () {

        expect(Math::trimTrailingZeros('test'))->toBe('test');
        expect(Math::trimTrailingZeros('foo.bar'))->toBe('foo.bar');
        expect(Math::trimTrailingZeros('foo.bar0'))->toBe('foo.bar');
        expect(Math::trimTrailingZeros('foo.0'))->toBe('foo');

        expect(Math::trimTrailingZeros(0))->toBe('0');
        expect(Math::trimTrailingZeros(+0))->toBe('0');
        expect(Math::trimTrailingZeros(-0))->toBe('0');

        expect(Math::trimTrailingZeros('0'))->toBe('0');
        expect(Math::trimTrailingZeros('+0'))->toBe('0');
        expect(Math::trimTrailingZeros('-0'))->toBe('0');

        expect(Math::trimTrailingZeros(0.0))->toBe('0');
        expect(Math::trimTrailingZeros(+0.0))->toBe('0');
        expect(Math::trimTrailingZeros(-0.0))->toBe('0');

        expect(Math::trimTrailingZeros('0.0'))->toBe('0');
        expect(Math::trimTrailingZeros('+0.0'))->toBe('0');
        expect(Math::trimTrailingZeros('-0.0'))->toBe('0');

        expect(Math::trimTrailingZeros(0.))->toBe('0');
        expect(Math::trimTrailingZeros(+0.))->toBe('0');
        expect(Math::trimTrailingZeros(-0.))->toBe('0');

        expect(Math::trimTrailingZeros('0.'))->toBe('0');
        expect(Math::trimTrailingZeros('+0.'))->toBe('0');
        expect(Math::trimTrailingZeros('-0.'))->toBe('0');

        expect(Math::trimTrailingZeros(.0))->toBe('0');
        expect(Math::trimTrailingZeros(+.0))->toBe('0');
        expect(Math::trimTrailingZeros(-.0))->toBe('0');

        expect(Math::trimTrailingZeros('.0'))->toBe('0');
        expect(Math::trimTrailingZeros('+.0'))->toBe('0');
        expect(Math::trimTrailingZeros('-.0'))->toBe('0');

        expect(Math::trimTrailingZeros(11))->toBe('11');
        expect(Math::trimTrailingZeros(+11))->toBe('11');
        expect(Math::trimTrailingZeros(-11))->toBe('-11');

        expect(Math::trimTrailingZeros('11'))->toBe('11');
        expect(Math::trimTrailingZeros('+11'))->toBe('11');
        expect(Math::trimTrailingZeros('-11'))->toBe('-11');

        expect(Math::trimTrailingZeros(11.11))->toBe('11.11');
        expect(Math::trimTrailingZeros(+11.11))->toBe('11.11');
        expect(Math::trimTrailingZeros(-11.11))->toBe('-11.11');

        expect(Math::trimTrailingZeros('11.11'))->toBe('11.11');
        expect(Math::trimTrailingZeros('+11.11'))->toBe('11.11');
        expect(Math::trimTrailingZeros('-11.11'))->toBe('-11.11');

        expect(Math::trimTrailingZeros(11.))->toBe('11');
        expect(Math::trimTrailingZeros(+11.))->toBe('11');
        expect(Math::trimTrailingZeros(-11.))->toBe('-11');

        expect(Math::trimTrailingZeros('11.'))->toBe('11');
        expect(Math::trimTrailingZeros('+11.'))->toBe('11');
        expect(Math::trimTrailingZeros('-11.'))->toBe('-11');

        expect(Math::trimTrailingZeros(.11))->toBe('0.11');
        expect(Math::trimTrailingZeros(+.11))->toBe('0.11');
        expect(Math::trimTrailingZeros(-.11))->toBe('-0.11');

        expect(Math::trimTrailingZeros('.11'))->toBe('0.11');
        expect(Math::trimTrailingZeros('+.11'))->toBe('0.11');
        expect(Math::trimTrailingZeros('-.11'))->toBe('-0.11');

        expect(Math::trimTrailingZeros(1100))->toBe('1100');
        expect(Math::trimTrailingZeros(+1100))->toBe('1100');
        expect(Math::trimTrailingZeros(-1100))->toBe('-1100');

        expect(Math::trimTrailingZeros('1100'))->toBe('1100');
        expect(Math::trimTrailingZeros('+1100'))->toBe('1100');
        expect(Math::trimTrailingZeros('-1100'))->toBe('-1100');

        expect(Math::trimTrailingZeros(1100.001100))->toBe('1100.0011');
        expect(Math::trimTrailingZeros(+1100.001100))->toBe('1100.0011');
        expect(Math::trimTrailingZeros(-1100.001100))->toBe('-1100.0011');

        expect(Math::trimTrailingZeros('1100.001100'))->toBe('1100.0011');
        expect(Math::trimTrailingZeros('+1100.001100'))->toBe('1100.0011');
        expect(Math::trimTrailingZeros('-1100.001100'))->toBe('-1100.0011');

        expect(Math::trimTrailingZeros(1100.))->toBe('1100');
        expect(Math::trimTrailingZeros(+1100.))->toBe('1100');
        expect(Math::trimTrailingZeros(-1100.))->toBe('-1100');

        expect(Math::trimTrailingZeros('1100.'))->toBe('1100');
        expect(Math::trimTrailingZeros('+1100.'))->toBe('1100');
        expect(Math::trimTrailingZeros('-1100.'))->toBe('-1100');

        expect(Math::trimTrailingZeros(.001100))->toBe('0.0011');
        expect(Math::trimTrailingZeros(+.001100))->toBe('0.0011');
        expect(Math::trimTrailingZeros(-.001100))->toBe('-0.0011');

        expect(Math::trimTrailingZeros('.001100'))->toBe('0.0011');
        expect(Math::trimTrailingZeros('+.001100'))->toBe('0.0011');
        expect(Math::trimTrailingZeros('-.001100'))->toBe('-0.0011');

        expect(Math::trimTrailingZeros("11-0000000000", '-'))->toBe('11');
        expect(Math::trimTrailingZeros("11-2100000000", '-'))->toBe('11-21');
        expect(Math::trimTrailingZeros("-11-2100000000", '-'))->toBe('-11-21');
    });

    test('decimalPlaceNumber method', function () {

        expect(Math::decimalPlaceNumber(0))->toBe(0);
        expect(Math::decimalPlaceNumber(11))->toBe(0);
        expect(Math::decimalPlaceNumber(11.01))->toBe(2);
        expect(Math::decimalPlaceNumber(0.000000000100))->toBe(10);
        expect(Math::decimalPlaceNumber('0.00000000100'))->toBe(9);

        expect(Math::decimalPlaceNumber(1.1e-8))->toBe(9);
        expect(Math::decimalPlaceNumber(0.1e-8))->toBe(9);
        expect(Math::decimalPlaceNumber(0.e-8))->toBe(0);

        expect(Math::decimalPlaceNumber('.1e-8'))->toBe(9);
        expect(Math::decimalPlaceNumber('-.1e-8'))->toBe(9);

        expect(Math::decimalPlaceNumber('0-0123', '-'))->toBe(4);
        expect(Math::decimalPlaceNumber('-0-0123', '-'))->toBe(4);

        expect(Math::decimalPlaceNumber('test'))->toBe(0);
    });

    test('number method', function () {

        expect(Math::number('test'))->toBe('test');
        expect(Math::number('foo.bar'))->toBe('foo.bar');
        expect(Math::number(''))->toBe('0');
        expect(Math::number(0))->toBe('0');

        expect(Math::number(0.001))->toBe('0.001');
        expect(Math::setPrecision(precision: 2)->number(0.001))->toBe('0');
        expect(math(precision: 2)->number(0.001))->toBe('0');

        expect(Math::number('.44015042'))->toBe('0.44015042');
        expect(Math::setPrecision(precision: 4)->number(0.44015042))->toBe('0.4401');
        expect(number('.44015042'))->toBe('0.44015042');
        expect(math(precision: 4)->number(0.44015042))->toBe('0.4401');

        expect(Math::number(11.000001000))->toBe('11.000001');
        expect(Math::number(-11.000001000))->toBe('-11.000001');

        expect(Math::number(1e8))->toBe('100000000');
        expect(Math::number(-1e8))->toBe('-100000000');

        expect(Math::number(1.1e+8))->toBe('110000000');
        expect(Math::number(.1e+8))->toBe('10000000');

        expect(Math::number(1.101e-5))->toBe('0.00001101');
        expect(Math::number(-0.101e-5))->toBe('-0.00000101');

        expect(Math::number(1.1E+20))->toBe('110000000000000000000');
        expect(Math::number(1.1E-20))->toBe('0'); // the decimal numbers is very more than precision
    });

    test('numberFormat method', function () {

        expect(Math::numberFormat(number: 'test'))->toBe('test');
        expect(Math::numberFormat(number: ''))->toBe('0');
        expect(Math::numberFormat(number: 0))->toBe('0');
        expect(Math::numberFormat(number: 1.1e-20))->toBe("0"); // the decimal numbers is very more than precision
        expect(Math::numberFormat(number: 1.1e-8))->toBe("0.000000011");
        expect(Math::numberFormat(number: 1.1e+8))->toBe("110,000,000");

        expect(Math::numberFormat(number: 5000000))->toBe("5,000,000");
        expect(Math::numberFormat(number: 5000000.50))->toBe("5,000,000.5");
        expect(Math::numberFormat(number: 5000000.5))->toBe("5,000,000.5");
        expect(Math::numberFormat(number: 5000000.05))->toBe("5,000,000.05");
        expect(Math::numberFormat(number: 5000000.015))->toBe("5,000,000.015");
        expect(Math::numberFormat(number: 5000000.0150))->toBe("5,000,000.015");
        expect(Math::numberFormat(number: 5000000.0150100))->toBe("5,000,000.01501");

        expect(Math::numberFormat(number: 1.1e+20, thousandsSeparator: "|"))->toBe("110|000|000|000|000|000|000");
        expect(Math::numberFormat(number: '5,000,000.0150100', decimalSeparator: '/', thousandsSeparator: " "))->toBe("5 000 000/01501");
        expect(Math::numberFormat(number: '1234_01230', decimalSeparator: "_"))->toBe("1,234_0123");
        expect(Math::numberFormat(number: '-1234-01230', decimalSeparator: "-"))->toBe("-1,234-0123");
    });

    test('sum method', function () {

        try {

            Math::sum(1);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect($e->getCode())->toBe(10101);
            expect($e->getLevel())->toBe('error');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'  => 'bcadd',
                'args'  => [1]
            ]);
        }

        try {

            Math::sum(1, null);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
            expect($e->getCode())->toBe(10102);
            expect($e->getLevel())->toBe('error');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'  => 'bcadd',
                'args'  => [1, null]
            ]);
        }

        expect(Math::setPrecision(precision: 0)->sum(5.599, 5.499))->toBe('11');
        expect(Math::setPrecision(precision: 5)->sum(5.599, 5.499))->toBe('11.098');
        expect(Math::setPrecision(precision: 2)->sum(5.599, -5.499))->toBe('0.1');
        expect(Math::sum(1.1e+8, 1.1e-8))->toBe("110000000.000000011");
        expect(Math::sum(0, 0))->toBe('0');
        expect(Math::sum(0.0, 0.0))->toBe('0');
        expect(Math::sum(1.1e+20, 1.1e-8))->toBe('110000000000000000000.000000011');
        expect(Math::sum('0', '1234567891234567889999999'))->toBe('1234567891234567889999999');
        expect(Math::sum('1234567891234567889999999', '0'))->toBe('1234567891234567889999999');
        expect(Math::sum('1234567891234567889999999', '-1234567891234567889999999'))->toBe('0');
        expect(Math::sum('-1234567891234567889999999', '1234567891234567889999999'))->toBe('0');
        expect(Math::sum('1234567891234567889999999.11', '1234567891234567889999999.0011'))->toBe('2469135782469135779999998.1111');
        expect(Math::sum('1234567891234567889999999.00000000011', '1234567891234567889999999.00000000019'))->toBe('2469135782469135779999998.0000000003');


        expect(math(precision: 0)->sum(5.599, 5.499))->toBe('11');
        expect(math(precision: 5)->sum(5.599, 5.499))->toBe('11.098');
        expect(math(precision: 2)->sum(5.599, -5.499))->toBe('0.1');
        expect(sum(1.1e+8, 1.1e-8))->toBe("110000000.000000011");
        expect(sum(0, 0))->toBe('0');
        expect(sum(0.0, 0.0))->toBe('0');
        expect(sum(1.1e+20, 1.1e-8))->toBe('110000000000000000000.000000011');
        expect(sum('0', '1234567891234567889999999'))->toBe('1234567891234567889999999');
        expect(sum('1234567891234567889999999', '0'))->toBe('1234567891234567889999999');
        expect(sum('1234567891234567889999999', '-1234567891234567889999999'))->toBe('0');
        expect(sum('-1234567891234567889999999', '1234567891234567889999999'))->toBe('0');
        expect(sum('1234567891234567889999999.11', '1234567891234567889999999.0011'))->toBe('2469135782469135779999998.1111');
        expect(sum('1234567891234567889999999.00000000011', '1234567891234567889999999.00000000019'))->toBe('2469135782469135779999998.0000000003');
    });

    test('subtract method', function () {

        expect(Math::subtract(5, 6))->toBe('-1');

        expect(Math::setPrecision(precision: 0)->subtract(5.599, 5.499))->toBe('0');
        expect(Math::setPrecision(precision: 4)->subtract(5.599, 5.499))->toBe('0.1');

        expect(Math::subtract(1.1e+8, 1.1e-8))->toBe('109999999.999999989');
        expect(Math::subtract(1.1e+20, 1.1e-8))->toBe('109999999999999999999.999999989');

        expect(Math::subtract(0, 0))->toBe('0');
        expect(Math::subtract(0.0, 0.0))->toBe('0');

        expect(Math::subtract('0', '1234567891234567889999999'))->toBe('-1234567891234567889999999');
        expect(Math::subtract('1234567891234567889999999', '0'))->toBe('1234567891234567889999999');
        expect(Math::subtract('1234567891234567889999999', '1234567891234567889999999'))->toBe('0');
        expect(Math::subtract('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-2469135782469135779999998');
        expect(Math::subtract('1234567891234567889999999.11', '-1234567891234567889999999.0011'))->toBe('2469135782469135779999998.1111');
        expect(Math::subtract('1234567891234567889999999.00000000011', '-1234567891234567889999999.00000000019'))->toBe('2469135782469135779999998.0000000003');
    });

    test('multiply method', function () {
        expect(Math::setPrecision(precision: 1)->multiply(5.125, 6.11))->toBe('31.3');
        expect(Math::setPrecision(precision: 2)->multiply(5.123456789, 6.123456789))->toBe('31.37');
        expect(Math::multiply(5.125, 6.11))->toBe('31.31375');
        expect(Math::multiply(1.1e+8, 1.1e-8))->toBe('1.21');
        expect(Math::multiply('1234567891234567889999999', 0))->toBe('0');
        expect(Math::multiply(0, '1234567891234567889999999'))->toBe('0');
        expect(Math::multiply('1234567891234567889999999', 1))->toBe('1234567891234567889999999');
        expect(Math::multiply(1, '1234567891234567889999999'))->toBe('1234567891234567889999999');
        expect(Math::multiply('1234567891234567889999999', '-1234567891234567889999999'))->toBe('-1524157878067367851562259605883269630864220000001');
        expect(Math::multiply('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-1524157878067367851562259605883269630864220000001');
        expect(Math::multiply('1234567891234567889999999', '1234567891234567889999999'))->toBe('1524157878067367851562259605883269630864220000001');
        expect(Math::multiply(0, 0))->toBe('0');
        expect(Math::multiply(0.0, 0.0))->toBe('0');


        expect(math(precision: 1)->multiply(5.125, 6.11))->toBe('31.3');
        expect(math(precision: 2)->multiply(5.123456789, 6.123456789))->toBe('31.37');
        expect(multiply(5.125, 6.11))->toBe('31.31375');
        expect(multiply(1.1e+8, 1.1e-8))->toBe('1.21');
        expect(multiply('1234567891234567889999999', 0))->toBe('0');
        expect(multiply(0, '1234567891234567889999999'))->toBe('0');
        expect(multiply('1234567891234567889999999', 1))->toBe('1234567891234567889999999');
        expect(multiply(1, '1234567891234567889999999'))->toBe('1234567891234567889999999');
        expect(multiply('1234567891234567889999999', '-1234567891234567889999999'))->toBe('-1524157878067367851562259605883269630864220000001');
        expect(multiply('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-1524157878067367851562259605883269630864220000001');
        expect(multiply('1234567891234567889999999', '1234567891234567889999999'))->toBe('1524157878067367851562259605883269630864220000001');
        expect(multiply(0, 0))->toBe('0');
        expect(multiply(0.0, 0.0))->toBe('0');
    });

    test('divide method', function () {

        try {

            Math::divide(5, 0);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
            expect($e->getCode())->toBe(10103);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'  => 'bcdiv',
                'args'  => [5, 0]
            ]);
        }

        expect(Math::divide(1, 0.5))->toBe('2');
        expect(Math::setPrecision(precision: 0)->divide(50, 0.4354))->toBe('114');
        expect(Math::setPrecision(precision: 0)->divide(361, 1.15))->toBe('313');
        expect(Math::divide(5, 6))->toBe('0.833333333333');
        expect(Math::divide(10, 3))->toBe('3.333333333333');
        expect(Math::divide(1, 1000000000))->toBe('0.000000001');
        expect(Math::divide(1, 111))->toBe('0.009009009009');
        expect(Math::divide(1.1e+8, 1.1e-8))->toBe('10000000000000000');
        expect(Math::divide('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-1');

        expect(divide(1, 0.5))->toBe('2');
        expect(math(precision: 0)->divide(50, 0.4354))->toBe('114');
        expect(math(precision: 0)->divide(361, 1.15))->toBe('313');
        expect(divide(5, 6))->toBe('0.833333333333');
        expect(divide(10, 3))->toBe('3.333333333333');
        expect(divide(1, 1000000000))->toBe('0.000000001');
        expect(divide(1, 111))->toBe('0.009009009009');
        expect(divide(1.1e+8, 1.1e-8))->toBe('10000000000000000');
        expect(divide('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-1');
    });


    test('modulus method', function () {

        try {

            Math::modulus(5, 0);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
            expect($e->getCode())->toBe(10103);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'  => 'bcmod',
                'args'  =>  [5, 0]
            ]);
        }

        expect(Math::modulus(12, 5))->toBe('2');
        expect(Math::modulus(5, 6))->toBe('5');
        expect(Math::modulus(1.1e+8, 1.1e-8))->toBe('0');
    });

    test('power method', function () {

        expect(Math::power(2, -3))->toBe('0.125');
        expect(Math::power(2, -2))->toBe('0.25');
        expect(Math::power(2, 3))->toBe('8');
        expect(Math::power(2, 0))->toBe('1');
        expect(Math::power(0, 2))->toBe('0');
        expect(Math::power(0, 0))->toBe('1');
        expect(Math::power(1, 20))->toBe('1');
        expect(Math::power('1234567891234567889999999', 2))->toBe('1524157878067367851562259605883269630864220000001');
        expect(Math::power('1234567891234567889999999', 3))->toBe('1881676377434183981909558127466713752376807174114547646517403703669999999');
    });

    test('sqrt method', function () {

        expect(Math::sqrt(0))->toBe('0');
        expect(Math::sqrt(1))->toBe('1');
        expect(Math::sqrt(2))->toBe('1.414213562373');
        expect(Math::sqrt(3))->toBe('1.732050807568');
        expect(Math::sqrt(4))->toBe('2');
        expect(Math::sqrt(9))->toBe('3');
        expect(Math::sqrt(16))->toBe('4');
        expect(Math::sqrt('1524157878067367851562259605883269630864220000001'))->toBe('1234567891234567889999999');
        expect(fn() => Math::sqrt(-2))->toThrow(ValueError::class);
    });

    test('roundUp method', function () {

        expect(Math::roundUp(0))->toBe('0');
        expect(Math::roundUp(1))->toBe('1');
        expect(Math::roundUp(1.1))->toBe('2');
        expect(Math::roundUp(-1.1))->toBe('-1');
        expect(Math::roundUp(1.9))->toBe('2');
        expect(Math::roundUp(-1.9))->toBe('-1');
        expect(Math::roundUp(1.1e+8))->toBe('110000000');
        expect(Math::roundUp(1.1e-8))->toBe('1');

        expect(roundUp(0))->toBe('0');
        expect(roundUp(1))->toBe('1');
        expect(roundUp(1.1))->toBe('2');
        expect(roundUp(-1.1))->toBe('-1');
        expect(roundUp(1.9))->toBe('2');
        expect(roundUp(-1.9))->toBe('-1');
        expect(roundUp(1.1e+8))->toBe('110000000');
        expect(roundUp(1.1e-8))->toBe('1');
    });

    test('roundDown method', function () {

        expect(Math::roundDown(0))->toBe('0');
        expect(Math::roundDown(1))->toBe('1');
        expect(Math::roundDown(1.1))->toBe('1');
        expect(Math::roundDown(-1.1))->toBe('-2');
        expect(Math::roundDown(1.9))->toBe('1');
        expect(Math::roundDown(-1.9))->toBe('-2');
        expect(Math::roundDown(1.1e+8))->toBe('110000000');
        expect(Math::roundDown(1.1e-8))->toBe('0');

        expect(roundDown(0))->toBe('0');
        expect(roundDown(1))->toBe('1');
        expect(roundDown(1.1))->toBe('1');
        expect(roundDown(-1.1))->toBe('-2');
        expect(roundDown(1.9))->toBe('1');
        expect(roundDown(-1.9))->toBe('-2');
        expect(roundDown(1.1e+8))->toBe('110000000');
        expect(roundDown(1.1e-8))->toBe('0');
    });

    test('roundClose method', function () {

        expect(Math::roundClose(0))->toBe('0');
        expect(Math::roundClose(1))->toBe('1');
        expect(Math::roundClose(1.1))->toBe('1');
        expect(Math::roundClose(-1.1))->toBe('-1');
        expect(Math::roundClose(1.9))->toBe('2');
        expect(Math::roundClose(-1.9))->toBe('-2');
        expect(Math::roundClose(5.045, 2))->toBe('5.05');
        expect(Math::roundClose(1.1e+8))->toBe('110000000');
        expect(Math::roundClose(1.1e-8))->toBe('0');

        expect(roundClose(0))->toBe('0');
        expect(roundClose(1))->toBe('1');
        expect(roundClose(1.1))->toBe('1');
        expect(roundClose(-1.1))->toBe('-1');
        expect(roundClose(1.9))->toBe('2');
        expect(roundClose(-1.9))->toBe('-2');
        expect(roundClose(5.045, 2))->toBe('5.05');
        expect(roundClose(1.1e+8))->toBe('110000000');
        expect(roundClose(1.1e-8))->toBe('0');
    });


    test('greaterThan method', function () {

        expect(Math::greaterThan(0, 0))->toBeFalse();
        expect(Math::greaterThan(-1, 0))->toBeFalse();
        expect(Math::greaterThan(0, 5))->toBeFalse();
        expect(Math::greaterThan(0, -1))->toBeTrue();
        expect(Math::setPrecision(precision: 1)->greaterThan(1.11, 1.112))->toBeFalse();
        expect(Math::greaterThan(1.1e+8, 1.1e-8))->toBeTrue();
        expect(Math::greaterThan(1.1e+8, 1.1e+8))->toBeFalse();
        expect(Math::greaterThan(1.1e-8, 1.1e+8))->toBeFalse();

        expect(greaterThan(0, 0))->toBeFalse();
        expect(greaterThan(-1, 0))->toBeFalse();
        expect(greaterThan(0, 5))->toBeFalse();
        expect(greaterThan(0, -1))->toBeTrue();
        expect(math(precision: 1)->greaterThan(1.11, 1.112))->toBeFalse();
        expect(greaterThan(1.1e+8, 1.1e-8))->toBeTrue();
        expect(greaterThan(1.1e+8, 1.1e+8))->toBeFalse();
        expect(greaterThan(1.1e-8, 1.1e+8))->toBeFalse();
    });

    test('greaterThanOrEqual method', function () {
        expect(Math::greaterThanOrEqual(0, 0))->toBeTrue();
        expect(Math::greaterThanOrEqual(-1, 0))->toBeFalse();
        expect(Math::greaterThanOrEqual(0, 5))->toBeFalse();
        expect(Math::greaterThanOrEqual(0, -1))->toBeTrue();
        expect(math(precision: 1)->greaterThanOrEqual(1.112, 1.112))->toBeTrue();
        expect(Math::greaterThanOrEqual(1.1e+8, 1.1e-8))->toBeTrue();
        expect(Math::greaterThanOrEqual(1.1e+8, 1.1e+8))->toBeTrue();
        expect(Math::greaterThanOrEqual(1.1e-8, 1.1e+8))->toBeFalse();

        expect(greaterThanOrEqual(0, 0))->toBeTrue();
        expect(greaterThanOrEqual(-1, 0))->toBeFalse();
        expect(greaterThanOrEqual(0, 5))->toBeFalse();
        expect(greaterThanOrEqual(0, -1))->toBeTrue();
        expect(math(precision: 1)->greaterThanOrEqual(1.113, 1.112))->toBeTrue();
        expect(greaterThanOrEqual(1.1e+8, 1.1e-8))->toBeTrue();
        expect(greaterThanOrEqual(1.1e+8, 1.1e+8))->toBeTrue();
        expect(greaterThanOrEqual(1.1e-8, 1.1e+8))->toBeFalse();
    });

    test('lessThan method', function () {
        expect(Math::lessThan(0, 0))->toBeFalse();
        expect(Math::lessThan(-1, 0))->toBeTrue();
        expect(Math::lessThan(0, 5))->toBeTrue();
        expect(Math::lessThan(0, -1))->toBeFalse();
        expect(Math::setPrecision(precision: 1)->lessThan(1.112, 1.11))->toBeFalse();
        expect(Math::lessThan(1.1e-8, 1.1e+8))->toBeTrue();
        expect(Math::lessThan(1.1e+8, 1.1e+8))->toBeFalse();
        expect(Math::lessThan(1.1e+8, 1.1e-8))->toBeFalse();

        expect(lessThan(0, 0))->toBeFalse();
        expect(lessThan(-1, 0))->toBeTrue();
        expect(lessThan(0, 5))->toBeTrue();
        expect(lessThan(0, -1))->toBeFalse();
        expect(math(precision: 1)->lessThan(1.112, 1.11))->toBeFalse();
        expect(lessThan(1.1e-8, 1.1e+8))->toBeTrue();
        expect(lessThan(1.1e+8, 1.1e+8))->toBeFalse();
        expect(lessThan(1.1e+8, 1.1e-8))->toBeFalse();
    });

    test('lessThanOrEqual method', function () {

        expect(Math::lessThanOrEqual(0, 0))->toBeTrue();
        expect(Math::lessThanOrEqual(-1, 0))->toBeTrue();
        expect(Math::lessThanOrEqual(0, 5))->toBeTrue();
        expect(Math::lessThanOrEqual(0, -1))->toBeFalse();
        expect(Math::setPrecision(precision: 1)->lessThanOrEqual(1.11, 1.11))->toBeTrue();
        expect(Math::lessThanOrEqual(1.1e-8, 1.1e+8))->toBeTrue();
        expect(Math::lessThanOrEqual(1.1e+8, 1.1e+8))->toBeTrue();
        expect(Math::lessThanOrEqual(1.1e+8, 1.1e-8))->toBeFalse();

        expect(lessThanOrEqual(0, 0))->toBeTrue();
        expect(lessThanOrEqual(-1, 0))->toBeTrue();
        expect(lessThanOrEqual(0, 5))->toBeTrue();
        expect(lessThanOrEqual(0, -1))->toBeFalse();
        expect(math(precision: 1)->lessThanOrEqual(1.11, 1.11))->toBeTrue();
        expect(lessThanOrEqual(1.1e-8, 1.1e+8))->toBeTrue();
        expect(lessThanOrEqual(1.1e+8, 1.1e+8))->toBeTrue();
        expect(lessThanOrEqual(1.1e+8, 1.1e-8))->toBeFalse();
    });

    test('equal method', function () {
        expect(Math::equal(0, 0))->toBeTrue();
        expect(Math::equal(-1, 0))->toBeFalse();
        expect(Math::equal(0, 5))->toBeFalse();
        expect(Math::equal(0, -1))->toBeFalse();
        expect(Math::setPrecision(precision: 1)->equal(1.112, 1.113))->toBeFalse();
        expect(Math::equal(1.1e-8, 1.1e+8))->toBeFalse();
        expect(Math::equal(1.1e+8, 1.1e+8))->toBeTrue();
        expect(Math::equal(1.1e+8, 1.1e-8))->toBeFalse();

        expect(equal(0, 0))->toBeTrue();
        expect(equal(-1, 0))->toBeFalse();
        expect(equal(0, 5))->toBeFalse();
        expect(equal(0, -1))->toBeFalse();
        expect(math(precision: 1)->equal(1.112, 1.113))->toBeFalse();
        expect(equal(1.1e-8, 1.1e+8))->toBeFalse();
        expect(equal(1.1e+8, 1.1e+8))->toBeTrue();
        expect(equal(1.1e+8, 1.1e-8))->toBeFalse();

        expect(Math::notEqual(0, 0))->toBeFalse();
        expect(Math::notEqual(-1, 0))->toBeTrue();
        expect(Math::notEqual(0, 5))->toBeTrue();
        expect(Math::notEqual(0, -1))->toBeTrue();
        expect(Math::setPrecision(precision: 1)->notEqual(1.112, 1.113))->toBeTrue();
        expect(Math::notEqual(1, 2))->toBeTrue();
        expect(Math::notEqual(2, 2))->toBeFalse();

        expect(notEqual(0, 0))->toBeFalse();
        expect(notEqual(-1, 0))->toBeTrue();
        expect(notEqual(0, 5))->toBeTrue();
        expect(notEqual(0, -1))->toBeTrue();
        expect(math(precision: 1)->notEqual(1.112, 1.113))->toBeTrue();
        expect(notEqual(1, 2))->toBeTrue();
        expect(notEqual(2, 2))->toBeFalse();
    });
});
