<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Concretes\Math\FooinoMathHandler;
use Fooino\Core\Exceptions\MathCalculationException;
use Fooino\Core\Facades\Math;
use Fooino\Core\Tests\Data\Datasets;
use RoundingMode;

describe('Math facade using FooinoMathHandler', function () {

    test('precision getter and setter', function () {

        expect(Math::getPrecision())->toBe(12);
        expect(Math::setPrecision(precision: 5)->getPrecision())->toBe(5);

        expect(math()->getPrecision())->toBe(12);
        expect(math(precision: 5)->getPrecision())->toBe(5);

        expect(bcscale())->toBe(0);
    });

    test('setPrecision returns same instance for same precision', function () {

        $a = Math::setPrecision(precision: 5);
        $b = Math::setPrecision(precision: 5);
        expect($a)->toBe($b); // same object via singleton cache

        $c = Math::setPrecision(precision: 7);
        expect($a)->not->toBe($c); // different precision → different instance
    });

    test('static instances cache works across different handler instances', function () {

        $a = (new FooinoMathHandler())->setPrecision(precision: 5);
        $b = (new FooinoMathHandler())->setPrecision(precision: 5);
        expect($a)->toBe($b); // same object via static cache, even from different handlers

        $c = (new FooinoMathHandler())->setPrecision(precision: 7);
        expect($a)->not->toBe($c); // different precision → different instance
    });

    test('convertScientificNumber method', function () {

        foreach (Datasets::mathConvertScientificNumber() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            expect(Math::convertScientificNumber($number))->toBe($expected);
        }

        // 
    });

    test('trimTrailingZeros method', function () {

        foreach (Datasets::mathTrimTrailingZeros() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            expect(Math::trimTrailingZeros($number))->toBe($expected);
        }

        // 
    });

    test('countDecimalPlaces method', function () {

        foreach (Datasets::mathCountDecimalPlaces() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            expect(Math::countDecimalPlaces($number))->toBe($expected);
        }

        //
    });

    test('number method', function () {

        foreach (Datasets::mathNumber() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];
            $precision = $dataset[2] ?? null;

            if (is_callable($number)) {
                $number();
                continue;
            }

            if (rand(0, 1)) {

                if (!is_null($precision)) {

                    expect(Math::setPrecision(precision: $precision)->number($number))->toBe($expected);
                    continue;
                }

                expect(Math::number($number))->toBe($expected);
                continue;
            }

            if (!is_null($precision)) {

                expect(math(precision: $precision)->number($number))->toBe($expected);
                continue;
            }

            expect(number($number))->toBe($expected);
        }

        // 
    });

    test('numberFormat method', function () {

        foreach (Datasets::mathNumberFormat() as $dataset) {

            $number = $dataset[0];
            $thousandsSeparator = $dataset[1];
            $expected = $dataset[2];
            $precision = $dataset[3] ?? null;

            if (rand(0, 1)) {

                if (!is_null($precision)) {

                    expect(Math::setPrecision(precision: $precision)->numberFormat(number: $number, thousandsSeparator: $thousandsSeparator))->toBe($expected);
                    continue;
                }

                expect(Math::numberFormat(number: $number, thousandsSeparator: $thousandsSeparator))->toBe($expected);
                continue;
            }

            if (!is_null($precision)) {

                expect(math(precision: $precision)->numberFormat(number: $number, thousandsSeparator: $thousandsSeparator))->toBe($expected);
                continue;
            }

            expect(numberFormat(number: $number, thousandsSeparator: $thousandsSeparator))->toBe($expected);
        }

        // 
    });

    test('sum method', function () {

        foreach (Datasets::mathSum() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            if (is_callable($number)) {
                $number();
                continue;
            }

            if (rand(0, 1)) {

                expect(Math::sum($number))->toBe($expected);

                continue;
            }

            expect(sum($number))->toBe($expected);
        }

        // 
    });

    test('subtract method', function () {

        foreach (Datasets::mathSubtract() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            if (is_callable($number)) {
                $number();
                continue;
            }

            if (rand(0, 1)) {

                expect(Math::subtract($number))->toBe($expected);

                continue;
            }

            expect(subtract($number))->toBe($expected);
        }

        // 
    });

    test('multiply method', function () {

        foreach (Datasets::mathMultiply() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            if (is_callable($number)) {
                $number();
                continue;
            }

            if (rand(0, 1)) {

                expect(Math::multiply($number))->toBe($expected);

                continue;
            }

            expect(multiply($number))->toBe($expected);
        }

        // 
    });

    test('divide method', function () {

        foreach (Datasets::mathDivide() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            if (is_callable($number)) {
                $number();
                continue;
            }

            if (rand(0, 1)) {

                expect(Math::divide($number))->toBe($expected);

                continue;
            }

            expect(divide($number))->toBe($expected);
        }

        // 
    });

    test('remainder method', function () {

        foreach (Datasets::mathRemainder() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            if (is_callable($number)) {
                $number();
                continue;
            }

            if (rand(0, 1)) {

                expect(Math::remainder($number))->toBe($expected);

                continue;
            }

            expect(remainder($number))->toBe($expected);
        }

        // 
    });

    test('power method', function () {

        foreach (Datasets::mathPower() as $dataset) {

            $number = $dataset[0];
            $exponent = $dataset[1];
            $expected = $dataset[2];

            expect(Math::power(number: $number, exponent: $exponent))->toBe($expected);
        }

        // 
    });

    test('sqrt method', function () {

        foreach (Datasets::mathSqrt() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            expect(Math::sqrt(number: $number))->toBe($expected);
        }

        // 
    });

    test('roundUp method', function () {

        foreach (Datasets::mathRoundUp() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            if (rand(0, 1)) {

                expect(Math::roundUp(number: $number))->toBe($expected);

                continue;
            }

            expect(roundUp(number: $number))->toBe($expected);
            // 
        }

        // 
    });

    test('roundDown method', function () {

        foreach (Datasets::mathRoundDown() as $dataset) {

            $number = $dataset[0];
            $expected = $dataset[1];

            if (rand(0, 1)) {

                expect(Math::roundDown(number: $number))->toBe($expected);

                continue;
            }

            expect(roundDown(number: $number))->toBe($expected);

            // 
        }

        // 
    });

    test('roundClose method', function () {

        foreach (Datasets::mathRoundClose() as $dataset) {

            $number = $dataset[0];
            $precision = $dataset[1];
            $mode = $dataset[2];
            $expected = $dataset[3];

            if (rand(0, 1)) {

                expect(Math::roundClose(number: $number, precision: $precision, mode: $mode))->toBe($expected);

                continue;
            }

            expect(roundClose(number: $number, precision: $precision, mode: $mode))->toBe($expected);

            // 
        }

        // 
    });

    test('greaterThan method', function () {

        foreach (Datasets::mathGreaterThan() as $dataset) {

            $num1 = $dataset[0];
            $num2 = $dataset[1];
            $expected = $dataset[2];

            if (rand(0, 1)) {

                expect(Math::greaterThan($num1, $num2))->toBe($expected);

                continue;
            }

            expect(greaterThan($num1, $num2))->toBe($expected);

            // 
        }

        // 
    });

    test('greaterThanOrEqual method', function () {

        foreach (Datasets::mathGreaterThanOrEqual() as $dataset) {

            $num1 = $dataset[0];
            $num2 = $dataset[1];
            $expected = $dataset[2];

            if (rand(0, 1)) {

                expect(Math::greaterThanOrEqual($num1, $num2))->toBe($expected);

                continue;
            }

            expect(greaterThanOrEqual($num1, $num2))->toBe($expected);

            // 
        }

        // 
    });

    test('lessThan method', function () {

        foreach (Datasets::mathLessThan() as $dataset) {

            $num1 = $dataset[0];
            $num2 = $dataset[1];
            $expected = $dataset[2];

            if (rand(0, 1)) {

                expect(Math::lessThan($num1, $num2))->toBe($expected);

                continue;
            }

            expect(lessThan($num1, $num2))->toBe($expected);

            // 
        }

        // 
    });

    test('lessThanOrEqual method', function () {

        foreach (Datasets::mathLessThanOrEqual() as $dataset) {

            $num1 = $dataset[0];
            $num2 = $dataset[1];
            $expected = $dataset[2];

            if (rand(0, 1)) {

                expect(Math::lessThanOrEqual($num1, $num2))->toBe($expected);

                continue;
            }

            expect(lessThanOrEqual($num1, $num2))->toBe($expected);
            // 
        }

        // 
    });

    test('equal method', function () {

        foreach (Datasets::mathEqual() as $dataset) {

            $num1 = $dataset[0];
            $num2 = $dataset[1];
            $expected = $dataset[2];

            if (rand(0, 1)) {

                expect(Math::equal($num1, $num2))->toBe($expected);

                continue;
            }

            expect(equal($num1, $num2))->toBe($expected);

            // 
        }

        // 
    });

    test('notEqual method', function () {

        foreach (Datasets::mathNotEqual() as $dataset) {

            $num1 = $dataset[0];
            $num2 = $dataset[1];
            $expected = $dataset[2];

            if (rand(0, 1)) {

                expect(Math::notEqual($num1, $num2))->toBe($expected);

                continue;
            }

            expect(notEqual($num1, $num2))->toBe($expected);

            // 
        }

        // 
    });


    describe('handle exceptions', function () {

        test('invalid precision', function () {

            expect(fn() => math(20))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidPrecision');

            try {

                Math::setPrecision(precision: 20);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidPrecision');
                expect($e->getCode())->toBe(1101);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'precision' => 20,
                    'bc_scale'  => 12
                ]);
            }

            expect(fn() => math(-1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidPrecision');

            try {

                Math::setPrecision(precision: -1);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidPrecision');
                expect($e->getCode())->toBe(1101);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'precision' => -1,
                    'bc_scale'  => 12
                ]);
            }
        });

        test('very big and small number for convertScientificNumber', function () {

            expect(fn() => Math::convertScientificNumber(1.1E+9999))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber(-1.1E+9999))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber(1.1E-322))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber(-1.1E-322))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');

            // very small number when it cast to string it will be zero. the max exponent php handle CAN BE 324
            expect(Math::convertScientificNumber(1.1E-324))->toBe('0');
            expect(Math::convertScientificNumber(-1.1E-324))->toBe('0');

            try {

                Math::convertScientificNumber(1.1E+9999);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(1105);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(1105);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'convertScientificNumber',
                    'operand'       => -INF,
                    'args'          => []
                ]);
            }

            expect(fn() => Math::convertScientificNumber('1.1E+9999'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber('-1.1E+9999'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber('1.1E-324'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber('-1.1E-324'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber('1.1E-322'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::convertScientificNumber('-1.1E-322'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');

            try {

                Math::convertScientificNumber('1.1E+9999');

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(1105);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'convertScientificNumber',
                    'operand'       => '1.1E+9999',
                    'args'          => []
                ]);
            }

            try {

                Math::convertScientificNumber('-1.1E+9999');

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(1105);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'convertScientificNumber',
                    'operand'       => '-1.1E+9999',
                    'args'          => []
                ]);
            }
        });

        test('number check the input is numeric', function () {

            expect(fn() => number())->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => number('test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => number(1, 'test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => number([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => number([[1, 2], 2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => number([[1, 2]], 2))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            try {

                Math::number();

                //
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'number',
                    'operand'       => ['1', 'test'],
                    'args'          => []
                ]);
            }

            try {

                Math::number([[1, 2], 2]);

                //
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'number',
                    'operand'       => [[1, 2], 2],
                    'args'          => []
                ]);
            }
        });

        test('numberFormat check the input is numeric', function () {

            expect(fn() => numberFormat('test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => numberFormat('2,000,000.12T'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            try {

                Math::numberFormat('2,000,000.12T');

                //
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'numberFormat',
                    'operand'       => '2,000,000.12T',
                    'args'          => []
                ]);
            }
        });

        test('calc methods check the operands count', function () {

            expect(fn() => sum())->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => sum(1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => sum([1]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => sum([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => sum([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => sum([[1, 2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => sum([[]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => sum([[[1, 2]]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');

            expect(fn() => subtract())->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => subtract(1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => subtract([1]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => subtract([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => subtract([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => subtract([[1, 2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => subtract([[]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => subtract([[[1, 2]]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');

            expect(fn() => multiply())->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => multiply(1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => multiply([1]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => multiply([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => multiply([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => multiply([[1, 2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => multiply([[]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => multiply([[[1, 2]]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');

            expect(fn() => divide())->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => divide(1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => divide([1]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => divide([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => divide([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => divide([[1, 2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => divide([[]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => divide([[[1, 2]]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');

            expect(fn() => remainder())->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => remainder(1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => remainder([1]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => remainder([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => remainder([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => remainder([[1, 2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => remainder([[]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => remainder([[[1, 2]]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');

            expect(fn() => Math::power([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => Math::sqrt([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => roundUp([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => roundDown([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');
            expect(fn() => roundClose([]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentsCount');

            try {

                Math::sum();

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcadd',
                    'operand'       => [],
                    'args'          => []
                ]);
            }

            try {

                Math::subtract(1);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsub',
                    'operand'       => [1],
                    'args'          => []
                ]);
            }

            try {

                Math::multiply([1]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmul',
                    'operand'       => [1],
                    'args'          => []
                ]);
            }

            try {

                Math::divide([]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcdiv',
                    'operand'       => [],
                    'args'          => []
                ]);
            }

            try {

                Math::remainder([[1, 2]]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmod',
                    'operand'       => [[1, 2]],
                    'args'          => []
                ]);
            }

            try {

                Math::power([]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentsCount');
                expect($e->getCode())->toBe(1102);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->getWith())->toBe([
                    'method'        => 'bcround',
                    'operand'       => [],
                    'args'          => ['precision' => 0, 'mode' => RoundingMode::HalfAwayFromZero]
                ]);
            }
        });

        test('calc methods check the operands are numeric', function () {

            expect(fn() => sum([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => sum([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => sum([1], [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => sum([[1, 2], [3]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => sum(1, [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => sum('', ''))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => sum(1, 'test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => sum([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => subtract([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => subtract([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => subtract([1], [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => subtract([[1, 2], [3]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => subtract(1, [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => subtract('', ''))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => subtract(1, 'test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => subtract([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => multiply([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => multiply([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => multiply([1], [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => multiply([[1, 2], [3]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => multiply(1, [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => multiply('', ''))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => multiply(1, 'test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => multiply([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => divide([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => divide([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => divide([1], [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => divide([[1, 2], [3]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => divide(1, [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => divide('', ''))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => divide(1, 'test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => divide([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => remainder([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => remainder([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => remainder([1], [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => remainder([[1, 2], [3]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => remainder(1, [2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => remainder('', ''))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => remainder(1, 'test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => remainder([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => Math::power('test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::power([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::power([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::power([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::power([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => Math::sqrt('test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::sqrt([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::sqrt([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::sqrt([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => Math::sqrt([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => roundUp('test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundUp([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundUp([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundUp([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundUp([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => roundDown('test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundDown([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundDown([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundDown([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundDown([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            expect(fn() => roundClose('test'))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundClose([1, 'test']))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundClose([[], []]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundClose([[1], [2]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');
            expect(fn() => roundClose([[1]]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidArgumentType');

            try {

                Math::sum(1, 'test');

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsub',
                    'operand'       => [1, 'test'],
                    'args'          => []
                ]);
            }

            try {

                Math::multiply([1], [2]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmul',
                    'operand'       => [[1], [2]],
                    'args'          => []
                ]);
            }

            try {

                Math::divide([[1], [2]]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcdiv',
                    'operand'       => [[1], [2]],
                    'args'          => []
                ]);
            }

            try {

                Math::remainder(1, 'test', 0);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcpow',
                    'operand'       => 'test',
                    'args'          => ['exponent' => 2]
                ]);
            }

            try {

                Math::sqrt([1, 'test']);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsqrt',
                    'operand'       => [1, 'test'],
                    'args'          => []
                ]);
            }

            try {

                Math::roundUp([[], []]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcceil',
                    'operand'       => [[], []],
                    'args'          => []
                ]);
            }

            try {

                Math::roundDown([[1], [2]]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcfloor',
                    'operand'       => [[1], [2]],
                    'args'          => []
                ]);
            }

            try {

                Math::roundClose([[1]]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcround',
                    'operand'       => [[1]],
                    'args'          => ['precision' => 0, 'mode' => RoundingMode::HalfAwayFromZero]
                ]);
            }
        });

        test('divide and remainder check the operands are not zero', function () {

            expect(fn() => divide(1, 0))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionDivisionByZero');
            expect(fn() => remainder(1, 0))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionDivisionByZero');
            expect(fn() => divide([1, 2, 0]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionDivisionByZero');
            expect(fn() => remainder([1, 3, 0]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionDivisionByZero');

            try {

                Math::divide(1, 2, 0);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
                expect($e->getCode())->toBe(1104);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
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
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
                expect($e->getCode())->toBe(1104);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcmod',
                    'operand'       => [1, 2, 0],
                    'args'          => []
                ]);
            }

            //
        });

        test('power and sqrt check the value and args value', function () {

            expect(fn() => Math::power(0, -1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionDivisionByZero');
            expect(fn() => Math::power([1, 0], -1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionDivisionByZero');

            expect(fn() => Math::sqrt(-1))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');
            expect(fn() => Math::sqrt([1, -1]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionInvalidValueError');

            try {

                Math::power(0, -1);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
                expect($e->getCode())->toBe(1104);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcpow',
                    'operand'       => 0,
                    'args'          => ['exponent' => -1]
                ]);
            }

            try {

                Math::power([1, 0], -1);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionDivisionByZero');
                expect($e->getCode())->toBe(1104);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcpow',
                    'operand'       => [1, 0],
                    'args'          => ['exponent' => -1]
                ]);
            }

            try {

                Math::sqrt(-1);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(1105);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsqrt',
                    'operand'       => -1,
                    'args'          => []
                ]);
            }

            try {

                Math::sqrt([1, -1]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidValueError');
                expect($e->getCode())->toBe(1105);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcsqrt',
                    'operand'       => [1, -1],
                    'args'          => []
                ]);
            }
        });

        test('operands for comparing must be numeric', function () {

            expect(fn() => greaterThan(1, 'test'))->toThrow(MathCalculationException::class);
            expect(fn() => greaterThanOrEqual('test', 1))->toThrow(MathCalculationException::class);

            expect(fn() => lessThan(1, 'test'))->toThrow(MathCalculationException::class);
            expect(fn() => lessThanOrEqual('test', 1))->toThrow(MathCalculationException::class);

            expect(fn() => equal(1, 'test'))->toThrow(MathCalculationException::class);
            expect(fn() => notEqual('test', 1))->toThrow(MathCalculationException::class);

            try {

                Math::greaterThan('test', 1);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bccomp',
                    'operand'       => ['test', '1'],
                    'args'          => []
                ]);
            }

            try {

                Math::greaterThanOrEqual(1, 'test');

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bccomp',
                    'operand'       => ['1', 'test'],
                    'args'          => []
                ]);
            }

            try {

                Math::lessThan('test', 1);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bccomp',
                    'operand'       => ['test', '1'],
                    'args'          => []
                ]);
            }

            try {

                Math::lessThanOrEqual(1, 'test');

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bccomp',
                    'operand'       => ['1', 'test'],
                    'args'          => []
                ]);
            }

            try {

                Math::equal('test', 1);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bccomp',
                    'operand'       => ['test', '1'],
                    'args'          => []
                ]);
            }

            try {

                Math::notEqual(1, 'test');

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidArgumentType');
                expect($e->getCode())->toBe(1103);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bccomp',
                    'operand'       => ['1', 'test'],
                    'args'          => []
                ]);
            }
        });

        test('unsupported calc method throws MathCalculationException with code 1106', function () {

            $handler = new FooinoMathHandler();

            $reflector = new \ReflectionMethod($handler, 'calc');

            expect(fn() => $reflector->invoke($handler, 'bcfoobar', [1, 2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionUnsupportedFunction');

            try {

                $reflector->invoke($handler, 'bcfoobar', [1, 2]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionUnsupportedFunction');
                expect($e->getCode())->toBe(1106);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcfoobar',
                    'operand'       => [1, 2],
                    'args'          => []
                ]);
            }


            $handler = new FooinoMathHandler();

            $reflector = new \ReflectionMethod($handler, 'calcTwoOperand');

            expect(fn() => $reflector->invoke($handler, 'bcfoobar', [1, 2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionUnsupportedFunction');

            try {

                $reflector->invoke($handler, 'bcfoobar', [1, 2]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionUnsupportedFunction');
                expect($e->getCode())->toBe(1106);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcfoobar',
                    'operand'       => [1, 2],
                    'args'          => []
                ]);
            }

            $handler = new FooinoMathHandler();

            $reflector = new \ReflectionMethod($handler, 'calcOneOperand');

            expect(fn() => $reflector->invoke($handler, 'bcfoobar', [1, 2]))->toThrow(MathCalculationException::class, 'msg.mathCalculationExceptionUnsupportedFunction');

            try {

                $reflector->invoke($handler, 'bcfoobar', [1, 2]);

                // 
            } catch (MathCalculationException $e) {

                expect($e->getMessage())->toBe('msg.mathCalculationExceptionUnsupportedFunction');
                expect($e->getCode())->toBe(1106);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'        => 'bcfoobar',
                    'operand'       => [1, 2],
                    'args'          => []
                ]);
            }
        });

        // 
    });
});
