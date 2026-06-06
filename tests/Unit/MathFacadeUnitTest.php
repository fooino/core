<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Facades\Math;

describe('Math facade using FooinoMathHandler', function () {

    test('precision getter and setter', function () {

        expect(Math::getPrecision())->toBe(12);
        expect(Math::setPrecision(precision: 5)->getPrecision())->toBe(5);

        expect(math()->getPrecision())->toBe(12);
        expect(math(precision: 5)->getPrecision())->toBe(5);

        expect(bcscale())->toBe(12);
    });

    test('convertScientificNumber method', function () {

        expect(Math::convertScientificNumber('null'))->toBe('null');
        expect(Math::convertScientificNumber('null.null'))->toBe('null.null');
        expect(Math::convertScientificNumber('""'))->toBe('""');
        expect(Math::convertScientificNumber('foobar'))->toBe('foobar');
        expect(Math::convertScientificNumber('foo.bar'))->toBe('foo.bar');
        expect(Math::convertScientificNumber('foo.bar.ino'))->toBe('foo.bar.ino');
        expect(Math::convertScientificNumber('-foo.bar.ino'))->toBe('-foo.bar.ino');
        expect(Math::convertScientificNumber('abc1E+3xyz'))->toBe('abc1E+3xyz'); // contains 1E+3 which is valid Scientific Number but the method must not convert it
        expect(Math::convertScientificNumber('test'))->toBe('test');

        expect(Math::convertScientificNumber(''))->toBe('');
        expect(Math::convertScientificNumber('.'))->toBe('.');
        expect(Math::convertScientificNumber('+.'))->toBe('+.');
        expect(Math::convertScientificNumber('-.'))->toBe('-.');

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
        expect(Math::trimTrailingZeros('foo.bar0'))->toBe('foo.bar0');
        expect(Math::trimTrailingZeros('foo.0'))->toBe('foo.0');

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

        expect(Math::trimTrailingZeros('-0.1E-2'))->toBe('-0.001');
    });

    describe('handle exceptions', function () {

        test('invalid precision', function () {

            expect(fn() => math(20))->toThrow('msg.mathCalculationExceptionInvalidPrecision');

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

            expect(fn() => math(-1))->toThrow('msg.mathCalculationExceptionInvalidPrecision');

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

        test('very big and small number', function () {

            expect(fn() => Math::convertScientificNumber(1.1E+9999))->toThrow('msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber(-1.1E+9999))->toThrow('msg.mathCalculationExceptionInvalidValueError');

            try {

                Math::convertScientificNumber(1.1E+9999);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(10105);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'convertScientificNumber',
                    'operand'       => INF,
                ]);
            }

            try {

                Math::convertScientificNumber(-1.1E+9999);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(10105);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'convertScientificNumber',
                    'operand'       => -INF,
                ]);
            }

            expect(fn() => Math::convertScientificNumber('1.1E+9999'))->toThrow('msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber('1.1E-9999'))->toThrow('msg.mathCalculationExceptionInvalidValueError');

            try {

                Math::convertScientificNumber('1.1E+9999');

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(10105);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'convertScientificNumber',
                    'operand'       => '1.1E+9999',
                ]);
            }

            try {

                Math::convertScientificNumber('1.1E-9999');

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(10105);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'convertScientificNumber',
                    'operand'       => '1.1E-9999',
                ]);
            }
        });

        // 
    });
});
