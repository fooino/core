<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Facades\Math;
use Fooino\Core\Tests\Data\Datasets;

describe('Math facade using FooinoMathHandler', function () {

    test('precision getter and setter', function () {

        expect(Math::getPrecision())->toBe(12);
        expect(Math::setPrecision(precision: 5)->getPrecision())->toBe(5);

        expect(math()->getPrecision())->toBe(12);
        expect(math(precision: 5)->getPrecision())->toBe(5);

        expect(bcscale())->toBe(12);
    });

    test('convertScientificNumber method', function ($number, $expected) {

        expect(Math::convertScientificNumber($number))->toBe($expected);

        // 
    })
        ->with(Datasets::mathConvertScientificNumber());

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

        expect(Math::countDecimalPlaces('test'))->toBe(0);
        expect(Math::countDecimalPlaces('test.'))->toBe(0);
        expect(Math::countDecimalPlaces('test.0'))->toBe(0);
    });

    test('number method', function () {

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

        expect(Math::number(1, 11.000001000, '.e+8'))->toBe(['1', '11.000001', '0']);
        expect(number([1, 11.000001000, '.e+8']))->toBe(['1', '11.000001', '0']);

        expect(Math::setPrecision(2)->number(1.001, '.44015042', '1e8', 'e8'))->toBe(['1', '0.44', '100000000', '0']);
        expect(math(2)->number(1.001, '.44015042', '1e8', 'e8'))->toBe(['1', '0.44', '100000000', '0']);
    });

    test('numberFormat method', function () {

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

        expect(Math::numberFormat(number: '5,000,000.0150100', thousandsSeparator: " "))->toBe("5 000 000.01501");
        expect(Math::numberFormat(number: '-5-000-000.0150100', thousandsSeparator: "-"))->toBe("-5-000-000.01501");
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

        test('very big and small number for convertScientificNumber', function () {

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
                    'args'          => []
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
                    'args'          => []
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
                    'args'          => []
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
                    'args'          => []
                ]);
            }
        });

        test('number check the input is numeric', function () {

            expect(fn() => number())->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => number(1, 'test'))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => number([1, 'test']))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');

            try {

                Math::number();

                //
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'number',
                    'operand'       => [],
                ]);
            }

            try {

                Math::number('test');

                //
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'number',
                    'operand'       => ['test'],
                ]);
            }

            try {

                Math::number(1, 'test');

                //
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'number',
                    'operand'       => ['1', 'test'],
                ]);
            }

            try {

                Math::number([1, 'test']);

                //
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'number',
                    'operand'       => ['1', 'test'],
                ]);
            }
        });

        test('numberFormat check the input is numeric', function () {

            expect(fn() => numberFormat('test'))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');

            try {

                Math::numberFormat('2,000,000.12T');

                //
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'numberFormat',
                    'operand'       => '2,000,000.12T',
                ]);
            }
        });

        // 
    });
});
