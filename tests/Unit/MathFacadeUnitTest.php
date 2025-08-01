<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Facades\Math;
use Fooino\Core\Tests\TestCase;
use DivisionByZeroError;
use TypeError;
use ValueError;

class MathFacadeUnitTest extends TestCase
{
    public function test_set_precision_method()
    {
        $this->assertEquals(Math::getPrecision(), 10);
        $this->assertEquals(math()->getPrecision(), 10);

        $this->assertEquals(Math::instance(precision: 8)->getPrecision(), 8);
        $this->assertEquals(math(precision: 8)->getPrecision(), 8);
    }

    public function test_set_trim_trailing_zeroes()
    {
        $this->assertEquals(Math::getTrimTrailingZeroes(), true);
        $this->assertEquals(math()->getTrimTrailingZeroes(), true);

        $this->assertEquals(Math::instance(trimTrailingZeroes: false)->getTrimTrailingZeroes(), false);
        $this->assertEquals(math(trimTrailingZeroes: false)->getTrimTrailingZeroes(), false);
    }

    public function test_trim_trailing_zeros_method()
    {
        $this->assertEquals(Math::trimTrailingZeroes(11), '11');
        $this->assertEquals(Math::trimTrailingZeroes(11.11), '11.11');
        $this->assertEquals(Math::trimTrailingZeroes(11.1100000000), '11.11');
        $this->assertEquals(Math::trimTrailingZeroes(11.0000000000), '11');
        $this->assertEquals(Math::trimTrailingZeroes("11-0000000000", '-'), '11');
        $this->assertEquals(Math::trimTrailingZeroes("11-2100000000", '-'), '11-21');
        $this->assertEquals(Math::instance(trimTrailingZeroes: false)->trimTrailingZeroes(11.0000000000), '11'); // since the the number is float the convertScientificNumber cast it to string without zero
        $this->assertEquals(Math::instance(trimTrailingZeroes: false)->trimTrailingZeroes('11.0000000000'), '11.0000000000');
        $this->assertEquals(Math::trimTrailingZeroes("1.1e+8"), '110000000');
        $this->assertEquals(Math::trimTrailingZeroes("-1.1e+8"), '-110000000');
        $this->assertEquals(Math::trimTrailingZeroes("1.1e-8"), '0.000000011');
        $this->assertEquals(Math::trimTrailingZeroes(0), '0');
        $this->assertEquals(Math::trimTrailingZeroes(0.0), '0');
        $this->assertEquals(Math::trimTrailingZeroes(null), '0');
        $this->assertEquals(Math::trimTrailingZeroes('test'), 'test');


        $this->assertEquals(trimTrailingZeroes(11), '11');
        $this->assertEquals(trimTrailingZeroes(11.11), '11.11');
        $this->assertEquals(trimTrailingZeroes(11.1100000000), '11.11');
        $this->assertEquals(trimTrailingZeroes(11.0000000000), '11');
        $this->assertEquals(trimTrailingZeroes("11-0000000000", '-'), '11');
        $this->assertEquals(trimTrailingZeroes("11-2100000000", '-'), '11-21');
        $this->assertEquals(math(trimTrailingZeroes: false)->trimTrailingZeroes(11.0000000000), '11'); // since the the number is float the convertScientificNumber cast it to string without zero
        $this->assertEquals(math(trimTrailingZeroes: false)->trimTrailingZeroes('11.0000000000'), '11.0000000000');
        $this->assertEquals(trimTrailingZeroes("1.1e+8"), '110000000');
        $this->assertEquals(trimTrailingZeroes("-1.1e+8"), '-110000000');
        $this->assertEquals(trimTrailingZeroes("1.1e-8"), '0.000000011');
        $this->assertEquals(trimTrailingZeroes(0), '0');
        $this->assertEquals(trimTrailingZeroes(0.0), '0');
        $this->assertEquals(trimTrailingZeroes(null), '0');
        $this->assertEquals(trimTrailingZeroes('test'), 'test');
    }

    public function test_convert_scientific_number_method()
    {
        $this->assertEquals(Math::convertScientificNumber(11.000000), '11');
        $this->assertEquals(Math::convertScientificNumber(11), '11');
        $this->assertEquals(Math::convertScientificNumber(-11), '-11');
        $this->assertEquals(Math::convertScientificNumber(11.11), '11.11');
        $this->assertEquals(Math::convertScientificNumber('1.1e+8'), '110000000.0000000000');
        $this->assertEquals(Math::convertScientificNumber(1.1e+8), '110000000');
        $this->assertEquals(Math::convertScientificNumber('1.1e+20'), '110000000000000000000.0000000000');
        $this->assertEquals(Math::convertScientificNumber('1.1e-8'), '0.0000000110');
        $this->assertEquals(Math::convertScientificNumber('1.1e-8'), '0.0000000110');
        $this->assertEquals(Math::convertScientificNumber('1.1e-11'), '0.0000000000');
        $this->assertEquals(Math::convertScientificNumber('20.1e+20'), '2010000000000000000000.0000000000');
        $this->assertEquals(Math::convertScientificNumber('-1.1e-11'), '-0.0000000000');
        $this->assertEquals(Math::convertScientificNumber(null), '0');
        $this->assertEquals(Math::convertScientificNumber('test'), 'test');
    }

    public function test_number_math_method()
    {
        $this->assertEquals(Math::instance(precision: 4)->number(0.44015042), '0.4401');
        $this->assertEquals(Math::number('.44015042'), '0.44015042');
        $this->assertEquals(Math::number(11.000001000), '11.000001');
        $this->assertEquals(Math::number(1.1e+8), '110000000');
        $this->assertEquals(Math::number(1.1e+20), '110000000000000000000');
        $this->assertEquals(Math::instance(trimTrailingZeroes: false)->number('1.1e+8'), '110000000.0000000000');
        $this->assertEquals(Math::instance(trimTrailingZeroes: false)->number(1.1e+8), '110000000.0');
        $this->assertEquals(Math::number('test'), 'test');
        $this->assertEquals(Math::number('foo.bar'), 'foo.bar');
        $this->assertEquals(Math::number(null), 0);
        $this->assertEquals(Math::number(0), 0);
        $this->assertEquals(Math::number(0.0), 0);

        $this->assertEquals(math(precision: 4)->number(0.44015042), '0.4401');
        $this->assertEquals(number('.44015042'), '0.44015042');
        $this->assertEquals(number(11.000001000), '11.000001');
        $this->assertEquals(number(1.1e+8), '110000000');
        $this->assertEquals(number(1.1e+20), '110000000000000000000');
        $this->assertEquals(math(trimTrailingZeroes: false)->number('1.1e+8'), '110000000.0000000000');
        $this->assertEquals(math(trimTrailingZeroes: false)->number(1.1e+8), '110000000.0');
        $this->assertEquals(number('test'), 'test');
        $this->assertEquals(number(null), 0);
        $this->assertEquals(number(0), 0);
        $this->assertEquals(number(0.0), 0);
    }

    public function test_number_format_method()
    {
        $this->assertEquals(Math::numberFormat(number: null), 0);
        $this->assertEquals(Math::numberFormat(number: 0), 0);
        $this->assertEquals(Math::numberFormat(number: 0.0), 0);
        $this->assertEquals(Math::numberFormat(number: 1.1e-8), "0.000000011");
        $this->assertEquals(Math::numberFormat(number: 1.1e+8), "110,000,000");
        $this->assertEquals(Math::numberFormat(number: 5000000), "5,000,000");
        $this->assertEquals(Math::numberFormat(number: 5000000.50), "5,000,000.5");
        $this->assertEquals(Math::numberFormat(number: 5000000.5), "5,000,000.5");
        $this->assertEquals(Math::numberFormat(number: 5000000.05), "5,000,000.05");
        $this->assertEquals(Math::numberFormat(number: 5000000.015), "5,000,000.015");
        $this->assertEquals(Math::numberFormat(number: 5000000.0150), "5,000,000.015");
        $this->assertEquals(Math::numberFormat(number: 5000000.01501), "5,000,000.01501");
        $this->assertEquals(Math::numberFormat(number: 1.1e+20, thousandsSeparator: "|"), "110|000|000|000|000|000|000");
        $this->assertEquals(Math::numberFormat(number: 1.1e-8, divisor: 10), "0.0000000011");
        $this->assertEquals(Math::numberFormat(number: 100000, divisor: 10000), "10");
        $this->assertEquals(Math::numberFormat(number: 5000000.50, divisor: 1000), "5,000.0005");
        $this->assertThrows(fn() => Math::numberFormat(number: 5000000.50, divisor: 0), DivisionByZeroError::class);
        $this->assertEquals(Math::numberFormat(number: 1.1e+20, thousandsSeparator: "|", divisor: 100), "1|100|000|000|000|000|000");
        $this->assertThrows(fn() => Math::numberFormat(number: 'test'), TypeError::class);


        $this->assertEquals(numberFormat(number: null), 0);
        $this->assertEquals(numberFormat(number: 0), 0);
        $this->assertEquals(numberFormat(number: 0.0), 0);
        $this->assertEquals(numberFormat(number: 1.1e-8), "0.000000011");
        $this->assertEquals(numberFormat(number: 1.1e+8), "110,000,000");
        $this->assertEquals(numberFormat(number: 5000000), "5,000,000");
        $this->assertEquals(numberFormat(number: 5000000.50), "5,000,000.5");
        $this->assertEquals(numberFormat(number: 5000000.5), "5,000,000.5");
        $this->assertEquals(numberFormat(number: 5000000.05), "5,000,000.05");
        $this->assertEquals(numberFormat(number: 5000000.015), "5,000,000.015");
        $this->assertEquals(numberFormat(number: 5000000.0150), "5,000,000.015");
        $this->assertEquals(numberFormat(number: 5000000.01501), "5,000,000.01501");
        $this->assertEquals(numberFormat(number: 1.1e+20, thousandsSeparator: "|"), "110|000|000|000|000|000|000");
        $this->assertEquals(numberFormat(number: 1.1e-8, divisor: 10), "0.0000000011");
        $this->assertEquals(numberFormat(number: 100000, divisor: 10000), "10");
        $this->assertEquals(numberFormat(number: 5000000.50, divisor: 1000), "5,000.0005");
        $this->assertEquals(numberFormat(number: 1.1e+20, thousandsSeparator: "|", divisor: 100), "1|100|000|000|000|000|000");
        $this->assertThrows(fn() => numberFormat(number: 'test'), TypeError::class);
    }

    public function test_add_two_numbers_method()
    {
        $this->assertEquals(Math::instance(precision: 0)->add(5.599, 5.499), 11);
        $this->assertEquals(Math::instance(precision: 5)->add(5.599, 5.499), 11.098);
        $this->assertEquals(Math::instance(precision: 2)->add(5.599, -5.499), 0.1);
        $this->assertEquals(Math::add(1.1e+8, 1.1e-8), "110000000.000000011");
        $this->assertEquals(Math::add(1.1e+8, null), "110000000");
        $this->assertEquals(Math::add(null, null), '0');
        $this->assertEquals(Math::add(0, 0), '0');
        $this->assertEquals(Math::add(0.0, 0.0), '0');
        $this->assertEquals(Math::add(1.1e+20, 1.1e-8), '110000000000000000000.000000011');
        $this->assertEquals(Math::add('0', '1234567891234567889999999'), '1234567891234567889999999');
        $this->assertEquals(Math::add('1234567891234567889999999', '0'), '1234567891234567889999999');
        $this->assertEquals(Math::add('1234567891234567889999999', '-1234567891234567889999999'), '0');
        $this->assertEquals(Math::add('-1234567891234567889999999', '1234567891234567889999999'), '0');
        $this->assertEquals(Math::add('1234567891234567889999999.11', '1234567891234567889999999.0011'), '2469135782469135779999998.1111');
        $this->assertEquals(Math::add('1234567891234567889999999.00000000011', '1234567891234567889999999.00000000019'), '2469135782469135779999998.0000000003');


        $this->assertEquals(math(precision: 0)->add(5.599, 5.499), 11);
        $this->assertEquals(math(precision: 5)->add(5.599, 5.499), 11.098);
        $this->assertEquals(math(precision: 2)->add(5.599, -5.499), 0.1);
        $this->assertEquals(add(1.1e+8, 1.1e-8), "110000000.000000011");
        $this->assertEquals(add(1.1e+8, null), "110000000");
        $this->assertEquals(add(null, null), '0');
        $this->assertEquals(add(0, 0), '0');
        $this->assertEquals(add(0.0, 0.0), '0');
        $this->assertEquals(add(1.1e+20, 1.1e-8), '110000000000000000000.000000011');
        $this->assertEquals(add('0', '1234567891234567889999999'), '1234567891234567889999999');
        $this->assertEquals(add('1234567891234567889999999', '0'), '1234567891234567889999999');
        $this->assertEquals(add('1234567891234567889999999', '-1234567891234567889999999'), '0');
        $this->assertEquals(add('-1234567891234567889999999', '1234567891234567889999999'), '0');
        $this->assertEquals(add('1234567891234567889999999.11', '1234567891234567889999999.0011'), '2469135782469135779999998.1111');
        $this->assertEquals(add('1234567891234567889999999.00000000011', '1234567891234567889999999.00000000019'), '2469135782469135779999998.0000000003');
    }

    public function test_subtract_two_numbers_method()
    {
        $this->assertEquals(Math::subtract(5, 6), -1);
        $this->assertEquals(Math::instance(precision: 0)->subtract(5.599, 5.499), 0.0);
        $this->assertEquals(Math::instance(precision: 4)->subtract(5.599, 5.499), 0.1);
        $this->assertEquals(Math::subtract(1.1e+8, 1.1e-8), 109999999.99999998);
        $this->assertEquals(Math::subtract(1.1e+20, 1.1e-8), 109999999999999999999.99999998);
        $this->assertEquals(Math::subtract(null, null), '0');
        $this->assertEquals(Math::subtract(0, 0), '0');
        $this->assertEquals(Math::subtract(0.0, 0.0), '0');
        $this->assertEquals(Math::subtract('0', '1234567891234567889999999'), '-1234567891234567889999999');
        $this->assertEquals(Math::subtract('1234567891234567889999999', '0'), '1234567891234567889999999');
        $this->assertEquals(Math::subtract('1234567891234567889999999', '1234567891234567889999999'), '0');
        $this->assertEquals(Math::subtract('-1234567891234567889999999', '1234567891234567889999999'), '-2469135782469135779999998');
        $this->assertEquals(Math::subtract('1234567891234567889999999.11', '-1234567891234567889999999.0011'), '2469135782469135779999998.1111');
        $this->assertEquals(Math::subtract('1234567891234567889999999.00000000011', '-1234567891234567889999999.00000000019'), '2469135782469135779999998.0000000003');
    }

    public function test_multiply_two_numbers_method()
    {
        $this->assertEquals(Math::instance(precision: 1)->multiply(5.125, 6.11), 31.3);
        $this->assertEquals(Math::instance(precision: 2)->multiply(5.123456789, 6.123456789), 31.37);
        $this->assertEquals(Math::multiply(5.125, 6.11), 31.31375);
        $this->assertEquals(Math::multiply(1.1e+8, 1.1e-8), 1.21);
        $this->assertEquals(Math::multiply('1234567891234567889999999', 0), '0');
        $this->assertEquals(Math::multiply(0, '1234567891234567889999999'), '0');
        $this->assertEquals(Math::multiply('1234567891234567889999999', 1), '1234567891234567889999999');
        $this->assertEquals(Math::multiply(1, '1234567891234567889999999'), '1234567891234567889999999');
        $this->assertEquals(Math::multiply('1234567891234567889999999', '-1234567891234567889999999'), '-1524157878067367851562259605883269630864220000001');
        $this->assertEquals(Math::multiply('-1234567891234567889999999', '1234567891234567889999999'), '-1524157878067367851562259605883269630864220000001');
        $this->assertEquals(Math::multiply('1234567891234567889999999', '1234567891234567889999999'), '1524157878067367851562259605883269630864220000001');
        $this->assertEquals(Math::multiply(null, null), '0');
        $this->assertEquals(Math::multiply(0, 0), '0');
        $this->assertEquals(Math::multiply(0.0, 0.0), '0');


        $this->assertEquals(math(precision: 1)->multiply(5.125, 6.11), 31.3);
        $this->assertEquals(math(precision: 2)->multiply(5.123456789, 6.123456789), 31.37);
        $this->assertEquals(multiply(5.125, 6.11), 31.31375);
        $this->assertEquals(multiply(1.1e+8, 1.1e-8), 1.21);
        $this->assertEquals(multiply('1234567891234567889999999', 0), '0');
        $this->assertEquals(multiply(0, '1234567891234567889999999'), '0');
        $this->assertEquals(multiply('1234567891234567889999999', 1), '1234567891234567889999999');
        $this->assertEquals(multiply(1, '1234567891234567889999999'), '1234567891234567889999999');
        $this->assertEquals(multiply('1234567891234567889999999', '-1234567891234567889999999'), '-1524157878067367851562259605883269630864220000001');
        $this->assertEquals(multiply('-1234567891234567889999999', '1234567891234567889999999'), '-1524157878067367851562259605883269630864220000001');
        $this->assertEquals(multiply('1234567891234567889999999', '1234567891234567889999999'), '1524157878067367851562259605883269630864220000001');
        $this->assertEquals(multiply(null, null), '0');
        $this->assertEquals(multiply(0, 0), '0');
        $this->assertEquals(multiply(0.0, 0.0), '0');
    }

    public function test_divide_two_numbers_method()
    {
        $this->assertThrows(fn() => Math::divide(null, null), DivisionByZeroError::class);
        $this->assertThrows(fn() => Math::divide(null, 0), DivisionByZeroError::class);
        $this->assertThrows(fn() => Math::divide(0, null), DivisionByZeroError::class);
        $this->assertThrows(fn() => Math::divide(0, 0), DivisionByZeroError::class);
        $this->assertThrows(fn() => Math::divide(5, 0), DivisionByZeroError::class);

        $this->assertEquals(Math::divide(1, 0.5), 2);
        $this->assertEquals(Math::instance(precision: 0)->divide(50, 0.4354), 114);
        $this->assertEquals(Math::instance(precision: 0)->divide(361, 1.15), 313);
        $this->assertEquals(Math::divide(5, 6), 0.8333333333);
        $this->assertEquals(Math::divide(10, 3), 3.3333333333);
        $this->assertEquals(Math::divide(1, 1000000000), '0.000000001');
        $this->assertEquals(Math::divide(1, 111), '0.009009009');
        $this->assertEquals(Math::divide(1.1e+8, 1.1e-8), 10000000000000000);
        $this->assertEquals(Math::divide('-1234567891234567889999999', '1234567891234567889999999'), '-1');

        $this->assertThrows(fn() => divide(null, null), DivisionByZeroError::class);
        $this->assertThrows(fn() => divide(null, 0), DivisionByZeroError::class);
        $this->assertThrows(fn() => divide(0, null), DivisionByZeroError::class);
        $this->assertThrows(fn() => divide(0, 0), DivisionByZeroError::class);
        $this->assertThrows(fn() => divide(5, 0), DivisionByZeroError::class);

        $this->assertEquals(divide(1, 0.5), 2);
        $this->assertEquals(math(precision: 0)->divide(50, 0.4354), 114);
        $this->assertEquals(math(precision: 0)->divide(361, 1.15), 313);
        $this->assertEquals(divide(5, 6), 0.8333333333);
        $this->assertEquals(divide(10, 3), 3.3333333333);
        $this->assertEquals(divide(1, 1000000000), '0.000000001');
        $this->assertEquals(divide(1, 111), '0.009009009');
        $this->assertEquals(divide(1.1e+8, 1.1e-8), 10000000000000000);
        $this->assertEquals(divide('-1234567891234567889999999', '1234567891234567889999999'), '-1');
    }

    public function test_modulus_two_numbers_method()
    {
        $this->assertThrows(fn() => Math::modulus(null, null), DivisionByZeroError::class);
        $this->assertThrows(fn() => Math::modulus(null, 0), DivisionByZeroError::class);
        $this->assertThrows(fn() => Math::modulus(0, null), DivisionByZeroError::class);
        $this->assertThrows(fn() => Math::modulus(0, 0), DivisionByZeroError::class);
        $this->assertThrows(fn() => Math::modulus(5, 0), DivisionByZeroError::class);

        $this->assertEquals(Math::modulus(12, 5), 2);
        $this->assertEquals(Math::modulus(5, 6), 5);
        $this->assertEquals(Math::modulus(1.1e+8, 1.1e-8), 0);
    }

    public function test_power_two_numbers_method()
    {
        $this->assertEquals(Math::power(2, -3), 0.125);
        $this->assertEquals(Math::power(2, -2), 0.25);
        $this->assertEquals(Math::power(2, 3), 8);
        $this->assertEquals(Math::power(2, 0), 1);
        $this->assertEquals(Math::power(0, 2), 0);
        $this->assertEquals(Math::power(0, 0), 1);
        $this->assertEquals(Math::power(null, null), 1);
        $this->assertEquals(Math::power(null, 0), 1);
        $this->assertEquals(Math::power(0, null), 1);
        $this->assertEquals(Math::power(1, 20), 1);
        $this->assertEquals(Math::power('1234567891234567889999999', 2), '1524157878067367851562259605883269630864220000001');
        $this->assertEquals(Math::power('1234567891234567889999999', 3), '1881676377434183981909558127466713752376807174114547646517403703669999999');
        $this->assertThrows(fn() => Math::power(10, 0.5), ValueError::class);
    }

    public function test_sqrt_method()
    {
        $this->assertEquals(Math::sqrt(0), 0);
        $this->assertEquals(Math::sqrt(1), 1);
        $this->assertEquals(Math::sqrt(2), '1.4142135623');
        $this->assertEquals(Math::sqrt(3), '1.7320508075');
        $this->assertEquals(Math::sqrt(4), 2);
        $this->assertEquals(Math::sqrt(9), 3);
        $this->assertEquals(Math::sqrt(16), 4);
        $this->assertEquals(Math::sqrt('1524157878067367851562259605883269630864220000001'), '1234567891234567889999999');
        $this->assertThrows(fn() => Math::sqrt(-2), ValueError::class);
    }


    public function test_round_up_method()
    {
        $this->assertEquals(Math::roundUp(null), 0);
        $this->assertEquals(Math::roundUp(0), 0);
        $this->assertEquals(Math::roundUp(1), 1);
        $this->assertEquals(Math::roundUp(1.1), 2);
        $this->assertEquals(Math::roundUp(-1.1), -1);
        $this->assertEquals(Math::roundUp(1.9), 2);
        $this->assertEquals(Math::roundUp(-1.9), -1);
        $this->assertEquals(Math::roundUp(1.1e+8), 110000000);
        $this->assertEquals(Math::roundUp(1.1e-8), 1);

        $this->assertEquals(roundUp(null), 0);
        $this->assertEquals(roundUp(0), 0);
        $this->assertEquals(roundUp(1), 1);
        $this->assertEquals(roundUp(1.1), 2);
        $this->assertEquals(roundUp(-1.1), -1);
        $this->assertEquals(roundUp(1.9), 2);
        $this->assertEquals(roundUp(-1.9), -1);
        $this->assertEquals(roundUp(1.1e+8), 110000000);
        $this->assertEquals(roundUp(1.1e-8), 1);
    }

    public function test_round_down_method()
    {
        $this->assertEquals(Math::roundDown(null), 0);
        $this->assertEquals(Math::roundDown(0), 0);
        $this->assertEquals(Math::roundDown(1), 1);
        $this->assertEquals(Math::roundDown(1.1), 1);
        $this->assertEquals(Math::roundDown(-1.1), -2);
        $this->assertEquals(Math::roundDown(1.9), 1);
        $this->assertEquals(Math::roundDown(-1.9), -2);
        $this->assertEquals(Math::roundDown(1.1e+8), 110000000);
        $this->assertEquals(Math::roundDown(1.1e-8), 0);

        $this->assertEquals(roundDown(null), 0);
        $this->assertEquals(roundDown(0), 0);
        $this->assertEquals(roundDown(1), 1);
        $this->assertEquals(roundDown(1.1), 1);
        $this->assertEquals(roundDown(-1.1), -2);
        $this->assertEquals(roundDown(1.9), 1);
        $this->assertEquals(roundDown(-1.9), -2);
        $this->assertEquals(roundDown(1.1e+8), 110000000);
        $this->assertEquals(roundDown(1.1e-8), 0);
    }

    public function test_round_close_method()
    {
        $this->assertEquals(Math::roundClose(null), 0);
        $this->assertEquals(Math::roundClose(0), 0);
        $this->assertEquals(Math::roundClose(1), 1);
        $this->assertEquals(Math::roundClose(1.1), 1);
        $this->assertEquals(Math::roundClose(-1.1), -1);
        $this->assertEquals(Math::roundClose(1.9), 2);
        $this->assertEquals(Math::roundClose(-1.9), -2);
        $this->assertEquals(Math::roundClose(1.1e+8), 110000000);
        $this->assertEquals(Math::roundClose(1.1e-8), 0);

        $this->assertEquals(roundClose(null), 0);
        $this->assertEquals(roundClose(0), 0);
        $this->assertEquals(roundClose(1), 1);
        $this->assertEquals(roundClose(1.1), 1);
        $this->assertEquals(roundClose(-1.1), -1);
        $this->assertEquals(roundClose(1.9), 2);
        $this->assertEquals(roundClose(-1.9), -2);
        $this->assertEquals(roundClose(1.1e+8), 110000000);
        $this->assertEquals(roundClose(1.1e-8), 0);
    }


    public function test_greater_than_numbers_method()
    {
        $this->assertEquals(Math::greaterThan(null, null), false);
        $this->assertEquals(Math::greaterThan(null, 0), false);
        $this->assertEquals(Math::greaterThan(0, null), false);
        $this->assertEquals(Math::greaterThan(0, 0), false);
        $this->assertEquals(Math::greaterThan(-1, 0), false);
        $this->assertEquals(Math::greaterThan(0, 5), false);
        $this->assertEquals(Math::greaterThan(0, -1), true);
        $this->assertEquals(Math::instance(precision: 1)->greaterThan(1.11, 1.112), false);
        $this->assertEquals(Math::greaterThan(1.1e+8, 1.1e-8), true);
        $this->assertEquals(Math::greaterThan(1.1e+8, 1.1e+8), false);
        $this->assertEquals(Math::greaterThan(1.1e-8, 1.1e+8), false);

        $this->assertEquals(greaterThan(null, null), false);
        $this->assertEquals(greaterThan(null, 0), false);
        $this->assertEquals(greaterThan(0, null), false);
        $this->assertEquals(greaterThan(0, 0), false);
        $this->assertEquals(greaterThan(-1, 0), false);
        $this->assertEquals(greaterThan(0, 5), false);
        $this->assertEquals(greaterThan(0, -1), true);
        $this->assertEquals(math(precision: 1)->greaterThan(1.11, 1.112), false);
        $this->assertEquals(greaterThan(1.1e+8, 1.1e-8), true);
        $this->assertEquals(greaterThan(1.1e+8, 1.1e+8), false);
        $this->assertEquals(greaterThan(1.1e-8, 1.1e+8), false);
    }

    public function test_greater_than_or_equal_numbers_method()
    {
        $this->assertEquals(Math::greaterThanOrEqual(null, null), true);
        $this->assertEquals(Math::greaterThanOrEqual(null, 0), true);
        $this->assertEquals(Math::greaterThanOrEqual(0, null), true);
        $this->assertEquals(Math::greaterThanOrEqual(0, 0), true);
        $this->assertEquals(Math::greaterThanOrEqual(-1, 0), false);
        $this->assertEquals(Math::greaterThanOrEqual(0, 5), false);
        $this->assertEquals(Math::greaterThanOrEqual(0, -1), true);
        $this->assertEquals(math(precision: 1)->greaterThanOrEqual(1.112, 1.112), true);
        $this->assertEquals(Math::greaterThanOrEqual(1.1e+8, 1.1e-8), true);
        $this->assertEquals(Math::greaterThanOrEqual(1.1e+8, 1.1e+8), true);
        $this->assertEquals(Math::greaterThanOrEqual(1.1e-8, 1.1e+8), false);

        $this->assertEquals(greaterThanOrEqual(null, null), true);
        $this->assertEquals(greaterThanOrEqual(null, 0), true);
        $this->assertEquals(greaterThanOrEqual(0, null), true);
        $this->assertEquals(greaterThanOrEqual(0, 0), true);
        $this->assertEquals(greaterThanOrEqual(-1, 0), false);
        $this->assertEquals(greaterThanOrEqual(0, 5), false);
        $this->assertEquals(greaterThanOrEqual(0, -1), true);
        $this->assertEquals(math(precision: 1)->greaterThanOrEqual(1.113, 1.112), true);
        $this->assertEquals(greaterThanOrEqual(1.1e+8, 1.1e-8), true);
        $this->assertEquals(greaterThanOrEqual(1.1e+8, 1.1e+8), true);
        $this->assertEquals(greaterThanOrEqual(1.1e-8, 1.1e+8), false);
    }

    public function test_less_than_numbers_method()
    {
        $this->assertEquals(Math::lessThan(null, null), false);
        $this->assertEquals(Math::lessThan(null, 0), false);
        $this->assertEquals(Math::lessThan(0, null), false);
        $this->assertEquals(Math::lessThan(0, 0), false);
        $this->assertEquals(Math::lessThan(-1, 0), true);
        $this->assertEquals(Math::lessThan(0, 5), true);
        $this->assertEquals(Math::lessThan(0, -1), false);
        $this->assertEquals(Math::instance(precision: 1)->lessThan(1.112, 1.11), false);
        $this->assertEquals(Math::lessThan(1.1e-8, 1.1e+8), true);
        $this->assertEquals(Math::lessThan(1.1e+8, 1.1e+8), false);
        $this->assertEquals(Math::lessThan(1.1e+8, 1.1e-8), false);

        $this->assertEquals(lessThan(null, null), false);
        $this->assertEquals(lessThan(null, 0), false);
        $this->assertEquals(lessThan(0, null), false);
        $this->assertEquals(lessThan(0, 0), false);
        $this->assertEquals(lessThan(-1, 0), true);
        $this->assertEquals(lessThan(0, 5), true);
        $this->assertEquals(lessThan(0, -1), false);
        $this->assertEquals(math(precision: 1)->lessThan(1.112, 1.11), false);
        $this->assertEquals(lessThan(1.1e-8, 1.1e+8), true);
        $this->assertEquals(lessThan(1.1e+8, 1.1e+8), false);
        $this->assertEquals(lessThan(1.1e+8, 1.1e-8), false);
    }

    public function test_less_than_or_equal_numbers_method()
    {
        $this->assertEquals(Math::lessThanOrEqual(null, null), true);
        $this->assertEquals(Math::lessThanOrEqual(null, 0), true);
        $this->assertEquals(Math::lessThanOrEqual(0, null), true);
        $this->assertEquals(Math::lessThanOrEqual(0, 0), true);
        $this->assertEquals(Math::lessThanOrEqual(-1, 0), true);
        $this->assertEquals(Math::lessThanOrEqual(0, 5), true);
        $this->assertEquals(Math::lessThanOrEqual(0, -1), false);
        $this->assertEquals(Math::instance(precision: 1)->lessThanOrEqual(1.11, 1.11), true);
        $this->assertEquals(Math::lessThanOrEqual(1.1e-8, 1.1e+8), true);
        $this->assertEquals(Math::lessThanOrEqual(1.1e+8, 1.1e+8), true);
        $this->assertEquals(Math::lessThanOrEqual(1.1e+8, 1.1e-8), false);

        $this->assertEquals(lessThanOrEqual(null, null), true);
        $this->assertEquals(lessThanOrEqual(null, 0), true);
        $this->assertEquals(lessThanOrEqual(0, null), true);
        $this->assertEquals(lessThanOrEqual(0, 0), true);
        $this->assertEquals(lessThanOrEqual(-1, 0), true);
        $this->assertEquals(lessThanOrEqual(0, 5), true);
        $this->assertEquals(lessThanOrEqual(0, -1), false);
        $this->assertEquals(math(precision: 1)->lessThanOrEqual(1.11, 1.11), true);
        $this->assertEquals(lessThanOrEqual(1.1e-8, 1.1e+8), true);
        $this->assertEquals(lessThanOrEqual(1.1e+8, 1.1e+8), true);
        $this->assertEquals(lessThanOrEqual(1.1e+8, 1.1e-8), false);
    }

    public function test_equal_numbers_method()
    {
        $this->assertEquals(Math::equal(null, null), true);
        $this->assertEquals(Math::equal(null, 0), true);
        $this->assertEquals(Math::equal(0, null), true);
        $this->assertEquals(Math::equal(0, 0), true);
        $this->assertEquals(Math::equal(-1, 0), false);
        $this->assertEquals(Math::equal(0, 5), false);
        $this->assertEquals(Math::equal(0, -1), false);
        $this->assertEquals(Math::instance(precision: 1)->equal(1.112, 1.113), false);
        $this->assertEquals(Math::equal(1.1e-8, 1.1e+8), false);
        $this->assertEquals(Math::equal(1.1e+8, 1.1e+8), true);
        $this->assertEquals(Math::equal(1.1e+8, 1.1e-8), false);

        $this->assertEquals(equal(null, null), true);
        $this->assertEquals(equal(null, 0), true);
        $this->assertEquals(equal(0, null), true);
        $this->assertEquals(equal(0, 0), true);
        $this->assertEquals(equal(-1, 0), false);
        $this->assertEquals(equal(0, 5), false);
        $this->assertEquals(equal(0, -1), false);
        $this->assertEquals(math(precision: 1)->equal(1.112, 1.113), false);
        $this->assertEquals(equal(1.1e-8, 1.1e+8), false);
        $this->assertEquals(equal(1.1e+8, 1.1e+8), true);
        $this->assertEquals(equal(1.1e+8, 1.1e-8), false);
    }

    public function test_not_equal_numbers_method()
    {
        $this->assertEquals(Math::notEqual(null, null), false);
        $this->assertEquals(Math::notEqual(null, 0), false);
        $this->assertEquals(Math::notEqual(0, null), false);
        $this->assertEquals(Math::notEqual(0, 0), false);
        $this->assertEquals(Math::notEqual(-1, 0), true);
        $this->assertEquals(Math::notEqual(0, 5), true);
        $this->assertEquals(Math::notEqual(0, -1), true);
        $this->assertEquals(Math::instance(precision: 1)->notEqual(1.112, 1.113), true);
        $this->assertEquals(Math::notEqual(1, 2), true);
        $this->assertEquals(Math::notEqual(2, 2), false);

        $this->assertEquals(notEqual(null, null), false);
        $this->assertEquals(notEqual(null, 0), false);
        $this->assertEquals(notEqual(0, null), false);
        $this->assertEquals(notEqual(0, 0), false);
        $this->assertEquals(notEqual(-1, 0), true);
        $this->assertEquals(notEqual(0, 5), true);
        $this->assertEquals(notEqual(0, -1), true);
        $this->assertEquals(math(precision: 1)->notEqual(1.112, 1.113), true);
        $this->assertEquals(notEqual(1, 2), true);
        $this->assertEquals(notEqual(2, 2), false);
    }

    public function test_decimal_place_number_method()
    {
        $this->assertEquals(Math::decimalPlaceNumber(0.000000000100), 10);
        $this->assertEquals(Math::decimalPlaceNumber(1.1e-8), 9);
        $this->assertEquals(Math::decimalPlaceNumber(1), 0);
        $this->assertEquals(Math::decimalPlaceNumber(0), 0);
        $this->assertEquals(Math::decimalPlaceNumber(null), 0);
        $this->assertEquals(Math::decimalPlaceNumber('0-0123', '-'), 4);
    }
}
