<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use DivisionByZeroError;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use stdClass;
use TypeError;
use ValueError;

class HelpersUnitTest extends TestCase
{


    public function test_math_helper()
    {
        $precision = 5;
        $trimTrailingZeroes = false;

        $result = math($precision, $trimTrailingZeroes);

        $this->assertEquals($precision, $result->getPrecision());
        $this->assertEquals($trimTrailingZeroes, $result->getTrimTrailingZeroes());
    }

    public function test_trim_trailing_zeros_helper()
    {
        $this->assertEquals(trimTrailingZeroes(11), '11');
        $this->assertEquals(trimTrailingZeroes(11.11), '11.11');
        $this->assertEquals(trimTrailingZeroes(11.1100000000), '11.11');
        $this->assertEquals(trimTrailingZeroes(11.0000000000), '11');
        $this->assertEquals(trimTrailingZeroes("11-0000000000", '-'), '11');
        $this->assertEquals(trimTrailingZeroes("11-2100000000", '-'), '11-21');
        $this->assertEquals(math(trimTrailingZeroes: false)->trimTrailingZeroes(11.0000000000), '11');
        $this->assertEquals(trimTrailingZeroes("1.1e+8"), '110000000');
        $this->assertEquals(trimTrailingZeroes("-1.1e+8"), '-110000000');
        $this->assertEquals(trimTrailingZeroes("1.1e-8"), '0.000000011');
        $this->assertEquals(trimTrailingZeroes(0), '0');
        $this->assertEquals(trimTrailingZeroes(0.0), '0');
        $this->assertEquals(trimTrailingZeroes(null), '0');
        $this->assertEquals(trimTrailingZeroes('test'), 'test');
    }

    public function test_number_format_helper()
    {
        $this->assertEquals(numberFormat(number: null), 0);
        $this->assertEquals(numberFormat(number: 0), 0);
        $this->assertEquals(numberFormat(number: 0.0), 0);
        $this->assertEquals(numberFormat(number: 1.1e-8), "0.000000011");
        $this->assertEquals(numberFormat(number: 1.1e+8), "110,000,000");
        $this->assertEquals(numberFormat(number: 1.1e+20, thousandsSeparator: "|"), "110|000|000|000|000|000|000");
        $this->assertEquals(numberFormat(number: 1.1e-8, divisor: 10), "0.0000000011");
        $this->assertEquals(numberFormat(number: 1.1e+8, divisor: 1000), "110,000");
        $this->expectException(TypeError::class);
        $this->assertEquals(numberFormat(number: 'test'), 'test');
        $this->assertEquals(numberFormat(number: 'test', divisor: 100), 'test');
    }

    public function test_number_math_helper()
    {
        $this->assertEquals(math(precision: 4)->number(0.44015042), '0.4401');
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

    public function test_add_two_numbers_helper()
    {
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

    public function test_subtract_two_numbers_helper()
    {
        $this->assertEquals(subtract(5, 6), -1);
        $this->assertEquals(math(precision: 0)->subtract(5.599, 5.499), 0.0);
        $this->assertEquals(math(precision: 4)->subtract(5.599, 5.499), 0.1);
        $this->assertEquals(subtract(1.1e+8, 1.1e-8), 109999999.99999998);
        $this->assertEquals(subtract(1.1e+20, 1.1e-8), 109999999999999999999.99999998);
        $this->assertEquals(subtract(null, null), '0');
        $this->assertEquals(subtract(0, 0), '0');
        $this->assertEquals(subtract(0.0, 0.0), '0');
        $this->assertEquals(subtract('0', '1234567891234567889999999'), '-1234567891234567889999999');
        $this->assertEquals(subtract('1234567891234567889999999', '0'), '1234567891234567889999999');
        $this->assertEquals(subtract('1234567891234567889999999', '1234567891234567889999999'), '0');
        $this->assertEquals(subtract('-1234567891234567889999999', '1234567891234567889999999'), '-2469135782469135779999998');
        $this->assertEquals(subtract('1234567891234567889999999.11', '-1234567891234567889999999.0011'), '2469135782469135779999998.1111');
        $this->assertEquals(subtract('1234567891234567889999999.00000000011', '-1234567891234567889999999.00000000019'), '2469135782469135779999998.0000000003');
    }

    public function test_multiply_two_numbers_helper()
    {
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

    public function test_divide_two_numbers_helper()
    {
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

    public function test_modulus_two_numbers_helper()
    {
        $this->assertThrows(fn() => modulus(null, null), DivisionByZeroError::class);
        $this->assertThrows(fn() => modulus(null, 0), DivisionByZeroError::class);
        $this->assertThrows(fn() => modulus(0, null), DivisionByZeroError::class);
        $this->assertThrows(fn() => modulus(0, 0), DivisionByZeroError::class);
        $this->assertThrows(fn() => modulus(5, 0), DivisionByZeroError::class);

        $this->assertEquals(modulus(12, 5), 2);
        $this->assertEquals(modulus(5, 6), 5);
        $this->assertEquals(modulus(1.1e+8, 1.1e-8), 0);
    }

    public function test_power_two_numbers_helper()
    {
        $this->assertEquals(power(2, -3), 0.125);
        $this->assertEquals(power(2, -2), 0.25);
        $this->assertEquals(power(2, 3), 8);
        $this->assertEquals(power(2, 0), 1);
        $this->assertEquals(power(0, 2), 0);
        $this->assertEquals(power(0, 0), 1);
        $this->assertEquals(power(null, null), 1);
        $this->assertEquals(power(null, 0), 1);
        $this->assertEquals(power(0, null), 1);
        $this->assertEquals(power(1, 20), 1);
        $this->assertEquals(power('1234567891234567889999999', 2), '1524157878067367851562259605883269630864220000001');
        $this->assertEquals(power('1234567891234567889999999', 3), '1881676377434183981909558127466713752376807174114547646517403703669999999');
        $this->assertThrows(fn() => power(10, 0.5), ValueError::class);
    }

    public function test_sqrt_helper()
    {
        $this->assertEquals(square(0), 0);
        $this->assertEquals(square(1), 1);
        $this->assertEquals(square(2), '1.4142135623');
        $this->assertEquals(square(3), '1.7320508075');
        $this->assertEquals(square(4), 2);
        $this->assertEquals(square(9), 3);
        $this->assertEquals(square(16), 4);
        $this->assertEquals(square('1524157878067367851562259605883269630864220000001'), '1234567891234567889999999');
    }


    public function test_greater_than_numbers_helper()
    {
        $this->assertEquals(greaterThan(null, null), false);
        $this->assertEquals(greaterThan(null, 0), false);
        $this->assertEquals(greaterThan(0, null), false);
        $this->assertEquals(greaterThan(0, 0), false);
        $this->assertEquals(greaterThan(-1, 0), false);
        $this->assertEquals(greaterThan(0, 5), false);
        $this->assertEquals(greaterThan(0, -1), true);
        $this->assertEquals(math(precision: 1)->greaterThan(1.11, 1.112), false);
        $this->assertEquals(math(precision: 1)->greaterThan(-1.112, -1.11), false);
        $this->assertEquals(greaterThan(2, 1), true);
        $this->assertEquals(greaterThan(1.1e+8, 1.1e-8), true);
        $this->assertEquals(greaterThan(1.1e+8, 1.1e+8), false);
        $this->assertEquals(greaterThan(1.1e-8, 1.1e+8), false);
    }

    public function test_greater_than_or_equal_numbers_helper()
    {
        $this->assertEquals(greaterThanOrEqual(null, null), true);
        $this->assertEquals(greaterThanOrEqual(null, 0), true);
        $this->assertEquals(greaterThanOrEqual(0, null), true);
        $this->assertEquals(greaterThanOrEqual(0, 0), true);
        $this->assertEquals(greaterThanOrEqual(-1, 0), false);
        $this->assertEquals(greaterThanOrEqual(0, 5), false);
        $this->assertEquals(greaterThanOrEqual(0, -1), true);
        $this->assertEquals(greaterThanOrEqual(1.1e+8, 1.1e-8), true);
        $this->assertEquals(greaterThanOrEqual(1.1e+8, 1.1e+8), true);
        $this->assertEquals(greaterThanOrEqual(1.1e-8, 1.1e+8), false);
    }

    public function test_less_than_numbers_helper()
    {
        $this->assertEquals(lessThan(null, null), false);
        $this->assertEquals(lessThan(null, 0), false);
        $this->assertEquals(lessThan(0, null), false);
        $this->assertEquals(lessThan(0, 0), false);
        $this->assertEquals(lessThan(-1, 0), true);
        $this->assertEquals(lessThan(0, 5), true);
        $this->assertEquals(lessThan(0, -1), false);
        $this->assertEquals(lessThan(1.1e-8, 1.1e+8), true);
        $this->assertEquals(lessThan(1.1e+8, 1.1e+8), false);
        $this->assertEquals(lessThan(1.1e+8, 1.1e-8), false);
    }

    public function test_less_than_or_equal_numbers_helper()
    {
        $this->assertEquals(lessThanOrEqual(null, null), true);
        $this->assertEquals(lessThanOrEqual(null, 0), true);
        $this->assertEquals(lessThanOrEqual(0, null), true);
        $this->assertEquals(lessThanOrEqual(0, 0), true);
        $this->assertEquals(lessThanOrEqual(-1, 0), true);
        $this->assertEquals(lessThanOrEqual(0, 5), true);
        $this->assertEquals(lessThanOrEqual(0, -1), false);
        $this->assertEquals(lessThanOrEqual(1.1e-8, 1.1e+8), true);
        $this->assertEquals(lessThanOrEqual(1.1e+8, 1.1e+8), true);
        $this->assertEquals(lessThanOrEqual(1.1e+8, 1.1e-8), false);
    }

    public function test_equal_numbers_helper()
    {
        $this->assertEquals(equal(null, null), true);
        $this->assertEquals(equal(null, 0), true);
        $this->assertEquals(equal(0, null), true);
        $this->assertEquals(equal(0, 0), true);
        $this->assertEquals(equal(-1, 0), false);
        $this->assertEquals(equal(0, 5), false);
        $this->assertEquals(equal(0, -1), false);
        $this->assertEquals(equal(1.1e-8, 1.1e+8), false);
        $this->assertEquals(equal(1.1e+8, 1.1e+8), true);
        $this->assertEquals(equal(1.1e+8, 1.1e-8), false);
    }

    public function test_not_equal_numbers_helper()
    {
        $this->assertEquals(notEqual(null, null), false);
        $this->assertEquals(notEqual(null, 0), false);
        $this->assertEquals(notEqual(0, null), false);
        $this->assertEquals(notEqual(0, 0), false);
        $this->assertEquals(notEqual(-1, 0), true);
        $this->assertEquals(notEqual(0, 5), true);
        $this->assertEquals(notEqual(0, -1), true);
        $this->assertEquals(notEqual(1, 2), true);
        $this->assertEquals(notEqual(2, 2), false);
    }

    public function test_trim_empty_string()
    {
        $stdClass = new stdClass;
        $this->assertTrue(trimEmptyString(12) == 12);
        $this->assertTrue(trimEmptyString(12.12) == 12.12);
        $this->assertTrue(trimEmptyString(true) == true);
        $this->assertTrue(trimEmptyString(false) == false);
        $this->assertTrue(trimEmptyString([1, 2]) == [1, 2]);
        $this->assertTrue(trimEmptyString($stdClass) == $stdClass);
        $this->assertTrue(trimEmptyString('foobar') == 'foobar');
        $this->assertTrue(trimEmptyString(' foobar') == 'foobar');
        $this->assertTrue(trimEmptyString('foobar ') == 'foobar');
        $this->assertTrue(trimEmptyString(' foobar ') == 'foobar');
    }


    public function test_empty_to_null_or_value()
    {
        $this->assertTrue(emptyToNullOrValue([]) == null);
        $this->assertTrue(emptyToNullOrValue('') == null);
        $this->assertTrue(emptyToNullOrValue('      ') == null);
        $this->assertTrue(emptyToNullOrValue(null) == null);
        $this->assertTrue(emptyToNullOrValue('null') == null);
        $this->assertTrue(emptyToNullOrValue('NULL') == null);
        $this->assertTrue(emptyToNullOrValue('NULl') == null);
        $this->assertTrue(emptyToNullOrValue(collect([])) == null);
        $this->assertTrue(emptyToNullOrValue('foobar') == 'foobar');
        $this->assertTrue(emptyToNullOrValue(1) == 1);
        $this->assertTrue(emptyToNullOrValue(0) == 0);
        $this->assertTrue(emptyToNullOrValue(0.0) == 0.0);
        $this->assertTrue(emptyToNullOrValue("0") == "0");
        $this->assertTrue(emptyToNullOrValue("0.0") == "0.0");
        $this->assertTrue(emptyToNullOrValue(1.1) == 1.1);
        $this->assertTrue(emptyToNullOrValue(true) == true);
        $this->assertTrue(emptyToNullOrValue(false) == false);
        $this->assertTrue(emptyToNullOrValue([1, 'foobar', true]) == [1, 'foobar', true]);
        $this->assertTrue(emptyToNullOrValue(collect([1, 'foobar', true])) == collect([1, 'foobar', true]));
    }
    public function test_zero_to_null_or_value()
    {
        $this->assertTrue(zeroToNullOrValue([]) == null);
        $this->assertTrue(zeroToNullOrValue('') == null);
        $this->assertTrue(zeroToNullOrValue('      ') == null);
        $this->assertTrue(zeroToNullOrValue(null) == null);
        $this->assertTrue(zeroToNullOrValue('null') == null);
        $this->assertTrue(zeroToNullOrValue('NULL') == null);
        $this->assertTrue(zeroToNullOrValue('NULl') == null);
        $this->assertTrue(zeroToNullOrValue(collect([])) == null);
        $this->assertTrue(zeroToNullOrValue(0) == null);
        $this->assertTrue(zeroToNullOrValue(0.0) == null);
        $this->assertTrue(zeroToNullOrValue("0") == null);
        $this->assertTrue(zeroToNullOrValue("0.0") == null);
        $this->assertTrue(zeroToNullOrValue('foobar') == 'foobar');
        $this->assertTrue(zeroToNullOrValue(1) == 1);
        $this->assertTrue(zeroToNullOrValue(1.1) == 1.1);
        $this->assertTrue(zeroToNullOrValue(true) == true);
        $this->assertTrue(zeroToNullOrValue(false) == false);
        $this->assertTrue(zeroToNullOrValue([1, 'foobar', true]) == [1, 'foobar', true]);
        $this->assertTrue(zeroToNullOrValue(collect([1, 'foobar', true])) == collect([1, 'foobar', true]));
    }


    public function test_remove_comma_helper()
    {
        $this->assertTrue(removeComma(123) == 123);
        $this->assertTrue(removeComma(123.11) == 123.11);
        $this->assertTrue(removeComma('123,123') == '123123');
        $this->assertTrue(removeComma('123,test') == '123test');
        $this->assertTrue(removeComma(['123,123', '123,foobar']) == ['123123', '123foobar']);
        $this->assertTrue(removeComma(null) == null);
        $this->assertTrue(removeComma(true) == true);
        $this->assertTrue(removeComma(false) == false);
    }

    public function test_replace_slash_to_dash()
    {
        $this->assertTrue(replaceSlashToDash(value: ['hi/hello', 'foobar']) == ['hi-hello', 'foobar']);
        $this->assertTrue(replaceSlashToDash(value: '2023/01/02') == '2023-01-02');
        $this->assertTrue(replaceSlashToDash(value: '') == '');
    }


    public function test_prettify_canonical_helper()
    {
        $this->assertEquals(
            prettify_canonical("test / prettify canonical ? %& $ *"),
            "test_/_prettify_canonical_?__&____"
        );
        $this->assertEquals(
            prettify_canonical("https://google.com"),
            "https://google.com"
        );
    }

    public function test_prettify_slug_helper()
    {
        $this->assertEquals(
            prettify_slug("test / prettify slug ? %& $ *"),
            "test___prettify_slug_________"
        );
    }




    public function test_date_range_function()
    {
        $range = dateRange(from: '2024-02-03', to: '2024-02-10');

        $this->assertTrue($range == [
            '2024-02-03',
            '2024-02-04',
            '2024-02-05',
            '2024-02-06',
            '2024-02-07',
            '2024-02-08',
            '2024-02-09',
            '2024-02-10',
        ]);
    }

    public function test_count_with_unit()
    {
        $this->assertEquals(countWithUnit(1), 1);
        $this->assertEquals(countWithUnit(1.1), 1.1);

        $this->assertEquals(countWithUnit(1000), '1 thousand');
        $this->assertEquals(countWithUnit(10000000.22), '10 million');
        $this->assertEquals(countWithUnit(10000000000.22), '10 billion');
        $this->assertEquals(countWithUnit(1000000000000000), '1,000,000 billion');
    }



    public function test_remove_empty_string()
    {
        $stdClass = new stdClass;
        $this->assertTrue(removeEmptyString(12) == 12);
        $this->assertTrue(removeEmptyString(12.12) == 12.12);
        $this->assertTrue(removeEmptyString(true) == true);
        $this->assertTrue(removeEmptyString(false) == false);
        $this->assertTrue(removeEmptyString([1, 2]) == [1, 2]);
        $this->assertTrue(removeEmptyString($stdClass) == $stdClass);
        $this->assertTrue(removeEmptyString('foobar') == 'foobar');
        $this->assertTrue(removeEmptyString(' foobar') == 'foobar');
        $this->assertTrue(removeEmptyString('foobar ') == 'foobar');
        $this->assertTrue(removeEmptyString(' foobar ') == 'foobar');
        $this->assertTrue(removeEmptyString(' 0912 123 1234 ') == '09121231234');
    }

    public function test_set_user_timezone()
    {
        $this->assertTrue(config('user-timezone') == null);
        setUserTimezone('UTC');
        $this->assertTrue(config('user-timezone') == 'UTC');
    }


    public function test_remove_emoji()
    {
        $stdClass = new stdClass;
        $this->assertTrue(removeEmoji(12) == 12);
        $this->assertTrue(removeEmoji(12.12) == 12.12);
        $this->assertTrue(removeEmoji(true) == true);
        $this->assertTrue(removeEmoji(false) == false);
        $this->assertTrue(removeEmoji([1, 2]) == [1, 2]);
        $this->assertTrue(removeEmoji($stdClass) == $stdClass);
        $this->assertTrue(removeEmoji('foobar') == 'foobar');
        $this->assertTrue(removeEmoji(' ') == ' ');
        $this->assertTrue(removeEmoji('😀😃😄😁😆😅😂🤣😊😇foobar') == 'foobar');
    }



    public function test_change_percentage_helper()
    {
        $this->assertTrue(changePercentage(from: 200, to: 50) == 75);
        $this->assertTrue(changePercentage(from: 200, to: 0) == -100);
        $this->assertTrue(changePercentage(from: 0, to: 200) == 100);
    }


    public function test_resolve_request()
    {
        $this->assertThrows(
            fn() => resolveRequest(
                request: TestFormRequest::class
            ),
            ValidationException::class,
            'The name field is required'
        );

        $resolved = resolveRequest(
            request: TestFormRequest::class,
            data: [
                'name'  => 'foobar',
                'email' => 'foobar@gmail.com'
            ]
        );

        $this->assertTrue($resolved instanceof TestFormRequest);
        $this->assertTrue($resolved->safe()->name == 'foobar');
        $this->assertTrue($resolved->safe()->email == 'foobar@gmail.com');
    }
}


class TestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => [
                'required',
                'max:255'
            ],
            'email' => [
                'required',
                'email'
            ]
        ];
    }
}
