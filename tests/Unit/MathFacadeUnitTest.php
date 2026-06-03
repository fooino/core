<?php

namespace Fooino\Core\Tests\Unit;

use DivisionByZeroError;
use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Facades\Math;
use TypeError;
use ValueError;

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
        expect(fn() => Math::divide(0, 0))->toThrow(DivisionByZeroError::class);
        expect(fn() => Math::divide(5, 0))->toThrow(DivisionByZeroError::class);

        expect(Math::divide(1, 0.5))->toBe('2');
        expect(Math::setPrecision(precision: 0)->divide(50, 0.4354))->toBe('114');
        expect(Math::setPrecision(precision: 0)->divide(361, 1.15))->toBe('313');
        expect(Math::divide(5, 6))->toBe('0.8333333333');
        expect(Math::divide(10, 3))->toBe('3.3333333333');
        expect(Math::divide(1, 1000000000))->toBe('0.000000001');
        expect(Math::divide(1, 111))->toBe('0.009009009');
        expect(Math::divide(1.1e+8, 1.1e-8))->toBe('10000000000000000');
        expect(Math::divide('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-1');

        expect(fn() => divide(0, 0))->toThrow(DivisionByZeroError::class);
        expect(fn() => divide(5, 0))->toThrow(DivisionByZeroError::class);

        expect(divide(1, 0.5))->toBe('2');
        expect(math(precision: 0)->divide(50, 0.4354))->toBe('114');
        expect(math(precision: 0)->divide(361, 1.15))->toBe('313');
        expect(divide(5, 6))->toBe('0.8333333333');
        expect(divide(10, 3))->toBe('3.3333333333');
        expect(divide(1, 1000000000))->toBe('0.000000001');
        expect(divide(1, 111))->toBe('0.009009009');
        expect(divide(1.1e+8, 1.1e-8))->toBe('10000000000000000');
        expect(divide('-1234567891234567889999999', '1234567891234567889999999'))->toBe('-1');
    });


    test('modulus method', function () {
        expect(fn() => Math::modulus(0, 0))->toThrow(DivisionByZeroError::class);
        expect(fn() => Math::modulus(5, 0))->toThrow(DivisionByZeroError::class);

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
        expect(Math::sqrt(2))->toBe('1.4142135623');
        expect(Math::sqrt(3))->toBe('1.7320508075');
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

        expect(Math::greaterThan(null, null))->toBeFalse();
        expect(Math::greaterThan(null, 0))->toBeFalse();
        expect(Math::greaterThan(0, null))->toBeFalse();
        expect(Math::greaterThan(0, 0))->toBeFalse();
        expect(Math::greaterThan(-1, 0))->toBeFalse();
        expect(Math::greaterThan(0, 5))->toBeFalse();
        expect(Math::greaterThan(0, -1))->toBeTrue();
        expect(Math::setPrecision(precision: 1)->greaterThan(1.11, 1.112))->toBeFalse();
        expect(Math::greaterThan(1.1e+8, 1.1e-8))->toBeTrue();
        expect(Math::greaterThan(1.1e+8, 1.1e+8))->toBeFalse();
        expect(Math::greaterThan(1.1e-8, 1.1e+8))->toBeFalse();

        expect(greaterThan(null, null))->toBeFalse();
        expect(greaterThan(null, 0))->toBeFalse();
        expect(greaterThan(0, null))->toBeFalse();
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
        expect(Math::greaterThanOrEqual(null, null))->toBeTrue();
        expect(Math::greaterThanOrEqual(null, 0))->toBeTrue();
        expect(Math::greaterThanOrEqual(0, null))->toBeTrue();
        expect(Math::greaterThanOrEqual(0, 0))->toBeTrue();
        expect(Math::greaterThanOrEqual(-1, 0))->toBeFalse();
        expect(Math::greaterThanOrEqual(0, 5))->toBeFalse();
        expect(Math::greaterThanOrEqual(0, -1))->toBeTrue();
        expect(math(precision: 1)->greaterThanOrEqual(1.112, 1.112))->toBeTrue();
        expect(Math::greaterThanOrEqual(1.1e+8, 1.1e-8))->toBeTrue();
        expect(Math::greaterThanOrEqual(1.1e+8, 1.1e+8))->toBeTrue();
        expect(Math::greaterThanOrEqual(1.1e-8, 1.1e+8))->toBeFalse();

        expect(greaterThanOrEqual(null, null))->toBeTrue();
        expect(greaterThanOrEqual(null, 0))->toBeTrue();
        expect(greaterThanOrEqual(0, null))->toBeTrue();
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
        expect(Math::lessThan(null, null))->toBeFalse();
        expect(Math::lessThan(null, 0))->toBeFalse();
        expect(Math::lessThan(0, null))->toBeFalse();
        expect(Math::lessThan(0, 0))->toBeFalse();
        expect(Math::lessThan(-1, 0))->toBeTrue();
        expect(Math::lessThan(0, 5))->toBeTrue();
        expect(Math::lessThan(0, -1))->toBeFalse();
        expect(Math::setPrecision(precision: 1)->lessThan(1.112, 1.11))->toBeFalse();
        expect(Math::lessThan(1.1e-8, 1.1e+8))->toBeTrue();
        expect(Math::lessThan(1.1e+8, 1.1e+8))->toBeFalse();
        expect(Math::lessThan(1.1e+8, 1.1e-8))->toBeFalse();

        expect(lessThan(null, null))->toBeFalse();
        expect(lessThan(null, 0))->toBeFalse();
        expect(lessThan(0, null))->toBeFalse();
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

        expect(Math::lessThanOrEqual(null, null))->toBeTrue();
        expect(Math::lessThanOrEqual(null, 0))->toBeTrue();
        expect(Math::lessThanOrEqual(0, null))->toBeTrue();
        expect(Math::lessThanOrEqual(0, 0))->toBeTrue();
        expect(Math::lessThanOrEqual(-1, 0))->toBeTrue();
        expect(Math::lessThanOrEqual(0, 5))->toBeTrue();
        expect(Math::lessThanOrEqual(0, -1))->toBeFalse();
        expect(Math::setPrecision(precision: 1)->lessThanOrEqual(1.11, 1.11))->toBeTrue();
        expect(Math::lessThanOrEqual(1.1e-8, 1.1e+8))->toBeTrue();
        expect(Math::lessThanOrEqual(1.1e+8, 1.1e+8))->toBeTrue();
        expect(Math::lessThanOrEqual(1.1e+8, 1.1e-8))->toBeFalse();

        expect(lessThanOrEqual(null, null))->toBeTrue();
        expect(lessThanOrEqual(null, 0))->toBeTrue();
        expect(lessThanOrEqual(0, null))->toBeTrue();
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
        expect(Math::equal(null, null))->toBeTrue();
        expect(Math::equal(null, 0))->toBeTrue();
        expect(Math::equal(0, null))->toBeTrue();
        expect(Math::equal(0, 0))->toBeTrue();
        expect(Math::equal(-1, 0))->toBeFalse();
        expect(Math::equal(0, 5))->toBeFalse();
        expect(Math::equal(0, -1))->toBeFalse();
        expect(Math::setPrecision(precision: 1)->equal(1.112, 1.113))->toBeFalse();
        expect(Math::equal(1.1e-8, 1.1e+8))->toBeFalse();
        expect(Math::equal(1.1e+8, 1.1e+8))->toBeTrue();
        expect(Math::equal(1.1e+8, 1.1e-8))->toBeFalse();

        expect(equal(null, null))->toBeTrue();
        expect(equal(null, 0))->toBeTrue();
        expect(equal(0, null))->toBeTrue();
        expect(equal(0, 0))->toBeTrue();
        expect(equal(-1, 0))->toBeFalse();
        expect(equal(0, 5))->toBeFalse();
        expect(equal(0, -1))->toBeFalse();
        expect(math(precision: 1)->equal(1.112, 1.113))->toBeFalse();
        expect(equal(1.1e-8, 1.1e+8))->toBeFalse();
        expect(equal(1.1e+8, 1.1e+8))->toBeTrue();
        expect(equal(1.1e+8, 1.1e-8))->toBeFalse();

        expect(Math::notEqual(null, null))->toBeFalse();
        expect(Math::notEqual(null, 0))->toBeFalse();
        expect(Math::notEqual(0, null))->toBeFalse();
        expect(Math::notEqual(0, 0))->toBeFalse();
        expect(Math::notEqual(-1, 0))->toBeTrue();
        expect(Math::notEqual(0, 5))->toBeTrue();
        expect(Math::notEqual(0, -1))->toBeTrue();
        expect(Math::setPrecision(precision: 1)->notEqual(1.112, 1.113))->toBeTrue();
        expect(Math::notEqual(1, 2))->toBeTrue();
        expect(Math::notEqual(2, 2))->toBeFalse();

        expect(notEqual(null, null))->toBeFalse();
        expect(notEqual(null, 0))->toBeFalse();
        expect(notEqual(0, null))->toBeFalse();
        expect(notEqual(0, 0))->toBeFalse();
        expect(notEqual(-1, 0))->toBeTrue();
        expect(notEqual(0, 5))->toBeTrue();
        expect(notEqual(0, -1))->toBeTrue();
        expect(math(precision: 1)->notEqual(1.112, 1.113))->toBeTrue();
        expect(notEqual(1, 2))->toBeTrue();
        expect(notEqual(2, 2))->toBeFalse();
    });
});
