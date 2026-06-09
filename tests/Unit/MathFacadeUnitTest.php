<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Facades\Math;
use Fooino\Core\Tests\Data\Datasets;
use RoundingMode;

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

    test('trimTrailingZeros method', function ($number, $expected) {

        expect(Math::trimTrailingZeros($number))->toBe($expected);

        // 
    })
        ->with(Datasets::mathTrimTrailingZeros());

    test('countDecimalPlaces method', function ($number, $expected) {

        expect(Math::countDecimalPlaces($number))->toBe($expected);

        //
    })
        ->with(Datasets::mathCountDecimalPlaces());

    test('number method', function ($number, $expected, $precision = null) {

        if (is_callable($number)) {
            $number();
            return;
        }

        if (rand(0, 1)) {

            if (!is_null($precision)) {

                expect(Math::setPrecision(precision: $precision)->number($number))->toBe($expected);
                return;
            }

            expect(Math::number($number))->toBe($expected);
        }

        if (!is_null($precision)) {

            expect(math(precision: $precision)->number($number))->toBe($expected);
            return;
        }

        expect(number($number))->toBe($expected);
    })
        ->with(Datasets::mathNumber());


    test('numberFormat method', function ($number, $thousandsSeparator, $expected, $precision = null) {

        if (rand(0, 1)) {

            if (!is_null($precision)) {

                expect(Math::setPrecision(precision: $precision)->numberFormat(number: $number, thousandsSeparator: $thousandsSeparator))->toBe($expected);
                return;
            }

            expect(Math::numberFormat(number: $number, thousandsSeparator: $thousandsSeparator))->toBe($expected);
        }

        if (!is_null($precision)) {

            expect(math(precision: $precision)->numberFormat(number: $number, thousandsSeparator: $thousandsSeparator))->toBe($expected);
            return;
        }

        expect(numberFormat(number: $number, thousandsSeparator: $thousandsSeparator))->toBe($expected);
    })
        ->with(Datasets::mathNumberFormat());


    test('sum method', function ($number, $expected) {

        if (is_callable($number)) {
            $number();
            return;
        }

        if (rand(0, 1)) {

            expect(Math::sum($number))->toBe($expected);

            return;
        }

        expect(sum($number))->toBe($expected);
    })
        ->with(Datasets::mathSum());


    test('subtract method', function ($number, $expected) {

        if (is_callable($number)) {
            $number();
            return;
        }

        if (rand(0, 1)) {

            expect(Math::subtract($number))->toBe($expected);

            return;
        }

        expect(subtract($number))->toBe($expected);
    })
        ->with(Datasets::mathSubtract());


    test('multiply method', function ($number, $expected) {

        if (is_callable($number)) {
            $number();
            return;
        }

        if (rand(0, 1)) {

            expect(Math::multiply($number))->toBe($expected);

            return;
        }

        expect(multiply($number))->toBe($expected);
    })
        ->with(Datasets::mathMultiply());


    test('divide method', function ($number, $expected) {

        if (is_callable($number)) {
            $number();
            return;
        }

        if (rand(0, 1)) {

            expect(Math::divide($number))->toBe($expected);

            return;
        }

        expect(divide($number))->toBe($expected);
    })
        ->with(Datasets::mathDivide());

    test('remainder method', function ($number, $expected) {

        if (is_callable($number)) {
            $number();
            return;
        }

        if (rand(0, 1)) {

            expect(Math::remainder($number))->toBe($expected);

            return;
        }

        expect(remainder($number))->toBe($expected);
    })
        ->with(Datasets::mathRemainder());

    test('power method', function ($number, $exponent, $expected) {

        expect(Math::power(number: $number, exponent: $exponent))->toBe($expected);

        // 
    })
        ->with(Datasets::mathPower());

    test('sqrt method', function ($number, $expected) {

        expect(Math::sqrt(number: $number))->toBe($expected);

        // 
    })
        ->with(Datasets::mathSqrt());

    test('roundUp method', function ($number, $expected) {

        if (rand(0, 1)) {

            expect(Math::roundUp(number: $number))->toBe($expected);

            return;
        }

        expect(roundUp(number: $number))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathRoundUp());

    test('roundDown method', function ($number, $expected) {

        if (rand(0, 1)) {

            expect(Math::roundDown(number: $number))->toBe($expected);

            return;
        }

        expect(roundDown(number: $number))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathRoundDown());

    test('roundClose method', function ($number, $precision, $mode, $expected) {

        if (rand(0, 1)) {

            expect(Math::roundClose(number: $number, precision: $precision, mode: $mode))->toBe($expected);

            return;
        }

        expect(roundClose(number: $number, precision: $precision, mode: $mode))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathRoundClose());

    test('greaterThan method', function ($a, $b, $expected) {

        if (rand(0, 1)) {

            expect(Math::greaterThan($a, $b))->toBe($expected);

            return;
        }

        expect(greaterThan($a, $b))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathGreaterThan());

    test('greaterThanOrEqual method', function ($a, $b, $expected) {

        if (rand(0, 1)) {

            expect(Math::greaterThanOrEqual($a, $b))->toBe($expected);

            return;
        }

        expect(greaterThanOrEqual($a, $b))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathGreaterThanOrEqual());

    test('lessThan method', function ($a, $b, $expected) {

        if (rand(0, 1)) {

            expect(Math::lessThan($a, $b))->toBe($expected);

            return;
        }

        expect(lessThan($a, $b))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathLessThan());

    test('lessThanOrEqual method', function ($a, $b, $expected) {

        if (rand(0, 1)) {

            expect(Math::lessThanOrEqual($a, $b))->toBe($expected);

            return;
        }

        expect(lessThanOrEqual($a, $b))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathLessThanOrEqual());

    test('equal method', function ($a, $b, $expected) {

        if (rand(0, 1)) {

            expect(Math::equal($a, $b))->toBe($expected);

            return;
        }

        expect(equal($a, $b))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathEqual());

    test('notEqual method', function ($a, $b, $expected) {

        if (rand(0, 1)) {

            expect(Math::notEqual($a, $b))->toBe($expected);

            return;
        }

        expect(notEqual($a, $b))->toBe($expected);

        return;

        // 
    })
        ->with(Datasets::mathNotEqual());



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
            expect(fn() => number('test'))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
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
                    'args'          => []
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
                    'args'          => []
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
                    'args'          => []
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
                    'args'          => []
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
                    'args'          => []
                ]);
            }
        });

        test('methods with two operands check the operands count', function () {

            expect(fn() => sum())->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => subtract(1))->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => multiply([1]))->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => divide(1))->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => remainder())->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');

            expect(fn() => Math::power([]))->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => Math::sqrt([]))->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => roundUp([]))->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => roundDown([]))->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => roundClose([]))->toThrow('msg.mathCalculationExceptionInvalidArgumentsCount');

            try {

                Math::sum(1);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcadd',
                    'operand'       => [1],
                    'args'          => []
                ]);
            }

            try {

                Math::subtract();

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsub',
                    'operand'       => [],
                    'args'          => []
                ]);
            }

            try {

                Math::multiply(1);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmul',
                    'operand'       => [1],
                    'args'          => []
                ]);
            }

            try {

                Math::divide([1]);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcdiv',
                    'operand'       => [[1]],
                    'args'          => []
                ]);
            }

            try {

                Math::remainder([1]);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmod',
                    'operand'       => [[1]],
                    'args'          => []
                ]);
            }

            try {

                Math::power([]);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcpow',
                    'operand'       => [],
                    'args'          => ['exponent'  => 2]
                ]);
            }

            try {

                Math::sqrt([]);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsqrt',
                    'operand'       => [],
                    'args'          => []
                ]);
            }

            try {

                Math::roundUp([]);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcceil',
                    'operand'       => [],
                    'args'          => []
                ]);
            }

            try {

                Math::roundDown([]);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcfloor',
                    'operand'       => [],
                    'args'          => []
                ]);
            }

            try {

                Math::roundClose([]);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(10102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcround',
                    'operand'       => [],
                    'args'          => ['precision' => 0, 'mode' => RoundingMode::HalfAwayFromZero]
                ]);
            }
        });

        test('methods check the operands are numeric', function () {

            expect(fn() => sum(1, 'test'))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => subtract([1, 'test']))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => multiply([1, 'test']))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => divide(1, 2, 'test'))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => remainder([1, 'test']))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => Math::power('test'))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::sqrt('test'))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundUp([1, 'test']))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundDown([1, 'test']))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundClose([1, 'test']))->toThrow('msg.mathCalculationExceptionInvalidArgumentType');


            try {

                Math::sum(1, 'test');

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcadd',
                    'operand'       => [1, 'test'],
                    'args'          => []
                ]);
            }


            try {

                Math::subtract([1, 'test']);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsub',
                    'operand'       => [[1, 'test']],
                    'args'          => []
                ]);
            }


            try {

                Math::multiply([1, 2, 'test']);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmul',
                    'operand'       => [[1, 2, 'test']],
                    'args'          => []
                ]);
            }


            try {

                Math::divide(1, 2, 'test');

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcdiv',
                    'operand'       => [1, 2, 'test'],
                    'args'          => []
                ]);
            }


            try {

                Math::remainder(1, 'test', 0);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmod',
                    'operand'       => [1, 'test', 0],
                    'args'          => []
                ]);
            }

            try {

                Math::power('test');

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcpow',
                    'operand'       => ['test'],
                    'args'          => ['exponent' => 2]
                ]);
            }

            try {

                Math::sqrt([1, 'test']);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsqrt',
                    'operand'       => [1, 'test'],
                    'args'          => []
                ]);
            }

            try {

                Math::roundUp('test');

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcceil',
                    'operand'       => ['test'],
                    'args'          => []
                ]);
            }

            try {

                Math::roundDown([1, 'test']);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcfloor',
                    'operand'       => [1, 'test'],
                    'args'          => []
                ]);
            }

            try {

                Math::roundClose('test');

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(10103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcround',
                    'operand'       => ['test'],
                    'args'          => ['precision' => 0, 'mode' => RoundingMode::HalfAwayFromZero]
                ]);
            }
        });

        test('divide and remainder check the operands are not zero', function () {

            expect(fn() => divide(1, 0))->toThrow('msg.mathCalculationExceptionDivisionByZero');
            expect(fn() => remainder(1, 0))->toThrow('msg.mathCalculationExceptionDivisionByZero');
            expect(fn() => divide([1, 2, 0]))->toThrow('msg.mathCalculationExceptionDivisionByZero');
            expect(fn() => remainder([1, 3, 0]))->toThrow('msg.mathCalculationExceptionDivisionByZero');

            try {

                Math::divide(1, 2, 0);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
                expect($e->getCode())->toBe(10104);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcdiv',
                    'operand'       => [1, 2, 0],
                    'args'          => []
                ]);
            }

            try {

                Math::remainder([1, 2, 0]);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
                expect($e->getCode())->toBe(10104);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmod',
                    'operand'       => [[1, 2, 0]],
                    'args'          => []
                ]);
            }

            //
        });

        test('power and sqrt check the value and args type', function () {

            expect(fn() => Math::power(0, -1))->toThrow('mathCalculationExceptionDivisionByZero');
            expect(fn() => Math::sqrt(-1))->toThrow('msg.mathCalculationExceptionInvalidValueError');

            try {

                Math::power([1, 0], -1);

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
                expect($e->getCode())->toBe(10104);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcpow',
                    'operand'       => [1, 0],
                    'args'          => ['exponent' => -1]
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
                    'method'        => 'bcsqrt',
                    'operand'       => [1, -1],
                    'args'          => []
                ]);
            }
        });

        // 
    });
});
