<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Facades\Math;
use RoundingMode;

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

        try {

            Math::convertScientificNumber(1.1E+9999);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
            expect($e->getCode())->toBe(10105);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'          => 'convertScientificNumber',
                'operand'       => INF,
            ]);
        }

        try {

            Math::convertScientificNumber('1.1E+9999');

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
            expect($e->getCode())->toBe(10105);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'          => 'convertScientificNumber',
                'operand'       => '1.1E+9999',
            ]);
        }

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

    test('countDecimalPlaces method', function () {

        expect(Math::countDecimalPlaces(0))->toBe(0);
        expect(Math::countDecimalPlaces(11))->toBe(0);
        expect(Math::countDecimalPlaces(11.01))->toBe(2);
        expect(Math::countDecimalPlaces(0.000000000100))->toBe(10);
        expect(Math::countDecimalPlaces('0.00000000100'))->toBe(9);

        expect(Math::countDecimalPlaces(1.1e-8))->toBe(9);
        expect(Math::countDecimalPlaces(0.1e-8))->toBe(9);
        expect(Math::countDecimalPlaces(0.e-8))->toBe(0);

        expect(Math::countDecimalPlaces('.1e-8'))->toBe(9);
        expect(Math::countDecimalPlaces('-.1e-8'))->toBe(9);

        expect(Math::countDecimalPlaces('0-0123', '-'))->toBe(4);
        expect(Math::countDecimalPlaces('-0-0123', '-'))->toBe(4);

        expect(Math::countDecimalPlaces('test'))->toBe(0);
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
        expect(Math::setPrecision(precision: 3)->numberFormat(number: 5000000.0150100))->toBe("5,000,000.015");
        expect(math(precision: 2)->numberFormat(number: 5000000.0150100))->toBe("5,000,000.01");

        expect(Math::numberFormat(number: 1.1e+20, thousandsSeparator: "|"))->toBe("110|000|000|000|000|000|000");

        expect(Math::numberFormat(number: '5,000,000.0150100', decimalSeparator: '/', thousandsSeparator: " "))->toBe("5 000 000/01501");

        expect(Math::numberFormat(number: '1234_01230', decimalSeparator: "_"))->toBe("1,234_0123");

        expect(Math::numberFormat(number: '-1234-01230', decimalSeparator: "-"))->toBe("-1,234-0123");
        expect(numberFormat(number: '-1234-01230', decimalSeparator: "-"))->toBe("-1,234-0123");
    });

    test('exceptions that calc throws', function () {

        try {

            Math::sum(1);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect($e->getCode())->toBe(10102);
            expect($e->getLevel())->toBe('error');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'      => 'bcadd',
                'operand'   => [1]
            ]);
        }

        try {

            Math::sum([1]);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect($e->getCode())->toBe(10102);
            expect($e->getLevel())->toBe('error');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'      => 'bcadd',
                'operand'   => [[1]]
            ]);
        }

        expect(fn() => Math::subtract(1))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
        expect(fn() => Math::multiply(1))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
        expect(fn() => Math::divide(1))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
        expect(fn() => Math::modulus(1))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');

        expect(fn() => Math::subtract([1]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
        expect(fn() => Math::multiply([1]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
        expect(fn() => Math::divide([1]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
        expect(fn() => Math::modulus([1]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');

        try {

            Math::sum(1, null);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
            expect($e->getCode())->toBe(10103);
            expect($e->getLevel())->toBe('error');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'      => 'bcadd',
                'operand'   => [1, null]
            ]);
        }


        expect(fn() => Math::sum(1, true))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::sum(2, 'test'))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::sum(2, [1, 2, 3]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::sum([1, true]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::sum([1, 'test']))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::sum([1, [1, 2]]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');


        expect(fn() => Math::subtract(1, true))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::subtract(2, 'test'))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::subtract(2, [1, 2, 3]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::subtract([1, true]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::subtract([1, 'test']))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::subtract([1, [1, 2]]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');


        expect(fn() => Math::multiply(1, true))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::multiply(2, 'test'))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::multiply(2, [1, 2, 3]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::multiply([1, true]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::multiply([1, 'test']))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::multiply([1, [1, 2]]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');


        expect(fn() => Math::divide(1, true))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::divide(2, 'test'))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::divide(2, [1, 2, 3]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::divide([1, true]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::divide([1, 'test']))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::divide([1, [1, 2]]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');


        expect(fn() => Math::modulus(1, true))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::modulus(2, 'test'))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::modulus(2, [1, 2, 3]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::modulus([1, true]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::modulus([1, 'test']))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
        expect(fn() => Math::modulus([1, [1, 2]]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionInvalidArgumentType');


        try {

            Math::divide(5, 0);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
            expect($e->getCode())->toBe(10104);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'      => 'bcdiv',
                'operand'   => [5, 0]
            ]);
        }

        try {

            Math::modulus(5, 0);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
            expect($e->getCode())->toBe(10104);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'      => 'bcmod',
                'operand'   => [5, 0]
            ]);
        }

        expect(fn() => Math::modulus(5, 0))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionDivisionByZero');

        expect(fn() => Math::divide([5, 0, 1]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionDivisionByZero');
        expect(fn() => Math::modulus([5, 0, 1]))->toThrow(FooinoException::class, 'msg.mathCalculationExceptionDivisionByZero');
    });

    test('sum method', function () {

        expect(Math::sum(0, 0))->toBe('0');
        expect(Math::sum(0.0, 0.0))->toBe('0');

        expect(Math::setPrecision(precision: 0)->sum(5.599, 5.499))->toBe('11');
        expect(Math::setPrecision(precision: 5)->sum(5.599, 5.499))->toBe('11.098');
        expect(Math::setPrecision(precision: 2)->sum(5.599, -5.499))->toBe('0.1');

        expect(Math::sum(1.1e+8, 1.1e-8))->toBe("110000000.000000011");
        expect(Math::sum(1.1e+20, 1.1e-8))->toBe('110000000000000000000.000000011');

        expect(Math::sum('0', '1234567891234567889999999'))->toBe('1234567891234567889999999');
        expect(Math::sum('1234567891234567889999999', '0'))->toBe('1234567891234567889999999');
        expect(Math::sum('1234567891234567889999999.000000000011', '1234567891234567889999999.000000000009'))->toBe('2469135782469135779999998.00000000002');

        expect(Math::sum('1234567891234567889999999', '-1234567891234567889999999'))->toBe('0');
        expect(Math::sum('-1234567891234567889999999', '1234567891234567889999999'))->toBe('0');
        expect(Math::sum('1234567891234567889999999.00000000011', '1234567891234567889999999.00000000019'))->toBe('2469135782469135779999998.0000000003');

        expect(sum([0, 0]))->toBe('0');
        expect(sum([0.0, 0.0]))->toBe('0');
        expect(sum(range(1, 10)))->toBe('55');
        expect(sum(1, 2, 3, 4))->toBe('10');

        expect(math(precision: 0)->sum([5.599, 5.499]))->toBe('11');
        expect(math(precision: 5)->sum([5.599, 5.499]))->toBe('11.098');
        expect(math(precision: 2)->sum([5.599, -5.499]))->toBe('0.1');

        expect(sum([1.1e+8, 1.1e-8]))->toBe("110000000.000000011");
        expect(sum([1.1e+20, 1.1e-8]))->toBe('110000000000000000000.000000011');

        expect(sum(['0', '1234567891234567889999999']))->toBe('1234567891234567889999999');
        expect(sum(['1234567891234567889999999', '0']))->toBe('1234567891234567889999999');
        expect(sum(['1234567891234567889999999.000000000011', '1234567891234567889999999.000000000009']))->toBe('2469135782469135779999998.00000000002');

        expect(sum(['1234567891234567889999999', '-1234567891234567889999999']))->toBe('0');
        expect(sum(['-1234567891234567889999999', '1234567891234567889999999']))->toBe('0');
        expect(sum(['1234567891234567889999999.00000000011', '1234567891234567889999999.00000000019']))->toBe('2469135782469135779999998.0000000003');
    });

    test('subtract method', function () {

        expect(Math::subtract(0, 0))->toBe('0');
        expect(Math::subtract(0.0, 0.0))->toBe('0');

        expect(Math::subtract(5, 6))->toBe('-1');

        expect(Math::setPrecision(precision: 0)->subtract(5.599, 5.499))->toBe('0');
        expect(Math::setPrecision(precision: 4)->subtract(5.599, 5.499))->toBe('0.1');

        expect(Math::subtract(1.1e+8, 1.1e-8))->toBe('109999999.999999989');
        expect(Math::subtract(1.1e+20, 1.1e-8))->toBe('109999999999999999999.999999989');

        expect(Math::subtract('0', '1234567891234567889999999'))->toBe('-1234567891234567889999999');
        expect(Math::subtract('1234567891234567889999999', '0'))->toBe('1234567891234567889999999');
        expect(Math::subtract('1234567891234567889999999', '1234567891234567889999999'))->toBe('0');

        expect(Math::subtract('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-2469135782469135779999998');
        expect(Math::subtract('1234567891234567889999999.11', '-1234567891234567889999999.0011'))->toBe('2469135782469135779999998.1111');

        expect(Math::subtract('1234567891234567889999999.000000000011', '-1234567891234567889999999.000000000019'))->toBe('2469135782469135779999998.00000000003');

        expect(subtract([0, 0]))->toBe('0');
        expect(subtract([0.0, 0.0]))->toBe('0');
        expect(subtract(range(1, 10)))->toBe('-53');
        expect(subtract(1, 2, 3, 4))->toBe('-8');

        expect(math(precision: 0)->subtract([5.599, 5.499]))->toBe('0');
        expect(math(precision: 4)->subtract([5.599, 5.499]))->toBe('0.1');

        expect(subtract([1.1e+8, 1.1e-8]))->toBe('109999999.999999989');
        expect(subtract([1.1e+20, 1.1e-8]))->toBe('109999999999999999999.999999989');

        expect(subtract(['0', '1234567891234567889999999']))->toBe('-1234567891234567889999999');
        expect(subtract(['1234567891234567889999999', '0']))->toBe('1234567891234567889999999');
        expect(subtract(['1234567891234567889999999', '1234567891234567889999999']))->toBe('0');

        expect(subtract(['-1234567891234567889999999', '1234567891234567889999999']))->toBe('-2469135782469135779999998');
        expect(subtract(['1234567891234567889999999.11', '-1234567891234567889999999.0011']))->toBe('2469135782469135779999998.1111');

        expect(subtract(['1234567891234567889999999.000000000011', '-1234567891234567889999999.000000000019']))->toBe('2469135782469135779999998.00000000003');
    });

    test('multiply method', function () {

        expect(Math::multiply(0, 0))->toBe('0');
        expect(Math::multiply(0.0, 0.0))->toBe('0');

        expect(Math::multiply(5.125, 6.11))->toBe('31.31375');
        expect(Math::setPrecision(precision: 1)->multiply(5.125, 6.11))->toBe('31.3');

        expect(Math::setPrecision(precision: 2)->multiply(5.123456789, 6.123456789))->toBe('31.37');

        expect(Math::multiply(1.1e+8, 1.1e-8))->toBe('1.21');

        expect(Math::multiply('1234567891234567889999999', 0))->toBe('0');
        expect(Math::multiply(0, '1234567891234567889999999'))->toBe('0');

        expect(Math::multiply('1234567891234567889999999', 1))->toBe('1234567891234567889999999');
        expect(Math::multiply(1, '1234567891234567889999999'))->toBe('1234567891234567889999999');

        expect(Math::multiply('1234567891234567889999999', '-1234567891234567889999999'))->toBe('-1524157878067367851562259605883269630864220000001');
        expect(Math::multiply('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-1524157878067367851562259605883269630864220000001');
        expect(Math::multiply('1234567891234567889999999', '1234567891234567889999999'))->toBe('1524157878067367851562259605883269630864220000001');

        expect(multiply([0, 0]))->toBe('0');
        expect(multiply([0.0, 0.0]))->toBe('0');

        expect(multiply(range(0, 10)))->toBe('0');
        expect(multiply(range(1, 10)))->toBe('3628800');
        expect(multiply(1, 2, 3, 4))->toBe('24');

        expect(multiply([5.125, 6.11]))->toBe('31.31375');
        expect(math(precision: 1)->multiply([5.125, 6.11]))->toBe('31.3');

        expect(math(precision: 2)->multiply([5.123456789, 6.123456789]))->toBe('31.37');

        expect(multiply([1.1e+8, 1.1e-8]))->toBe('1.21');

        expect(multiply(['1234567891234567889999999', 0]))->toBe('0');
        expect(multiply([0, '1234567891234567889999999']))->toBe('0');

        expect(multiply(['1234567891234567889999999', 1]))->toBe('1234567891234567889999999');
        expect(multiply([1, '1234567891234567889999999']))->toBe('1234567891234567889999999');

        expect(multiply(['1234567891234567889999999', '-1234567891234567889999999']))->toBe('-1524157878067367851562259605883269630864220000001');
        expect(multiply(['-1234567891234567889999999', '1234567891234567889999999']))->toBe('-1524157878067367851562259605883269630864220000001');
        expect(multiply(['1234567891234567889999999', '1234567891234567889999999']))->toBe('1524157878067367851562259605883269630864220000001');
    });

    test('divide method', function () {

        expect(Math::divide(0, 10))->toBe('0');

        expect(Math::divide(1, -0.5))->toBe('-2');

        expect(math::divide(50, 0.4354))->toBe('114.836931557188');
        expect(math::divide(361, 1.15))->toBe('313.91304347826');

        expect(Math::setPrecision(precision: 0)->divide(50, 0.4354))->toBe('114');
        expect(Math::setPrecision(precision: 0)->divide(361, 1.15))->toBe('313');

        expect(Math::divide(-5, 6))->toBe('-0.833333333333');
        expect(Math::divide(10, 3))->toBe('3.333333333333');

        expect(Math::divide(1, 1E12))->toBe('0.000000000001');
        expect(Math::divide(1, 111))->toBe('0.009009009009');

        expect(Math::divide(1.1e+8, 1.1e-8))->toBe('10000000000000000');

        expect(Math::divide('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-1');


        expect(divide([0, 10]))->toBe('0');

        expect(divide(range(0, 10)))->toBe('0');
        expect(divide(range(1, 10)))->toBe('0.000000275573');
        expect(divide(range(1, 100)))->toBe('0');
        expect(divide(range(10, 1)))->toBe('0.000027557319');

        expect(divide(10, 2, 5, 2))->toBe('0.5');
        expect(divide(0, 2, 5, 2))->toBe('0');

        expect(divide([1, -0.5]))->toBe('-2');

        expect(divide(50, 0.4354))->toBe('114.836931557188');
        expect(divide(361, 1.15))->toBe('313.91304347826');

        expect(math(precision: 0)->divide([50, 0.4354]))->toBe('114');
        expect(math(precision: 0)->divide([361, 1.15]))->toBe('313');

        expect(divide([-5, 6]))->toBe('-0.833333333333');
        expect(divide([10, 3]))->toBe('3.333333333333');

        expect(divide([1, 1E12]))->toBe('0.000000000001');
        expect(divide([1, 111]))->toBe('0.009009009009');

        expect(divide([1.1e+8, 1.1e-8]))->toBe('10000000000000000');

        expect(divide(['-1234567891234567889999999', '1234567891234567889999999']))->toBe('-1');
    });

    test('modulus method', function () {

        expect(Math::modulus(0, 5))->toBe('0');

        expect(Math::modulus(13, 5))->toBe('3');
        expect(Math::modulus(13, -5))->toBe('3');

        expect(Math::modulus(-13, 5))->toBe('-3');
        expect(Math::modulus(-13, -5))->toBe('-3');

        expect(Math::modulus(5, 6))->toBe('5');
        expect(Math::modulus(1.1e+8, 1.1e-8))->toBe('0');

        expect(Math::modulus(5.7, 1.3))->toBe('0.5');
    });

    test('power method', function () {

        expect(Math::power(2, 3))->toBe('8');
        expect(Math::power(2, -3))->toBe('0.125');

        expect(Math::power(2))->toBe('4');
        expect(Math::power(2, -2))->toBe('0.25');

        expect(Math::power(2, 0))->toBe('1');
        expect(Math::power(0, 2))->toBe('0');
        expect(Math::power(0, 0))->toBe('1');

        expect(Math::power(1, 20))->toBe('1');

        expect(Math::power(1.1E+2, 2))->toBe('12100');

        expect(Math::power('1234567891234567889999999', 2))->toBe('1524157878067367851562259605883269630864220000001');
        expect(Math::power('1234567891234567889999999', 3))->toBe('1881676377434183981909558127466713752376807174114547646517403703669999999');

        expect(Math::power([2]))->toBe(['4']);
        expect(Math::power([2, 3, 4, 1.1E+2], 3))->toBe(['8', '27', '64', '1331000']);

        try {

            Math::power(0, -3); // 1/0 * 1/0 * 1/0

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
            expect($e->getCode())->toBe(10104);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'          => 'bcpow',
                'operand'       => [
                    'number'        => 0,
                    'exponent'      => -3
                ],
            ]);
        }

        try {

            Math::power([2, 0, 3], -3);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
            expect($e->getCode())->toBe(10104);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'          => 'bcpow',
                'operand'       => [
                    'number'        => [2, 0, 3],
                    'exponent'      => -3
                ],
            ]);
        }
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

        expect(Math::sqrt([0, 1, 2, 3, 4, 9, 16]))->toBe(['0', '1', '1.414213562373', '1.732050807568', '2', '3', '4']);

        try {

            Math::sqrt(-1);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
            expect($e->getCode())->toBe(10105);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'          => 'bcsqrt',
                'operand'       => -1,
            ]);
        }

        try {

            Math::sqrt([1, -1]);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
            expect($e->getCode())->toBe(10105);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'func'          => 'bcsqrt',
                'operand'       => [1, -1],
            ]);
        }
    });

    test('roundUp method', function () {

        expect(Math::roundUp(0))->toBe('0');
        expect(Math::roundUp(0.01))->toBe('1');
        expect(Math::roundUp(-0.01))->toBe('0');

        expect(Math::roundUp(1))->toBe('1');
        expect(Math::roundUp(1.1))->toBe('2');
        expect(Math::roundUp(-1.1))->toBe('-1');
        expect(Math::roundUp(1.999099))->toBe('2');
        expect(Math::roundUp(-1.999099))->toBe('-1');

        expect(Math::roundUp(1.1e+8))->toBe('110000000');
        expect(Math::roundUp(1.1e-8))->toBe('1');

        expect(roundUp([0, 0.01, -0.01, 1, 1.1, -1.1, 1.999099, -1.999099, 1.1e+8, 1.1e-8]))->toBe(['0', '1', '0', '1', '2', '-1', '2', '-1', '110000000', '1']);
    });

    test('roundDown method', function () {

        expect(Math::roundDown(0))->toBe('0');
        expect(Math::roundDown(0.01))->toBe('0');
        expect(Math::roundDown(-0.01))->toBe('-1');

        expect(Math::roundDown(1))->toBe('1');
        expect(Math::roundDown(1.1))->toBe('1');
        expect(Math::roundDown(-1.1))->toBe('-2');
        expect(Math::roundDown(1.9999))->toBe('1');
        expect(Math::roundDown(-1.9999))->toBe('-2');

        expect(Math::roundDown(1.1e+8))->toBe('110000000');
        expect(Math::roundDown(1.1e-8))->toBe('0');

        expect(roundDown([0, 0.01, -0.01, 1, 1.1, -1.1, 1.9999, -1.9999, 1.1e+8, 1.1e-8]))->toBe(['0', '0', '-1', '1', '1', '-2', '1', '-2', '110000000', '0']);
    });

    test('roundClose method', function ($number, $precision, $mode, $expected) {

        if (is_array($number)) {

            expect(roundClose(number: $number, precision: $precision, mode: $mode))->toBe($expected);

            // 
        } else {

            expect(Math::roundClose(number: $number, precision: $precision, mode: $mode))->toBe($expected);
            // 
        }
    })->with([
        [1.1,    0, RoundingMode::HalfAwayFromZero, '1'],
        [1.5,    0, RoundingMode::HalfAwayFromZero, '2'],      // halfway → away from zero
        [-1.1,   0, RoundingMode::HalfAwayFromZero, '-1'],
        [-1.5,   0, RoundingMode::HalfAwayFromZero, '-2'],     // halfway → away from zero (more negative)
        [2.5,    0, RoundingMode::HalfAwayFromZero, '3'],
        [-2.5,   0, RoundingMode::HalfAwayFromZero, '-3'],
        [0.5,    0, RoundingMode::HalfAwayFromZero, '1'],
        [-0.5,   0, RoundingMode::HalfAwayFromZero, '-1'],
        [0.499,  0, RoundingMode::HalfAwayFromZero, '0'],     // just below halfway → rounds towards zero
        [-0.499, 0, RoundingMode::HalfAwayFromZero, '0'],     // just below halfway → rounds towards zero

        [[1.1, 1.5, 0.499], 0, RoundingMode::HalfAwayFromZero, ['1', '2', '0']],

        [1.2,     2, RoundingMode::HalfAwayFromZero, '1.2'],
        [1.92,    2, RoundingMode::HalfAwayFromZero, '1.92'],
        [1.996,   2, RoundingMode::HalfAwayFromZero, '2'],      // third decimal 6 ≥ 5 → carries into integer part
        [1.005,   2, RoundingMode::HalfAwayFromZero, '1.01'],   // exactly halfway → away from zero
        [1.004,   2, RoundingMode::HalfAwayFromZero, '1'],      // just below halfway
        [-1.005,  2, RoundingMode::HalfAwayFromZero, '-1.01'],
        [-1.004,  2, RoundingMode::HalfAwayFromZero, '-1'],
        [1.995,   2, RoundingMode::HalfAwayFromZero, '2'],      // halfway + carry
        [-1.995,  2, RoundingMode::HalfAwayFromZero, '-2'],

        [1.1,    0, RoundingMode::HalfTowardsZero, '1'],      // below halfway → normal rounding
        [1.5,    0, RoundingMode::HalfTowardsZero, '1'],      // halfway → toward zero (not 2)
        [-1.5,   0, RoundingMode::HalfTowardsZero, '-1'],     // halfway → toward zero (not -2)
        [2.5,    0, RoundingMode::HalfTowardsZero, '2'],      // halfway → toward zero
        [-2.5,   0, RoundingMode::HalfTowardsZero, '-2'],     // halfway → toward zero
        [0.5,    0, RoundingMode::HalfTowardsZero, '0'],      // halfway → toward zero
        [-0.5,   0, RoundingMode::HalfTowardsZero, '0'],      // halfway → toward zero
        [0.499,  0, RoundingMode::HalfTowardsZero, '0'],      // just below halfway → no rounding up
        [-0.499, 0, RoundingMode::HalfTowardsZero, '0'],      // just below halfway → stays 0

        [1.2,     2, RoundingMode::HalfTowardsZero, '1.2'],   // no rounding needed
        [1.005,   2, RoundingMode::HalfTowardsZero, '1'],     // exactly halfway → toward zero (1.00, not 1.01)
        [-1.005,  2, RoundingMode::HalfTowardsZero, '-1'],    // exactly halfway → toward zero (-1.00, not -1.01)
        [1.004,   2, RoundingMode::HalfTowardsZero, '1'],     // just below halfway
        [-1.004,  2, RoundingMode::HalfTowardsZero, '-1'],    // just below halfway
        [1.995,   2, RoundingMode::HalfTowardsZero, '1.99'],  // exactly halfway between 1.99 and 2.00 → toward zero
        [-1.995,  2, RoundingMode::HalfTowardsZero, '-1.99'], // exactly halfway → toward zero (-1.99, not -2.00)
        [1.996,   2, RoundingMode::HalfTowardsZero, '2'],     // above halfway → normal rounding up
        [0.005,   2, RoundingMode::HalfTowardsZero, '0'],     // halfway → toward zero
        [-0.005,  2, RoundingMode::HalfTowardsZero, '0'],     // halfway → toward zero

        [1.1,    0, RoundingMode::HalfEven, '1'],            // normal rounding
        [1.5,    0, RoundingMode::HalfEven, '2'],            // half → even neighbour (2 is even)
        [2.5,    0, RoundingMode::HalfEven, '2'],            // half → even (2)
        [0.5,    0, RoundingMode::HalfEven, '0'],            // half → even (0)
        [-1.5,   0, RoundingMode::HalfEven, '-2'],           // half → even neighbour, -2 is even
        [-2.5,   0, RoundingMode::HalfEven, '-2'],           // half → even (-2)
        [-0.5,   0, RoundingMode::HalfEven, '0'],            // half → even (0)
        [0.499,  0, RoundingMode::HalfEven, '0'],            // just below half → towards zero

        [1.2,     2, RoundingMode::HalfEven, '1.2'],       // no rounding needed
        [1.005,   2, RoundingMode::HalfEven, '1'],          // half → last digit even (0)
        [1.025,   2, RoundingMode::HalfEven, '1.02'],       // half → last digit even (2)
        [-1.005,  2, RoundingMode::HalfEven, '-1'],         // half → even (-1.00)
        [-1.025,  2, RoundingMode::HalfEven, '-1.02'],      // half → even (-1.02)
        [1.995,   2, RoundingMode::HalfEven, '2'],          // half → 2.00 (last digit 0, even)
        [2.005,   2, RoundingMode::HalfEven, '2'],          // half → even (2.00)
        [0.005,   2, RoundingMode::HalfEven, '0'],          // half → even (0.00)
        [-0.005,  2, RoundingMode::HalfEven, '0'],          // half → even (0.00)
        [1.004,   2, RoundingMode::HalfEven, '1'],          // just below half → normal rounding down

        [1.1,    0, RoundingMode::HalfOdd, '1'],            // normal rounding
        [1.5,    0, RoundingMode::HalfOdd, '1'],            // half → odd neighbour (1 is odd, not 2)
        [2.5,    0, RoundingMode::HalfOdd, '3'],            // half → odd (3 is odd, 2 is even)
        [-1.5,   0, RoundingMode::HalfOdd, '-1'],           // half → odd (-1 is odd, -2 even)
        [-2.5,   0, RoundingMode::HalfOdd, '-3'],           // half → odd (-3 odd, -2 even)
        [0.5,    0, RoundingMode::HalfOdd, '1'],            // half → odd (1)
        [-0.5,   0, RoundingMode::HalfOdd, '-1'],           // half → odd (-1)
        [0.499,  0, RoundingMode::HalfOdd, '0'],            // just below half → no rounding up

        [1.2,     2, RoundingMode::HalfOdd, '1.2'],         // no rounding
        [1.005,   2, RoundingMode::HalfOdd, '1.01'],        // half → last digit odd (1.01, not 1.00)
        [1.025,   2, RoundingMode::HalfOdd, '1.03'],        // half → odd (1.03, last digit 3 odd)
        [-1.005,  2, RoundingMode::HalfOdd, '-1.01'],       // half → odd (-1.01)
        [-1.025,  2, RoundingMode::HalfOdd, '-1.03'],       // half → odd (-1.03)
        [1.995,   2, RoundingMode::HalfOdd, '1.99'],        // half → 1.99 (last digit 9 odd, 2.00 would be even)
        [2.005,   2, RoundingMode::HalfOdd, '2.01'],        // half → odd (2.01, not 2.00)
        [0.005,   2, RoundingMode::HalfOdd, '0.01'],        // half → odd (0.01, not 0.00)
        [-0.005,  2, RoundingMode::HalfOdd, '-0.01'],       // half → odd (-0.01)
        [1.004,   2, RoundingMode::HalfOdd, '1'],           // just below half → normal rounding down

        [1.1,    0, RoundingMode::TowardsZero, '1'],      // truncate → 1
        [1.5,    0, RoundingMode::TowardsZero, '1'],      // half does not round up → 1
        [1.9,    0, RoundingMode::TowardsZero, '1'],      // just below 2 → 1
        [-1.1,   0, RoundingMode::TowardsZero, '-1'],     // truncate → -1
        [-1.5,   0, RoundingMode::TowardsZero, '-1'],     // truncate toward zero → -1
        [-1.9,   0, RoundingMode::TowardsZero, '-1'],     // truncate → -1
        [2.5,    0, RoundingMode::TowardsZero, '2'],      // truncate → 2
        [-2.5,   0, RoundingMode::TowardsZero, '-2'],     // truncate → -2
        [0.5,    0, RoundingMode::TowardsZero, '0'],      // truncate → 0
        [-0.5,   0, RoundingMode::TowardsZero, '0'],      // truncate → 0

        [1.2,     2, RoundingMode::TowardsZero, '1.2'],   // exact → no change
        [1.996,   2, RoundingMode::TowardsZero, '1.99'],  // third decimal cut, no rounding → 1.99
        [1.004,   2, RoundingMode::TowardsZero, '1'],     // 1.004 → 1.00
        [1.005,   2, RoundingMode::TowardsZero, '1'],     // halfway → truncate, stays 1.00
        [-1.005,  2, RoundingMode::TowardsZero, '-1'],    // halfway → truncate toward zero → -1.00
        [1.995,   2, RoundingMode::TowardsZero, '1.99'],  // 1.995 → truncate to 1.99
        [-1.995,  2, RoundingMode::TowardsZero, '-1.99'], // truncate toward zero → -1.99
        [0.005,   2, RoundingMode::TowardsZero, '0'],     // truncate → 0.00

        [1.0,    0, RoundingMode::AwayFromZero, '1'],      // exact integer → no change
        [1.1,    0, RoundingMode::AwayFromZero, '2'],      // any fraction → away from zero (up)
        [1.5,    0, RoundingMode::AwayFromZero, '2'],      // halfway included
        [-1.1,   0, RoundingMode::AwayFromZero, '-2'],     // negative fraction → away (down)
        [-1.5,   0, RoundingMode::AwayFromZero, '-2'],
        [0.1,    0, RoundingMode::AwayFromZero, '1'],      // positive small fraction → 1
        [-0.1,   0, RoundingMode::AwayFromZero, '-1'],     // negative small fraction → -1
        [2.0,    0, RoundingMode::AwayFromZero, '2'],      // exact integer stays
        [-2.0,   0, RoundingMode::AwayFromZero, '-2'],

        [1.2,     2, RoundingMode::AwayFromZero, '1.2'],    // exact → unchanged
        [1.201,   2, RoundingMode::AwayFromZero, '1.21'],   // third decimal > 0 → round up
        [1.200,   2, RoundingMode::AwayFromZero, '1.2'],    // exactly 1.20 → stays
        [1.005,   2, RoundingMode::AwayFromZero, '1.01'],   // half still rounds away
        [-1.005,  2, RoundingMode::AwayFromZero, '-1.01'],  // negative half → away (more negative)
        [1.999,   2, RoundingMode::AwayFromZero, '2'],      // carry-over, rounds up
        [-1.999,  2, RoundingMode::AwayFromZero, '-2'],     // negative, rounds down with carry
        [0.001,   2, RoundingMode::AwayFromZero, '0.01'],   // tiny fraction → away from zero
        [-0.001,  2, RoundingMode::AwayFromZero, '-0.01'],  // tiny negative fraction → away

        [1.0,     0, RoundingMode::NegativeInfinity, '1'],       // exact integer → unchanged
        [1.0001,  0, RoundingMode::NegativeInfinity, '1'],       // tiny fraction → round down (floor)
        [1.5,     0, RoundingMode::NegativeInfinity, '1'],       // half → down
        [1.999,   0, RoundingMode::NegativeInfinity, '1'],       // just below 2 → down
        [-1.0,    0, RoundingMode::NegativeInfinity, '-1'],      // exact → unchanged
        [-1.0001, 0, RoundingMode::NegativeInfinity, '-2'],      // floor: -2 < -1.0001
        [-1.5,    0, RoundingMode::NegativeInfinity, '-2'],      // half → down (more negative)
        [-1.999,  0, RoundingMode::NegativeInfinity, '-2'],      // almost -2 → floor
        [0.1,     0, RoundingMode::NegativeInfinity, '0'],       // positive fraction → 0
        [-0.1,    0, RoundingMode::NegativeInfinity, '-1'],      // negative fraction → -1

        [1.2,     2, RoundingMode::NegativeInfinity, '1.2'],    // exact → unchanged
        [1.001,   2, RoundingMode::NegativeInfinity, '1'],      // any fraction → round down
        [1.005,   2, RoundingMode::NegativeInfinity, '1'],      // halfway ignored, still down
        [1.999,   2, RoundingMode::NegativeInfinity, '1.99'],   // 1.999 → floor(199.9) = 199 → 1.99
        [-1.001,  2, RoundingMode::NegativeInfinity, '-1.01'],  // floor: -1.01 < -1.001
        [-1.005,  2, RoundingMode::NegativeInfinity, '-1.01'],  // half floor → -1.01
        [-1.999,  2, RoundingMode::NegativeInfinity, '-2'],     // carry‑over: floor(-199.9) = -200 → -2.00
        [0.001,   2, RoundingMode::NegativeInfinity, '0'],      // tiny positive → 0.00
        [-0.001,  2, RoundingMode::NegativeInfinity, '-0.01'],  // tiny negative → -0.01

        [1.0,    0, RoundingMode::PositiveInfinity,  '1'],      // exact integer → unchanged
        [1.1,    0, RoundingMode::PositiveInfinity,  '2'],      // any fraction → round up (ceil)
        [1.5,    0, RoundingMode::PositiveInfinity,  '2'],      // half → up
        [1.999,  0, RoundingMode::PositiveInfinity,  '2'],      // just below 2 → up
        [0.1,    0, RoundingMode::PositiveInfinity,  '1'],      // small positive fraction → 1
        [-1.0,   0, RoundingMode::PositiveInfinity,  '-1'],     // exact negative → unchanged
        [-1.1,   0, RoundingMode::PositiveInfinity,  '-1'],     // ceil: -1 > -1.1 → -1
        [-1.5,   0, RoundingMode::PositiveInfinity,  '-1'],     // half → up (toward +∞)
        [-1.999, 0, RoundingMode::PositiveInfinity,  '-1'],     // almost -2 → ceil gives -1
        [-0.1,   0, RoundingMode::PositiveInfinity,  '0'],      // negative tiny fraction → 0

        [1.2,     2, RoundingMode::PositiveInfinity,  '1.2'],   // exact → unchanged
        [1.001,   2, RoundingMode::PositiveInfinity,  '1.01'],  // any fraction → round up
        [1.005,   2, RoundingMode::PositiveInfinity,  '1.01'],   // half → up
        [1.999,   2, RoundingMode::PositiveInfinity,  '2'],     // carry‑over: 1.999 → 2.00
        [0.001,   2, RoundingMode::PositiveInfinity,  '0.01'],  // tiny positive → up
        [-1.001,  2, RoundingMode::PositiveInfinity,  '-1'],    // ceil: -1.00 > -1.001
        [-1.005,  2, RoundingMode::PositiveInfinity,  '-1'],    // half → -1.00
        [-1.999,  2, RoundingMode::PositiveInfinity,  '-1.99'],  // ceil of -199.9 → -199 → -1.99
        [-0.001,  2, RoundingMode::PositiveInfinity,  '0'],     // negative tiny → ceil to zero
    ]);

    test('greaterThan method', function () {

        expect(Math::greaterThan(0, 0))->toBeFalse();
        expect(Math::greaterThan(-1, 0))->toBeFalse();
        expect(Math::greaterThan(0, 5))->toBeFalse();
        expect(Math::greaterThan(0, -1))->toBeTrue();

        expect(Math::setPrecision(precision: 1)->greaterThan(1.11, 1.112))->toBeFalse();

        expect(Math::greaterThan(1.1e+8, 1.1e-8))->toBeTrue();
        expect(Math::greaterThan(1.1e+8, 1.1e+8))->toBeFalse();
        expect(Math::greaterThan(1.1e-8, 1.1e+8))->toBeFalse();

        expect(math(precision: 1)->greaterThan(1.11, 1.112))->toBeFalse();
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

        expect(math(precision: 1)->greaterThanOrEqual(1.113, 1.112))->toBeTrue();
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

        expect(math(precision: 1)->lessThan(1.112, 1.11))->toBeFalse();
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

        expect(math(precision: 1)->lessThanOrEqual(1.11, 1.11))->toBeTrue();
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

        expect(math(precision: 1)->equal(1.112, 1.113))->toBeFalse();
        expect(equal(1.1e+8, 1.1e-8))->toBeFalse();

        expect(Math::notEqual(0, 0))->toBeFalse();
        expect(Math::notEqual(-1, 0))->toBeTrue();
        expect(Math::notEqual(0, 5))->toBeTrue();
        expect(Math::notEqual(0, -1))->toBeTrue();

        expect(Math::setPrecision(precision: 1)->notEqual(1.112, 1.113))->toBeTrue();
        expect(Math::notEqual(1, 2))->toBeTrue();
        expect(Math::notEqual(2, 2))->toBeFalse();

        expect(math(precision: 1)->notEqual(1.112, 1.113))->toBeTrue();
        expect(notEqual(1, 2))->toBeTrue();
        expect(notEqual(2, 2))->toBeFalse();
    });
});
