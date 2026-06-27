<?php

namespace Fooino\Core\Tests\Data;

use Fooino\Core\Facades\Math;
use RoundingMode;

class Datasets
{
    public static function zeros(): array
    {
        return [
            0,
            +0,
            -0,
            '0',
            '+0',
            '-0',

            0.0,
            +0.0,
            -0.0,
            '0.0',
            '+0.0',
            '-0.0',

            0.,
            +0.,
            -0.,
            '0.',
            '+0.',
            '-0.',

            .0,
            +.0,
            -.0,
            '.0',
            '+.0',
            '-.0',

            0000.0,
            +0000.0,
            -0000.0,
            '0000.0',
            '+0000.0',
            '-0000.0',

            0.0E+10,
            +0.0E+10,
            -0.0E-10,
            '0.0E+10',
            '+0.0E+10',
            '-0.0E+10',

            .0e+10,
            +.0E-10,
            -.0E+10,
            '.0E+10',
            '+.0e+10',
            '-.0E-10',

            0.E+10,
            +0.e+10,
            -0.E-10,
            '0.e-10',
            '+0.E+10',
            '-0.E+10',

            0E+10,
            +0e+10,
            -0E-10,
            '0E+10',
            '+0E-10',
            '-0e+10',

            0.E10,
            +0.e10,
            -0.E10,
            '0.E10',
            '+0.E10',
            '-0.E10',

            0E10,
            +0e10,
            -0E10,
            '0E10',
            '+0E10',
            '-0E10',

            '   0.0',
            '  +.0E+10  ',
            "\t0",
            "\n0e0\n",
            '0e0',
            '0.E0',
            '0.00e10',
            '000.0000E+5',
            '0.000000e-3',
            '00',
            00,
            0_0,
        ];
    }

    public static function shuffleZeros(int $count = 5): array
    {
        $zeros = self::zeros();

        shuffle($zeros);

        return array_map(fn($zero) => [$zero, '0'], array_slice($zeros, 0, $count));
    }

    public static function nonZero(): array
    {
        return [
            '.',
            '+.',
            '-.',

            '',
            '+',
            '-',

            10,
            -10,
            '10',
            '-10',

            10.000010,
            -10.000010,
            '10.000010',
            '-10.000010',

            0.1E-20,
            '0.1E-20',
            1.1E-20,
            '1.1E-20',

            'A10',
            'foobar',

            '   ',
            "\t",
            "\n",

            NAN,
            INF,
            -INF,

            '0e',
            '0E',
            '.e',
            '0.0e',
            '0.0E+',

            '0..0',
            '0.0.0',
            '0e0e0',
            '0x0',
            '0b0',
            '0o0',
            '0 0',
            '0a',
            '001',
            '0.001',

            'e10',
            '+E10',
            '-e10',

            'E+10',
            '+E+10',
            '-E-10',

            '.e+10',
            '+.E+10',
            '-.E-10',
        ];
    }

    public static function mathConvertScientificNumber(): array
    {
        $set = [
            ['null', 'null'],
            ['null.null', 'null.null'],
            ['""', '""'],
            ['foobar', 'foobar'],
            ['foo.bar', 'foo.bar'],
            ['foo.bar.ino', 'foo.bar.ino'],
            ['-foo.bar.ino', '-foo.bar.ino'],
            ['abc1E+3xyz', 'abc1E+3xyz'], // contains 1E+3 which is valid Scientific Number but the method must not convert it
            ['test', 'test'],

            ['', ''],
            [' ', ' '],
            ['.', '.'],
            ['+.', '+.'],
            ['-.', '-.'],

            ['e', 'e'],
            ['+e', '+e'],
            ['-e', '-e'],

            ['E', 'E'],
            ['+E', '+E'],
            ['-E', '-E'],

            [11, '11'],
            [+11, '11'],
            [-11, '-11'],

            ['11', '11'],
            ['+11', '11'],
            ['-11', '-11'],

            [11.000000, '11'],
            [+11.000000, '11'],
            [-11.000000, '-11'],

            ['11.000000', '11'],
            ['+11.000000', '11'],
            ['-11.000000', '-11'],

            [11.011000, '11.011'],
            [+11.011000, '11.011'],
            [-11.011000, '-11.011'],

            ['11.011000', '11.011000'],
            ['+11.011000', '11.011000'],
            ['-11.011000', '-11.011000'],

            [0.011000, '0.011'],
            [.011000, '0.011'],
            [+.011000, '0.011'],
            [-.011000, '-0.011'],

            ['0.011000', '0.011000'],
            ['+.011000', '0.011000'],
            ['-.011000', '-0.011000'],
            ['-0.011000', '-0.011000'],

            [PHP_INT_MAX, "" . PHP_INT_MAX . ""],
            [PHP_INT_MAX . '.' . PHP_INT_MAX, PHP_INT_MAX . '.' . PHP_INT_MAX],
            [PHP_INT_MIN, "" . PHP_INT_MIN . ""],
            [bcadd(PHP_INT_MAX, 1000, 0), bcadd(PHP_INT_MAX, 1000, 0)],
            [bcsub(PHP_INT_MIN, 1000, 0), bcsub(PHP_INT_MIN, 1000, 0)],

            [1e8, '100000000'],
            [+1e8, '100000000'],
            [-1e8, '-100000000'],

            ['1e8', '100000000'],
            ['+1e8', '100000000'],
            ['-1e8', '-100000000'],

            [0.1e8, '10000000'],
            [.1e8, '10000000'],
            [+.1e8, '10000000'],
            [-.1e8, '-10000000'],

            ['0.1e8', '10000000'],
            ['.1e8', '10000000'],
            ['+.1e8', '10000000'],
            ['-.1e8', '-10000000'],

            [1e+8, '100000000'],
            [+1e+8, '100000000'],
            [-1e+8, '-100000000'],

            ['1e+8', '100000000'],
            ['+1e+8', '100000000'],
            ['-1e+8', '-100000000'],

            [0.1e+8, '10000000'],
            [.1e+8, '10000000'],
            [+.1e+8, '10000000'],
            [-.1e+8, '-10000000'],

            ['0.1e+8', '10000000'],
            ['.1e+8', '10000000'],
            ['+.1e+8', '10000000'],
            ['-.1e+8', '-10000000'],

            [1e-8, '0.00000001'],
            [+1e-8, '0.00000001'],
            [-1e-8, '-0.00000001'],

            ['1e-8', '0.00000001'],
            ['+1e-8', '0.00000001'],
            ['-1e-8', '-0.00000001'],

            [0.1e-8, '0.000000001'],
            [.1e-8, '0.000000001'],
            [+.1e-8, '0.000000001'],
            [-.1e-8, '-0.000000001'],

            ['0.1e-8', '0.000000001'],
            ['.1e-8', '0.000000001'],
            ['+.1e-8', '0.000000001'],
            ['-.1e-8', '-0.000000001'],

            [1.1e+8, '110000000'],
            [+1.1e+8, '110000000'],
            [-1.1e+8, '-110000000'],

            ['1.1e+8', '110000000'],
            ['+1.1e+8', '110000000'],
            ['-1.1e+8', '-110000000'],

            [0.11e+9, '110000000'],
            [.11e+9, '110000000'],
            [+.11e+9, '110000000'],
            [-.11e+9, '-110000000'],

            ['0.11e+9', '110000000'],
            ['.11e+9', '110000000'],
            ['+.11e+9', '110000000'],
            ['-.11e+9', '-110000000'],

            [1.1E-8, '0.000000011'],
            [+1.1E-8, '0.000000011'],
            [-1.1E-8, '-0.000000011'],

            ['1.1E-8', '0.000000011'],
            ['+1.1E-8', '0.000000011'],
            ['-1.1E-8', '-0.000000011'],

            [312.12E-2, '3.1212'],
            [+312.12E-2, '3.1212'],
            [-312.12E-2, '-3.1212'],

            ['312.12E-2', '3.1212'],
            ['+312.12E-2', '3.1212'],
            ['-312.12E-2', '-3.1212'],

            ['312.120E-2', '3.1212'],
            ['31213141516171819.20E-14', '312.131415161718192'],

            ['1.1e-20', '0.000000000000000000011'],
            ['-1.1e-20', '-0.000000000000000000011'],

            ['1.1e+20', '110000000000000000000'],
            ['20.1e+20', '2010000000000000000000'],

            [1.1E-999, '0'],
            [1.1E-324, '0'],
            [-1.1E-324, '0'],

            ['.e+8', '.e+8'],
            ['.e8', '.e8'],
            ['e8', 'e8'],
            ['1.1e+', '1.1e+'],
            ['1.1e-', '1.1e-'],
            ['1.1e', '1.1e'],
            ['1.1e+2', '110'],
            ['1.e+2', '100'],
            ['.1e+2', '10'],
            ['1e+2', '100'],
            ['000.000', '0'],
            ['000123.12', '123.12'],
            ['0001.1e+2', '110'],
        ];

        return array_merge(self::shuffleZeros(20), $set);
    }

    public static function mathTrimTrailingZeros(): array
    {
        $set = [
            ['test', 'test'],
            ['foo.bar', 'foo.bar'],
            ['foo.bar0', 'foo.bar0'],
            ['foo.0', 'foo.0'],

            ['-0.1E-2', '-0.001'],
            ['1.00100000E+5', '100100'],

            [11, '11'],
            [+11, '11'],
            [-11, '-11'],

            ['11', '11'],
            ['+11', '11'],
            ['-11', '-11'],

            [11.11, '11.11'],
            [+11.11, '11.11'],
            [-11.11, '-11.11'],

            ['11.11', '11.11'],
            ['+11.11', '11.11'],
            ['-11.11', '-11.11'],

            [11., '11'],
            [+11., '11'],
            [-11., '-11'],
            ['11.', '11'],
            ['+11.', '11'],
            ['-11.', '-11'],

            [.11, '0.11'],
            [+.11, '0.11'],
            [-.11, '-0.11'],
            ['.11', '0.11'],
            ['+.11', '0.11'],
            ['-.11', '-0.11'],

            [1100, '1100'],
            [+1100, '1100'],
            [-1100, '-1100'],

            ['1100', '1100'],
            ['+1100', '1100'],
            ['-1100', '-1100'],

            [1100., '1100'],
            [+1100., '1100'],
            [-1100., '-1100'],

            ['1100. ', '1100'],
            ['+1100. ', '1100'],
            ['-1100. ', '-1100'],

            [1100.001100, '1100.0011'],
            [+1100.001100, '1100.0011'],
            [-1100.001100, '-1100.0011'],

            ['1100.001100', '1100.0011'],
            ['+1100.001100', '1100.0011'],
            ['-1100.001100', '-1100.0011'],

            [.001100, '0.0011'],
            [+.001100, '0.0011'],
            [-.001100, '-0.0011'],

            ['.001100', '0.0011'],
            ['+.001100', '0.0011'],
            ['-.001100', '-0.0011'],
        ];

        return array_merge(self::shuffleZeros(), $set);
    }

    public static function mathCountDecimalPlaces(): array
    {
        $set = [
            [11, 0],
            [11.01, 2],

            [0.000000000100, 10],
            ['0.00000000100', 9],

            [1.1e-8, 9],
            [0.1e-8, 9],
            [0.e-8, 0],

            ['.1e-8', 9],
            ['-.1e-8', 9],

            ['test', 0],
            ['test.', 0],
            ['test.0', 0],
            ['test.01', 0],
            ['1.00100000E+5', 0],
        ];

        return array_merge(
            array_map(fn($arr) => [$arr[0], (int)$arr[1]], self::shuffleZeros()),
            $set
        );
    }

    public static function mathNumber(): array
    {
        $set = [
            [0.001, '0.001', null],
            ['0.00000000000123', '0.000000000001', null],
            [0.001, '0', 2],

            ['.44015042', '0.44015042', null],
            [0.44015042, '0.4401', 4],

            [11.000001000, '11.000001', null],
            [-11.000001000, '-11.000001', null],

            [1e8, '100000000', null],
            [-1e8, '-100000000', null],

            [1.1e+8, '110000000', null],
            [.1e+8, '10000000', null],

            [1.101e-5, '0.00001101', null],
            [-0.101e-5, '-0.00000101', null],

            [1.1E+20, '110000000000000000000', null],
            [1.1E-20, '0', null], // the decimal numbers is very more than precision

            [[0.00101], ['0.001'], 4],

            [[1, 11.000001000, '0.e+8'], ['1', '11.000001', '0'], null],

            [fn() => expect(Math::setPrecision(2)->number(1.001, '.44015042', '1e8', '0.e+8'))->toBe(['1', '0.44', '100000000', '0']), null, null],

            [fn() => expect(number(1, 11.000001000, '.0e8'))->toBe(['1', '11.000001', '0']), null, null]
        ];

        return array_merge(
            array_map(fn($a) => [$a[0], $a[1], null], self::shuffleZeros()),
            $set
        );
    }

    public static function mathNumberFormat(): array
    {
        $set = [
            [1.1e-20, ',', '0', null], // the decimal numbers is very more than precision

            [1.1e-8, ',', '0.000000011', null],
            [1.1e+8, ',', '110,000,000', null],

            [5000000, ',', '5,000,000', null],
            [5000000.50, ',', '5,000,000.5', null],
            [5000000.05, ',', '5,000,000.05', null],
            [5000000.0150100, ',', '5,000,000.01501', null],
            [5000000.0150100, ',', '5,000,000.015', 3],
            [5000000.0150100, ',', '5,000,000.01', 2],

            [1.1e+20, '|', '110|000|000|000|000|000|000', null],

            ['5,000,000.0150100', ' ', '5 000 000.01501', null],
            ['+5,000,000.0150100', ' ', '5 000 000.01501', null],

            ['-5-000-000.0150100', '-', '-5-000-000.01501', null],

            ['1.250,50', ',', '1.2505', null]
        ];

        return array_merge(
            array_map(fn($a) => [$a[0], ',', $a[1], null], self::shuffleZeros()),
            $set
        );
    }

    public static function mathSum(): array
    {
        $set = [
            [[5.599, 5.499], '11.098'],
            [[5.599, -5.499], '0.1'],

            [[1.1e+8, 1.1e-8], '110000000.000000011'],
            [[1.1e+20, 1.1e-8], '110000000000000000000.000000011'],

            [[0, '1234567891234567889999999'], '1234567891234567889999999'],
            [['1234567891234567889999999', 0], '1234567891234567889999999'],

            [['1234567891234567889999999', '-1234567891234567889999999'], '0'],
            [['-1234567891234567889999999', '1234567891234567889999999'], '0'],

            [['1234567891234567889999999.000000000011', '1234567891234567889999999.000000000009'], '2469135782469135779999998.00000000002'],
            [['1234567891234567889999999.00000000011', '1234567891234567889999999.00000000019'], '2469135782469135779999998.0000000003'],

            [range(1, 10), '55'],
            [[1, 2, 3, 4], '10'],

            [fn() => expect(sum(1, 2, 3, 4))->toBe('10'), null]
        ];

        return array_merge(
            array_map(fn($z) => [[$z[0], $z[0]], '0'], self::shuffleZeros()),
            $set
        );
    }

    public static function mathSubtract(): array
    {
        $set = [

            [[5, 6], '-1'],
            [[5.599, 5.499], '0.1'],

            [[1.1e+8, 1.1e-8], '109999999.999999989'],
            [[1.1e+20, 1.1e-8], '109999999999999999999.999999989'],

            [[0, '1234567891234567889999999'], '-1234567891234567889999999'],
            [['1234567891234567889999999', 0], '1234567891234567889999999'],

            [['1234567891234567889999999', '1234567891234567889999999'], '0'],
            [['-1234567891234567889999999', '-1234567891234567889999999'], '0'],

            [['-1234567891234567889999999', '1234567891234567889999999'], '-2469135782469135779999998'],
            [['1234567891234567889999999', '-1234567891234567889999999'], '2469135782469135779999998'],

            [['1234567891234567889999999.000000000011', '-1234567891234567889999999.000000000019'], '2469135782469135779999998.00000000003'],

            [range(1, 10), '-53'],

            [fn() => expect(subtract(1, 2, 3, 4))->toBe('-8'), null]

        ];

        return array_merge(
            array_map(fn($z) => [[$z[0], $z[0]], '0'], self::shuffleZeros()),
            $set
        );
    }

    public static function mathMultiply(): array
    {
        $set = [
            [[5.125, 6.11], '31.31375'],

            [[5.123456789, 6.123456789], '31.37326625775'],

            [[1.1e+8, 1.1e-8], '1.21'],

            [['1234567891234567889999999', 0], '0'],
            [[0, '1234567891234567889999999'], '0'],

            [['1234567891234567889999999', -1], '-1234567891234567889999999'],
            [['1234567891234567889999999', -1, -1], '1234567891234567889999999'],

            [['1234567891234567889999999', '-1234567891234567889999999'], '-1524157878067367851562259605883269630864220000001'],
            [['-1234567891234567889999999', '1234567891234567889999999'], '-1524157878067367851562259605883269630864220000001'],
            [['1234567891234567889999999', '1234567891234567889999999'], '1524157878067367851562259605883269630864220000001'],

            [range(0, 10), '0'],
            [range(1, 10), '3628800'],
            [fn() => expect(multiply(1, 2, 3, 4))->toBe('24'), null]
        ];

        return array_merge(
            array_map(fn($z) => [[$z[0], $z[0]], '0'], self::shuffleZeros()),
            $set
        );
    }

    public static function mathDivide(): array
    {
        $set = [
            [[1, -0.5], '-2'],

            [[50, 0.4354], '114.836931557188'],
            [[361, 1.15], '313.91304347826'],

            [[-5, 6], '-0.833333333333'],
            [[10, 3], '3.333333333333'],
            [[1, 1E12], '0.000000000001'],
            [[1, 111], '0.009009009009'],

            [[1.1e+8, 1.1e-8], '10000000000000000'],
            [['-1234567891234567889999999', '1234567891234567889999999', '-0.5'], '2'],

            [range(0, 10), '0'],
            [range(1, 10), '0.000000275573'],
            [range(1, 100), '0'],
            [range(10, 1), '0.000027557319'],

            [fn() => expect(divide(10, 2, 5, 2))->toBe('0.5'), null],
            [fn() => expect(divide(0, 2, 5, 2))->toBe('0'), null]
        ];

        return array_merge(
            array_map(fn($z) => [[$z[0], 1], '0'], self::shuffleZeros()),
            $set
        );
    }

    public static function mathRemainder(): array
    {
        $set = [
            [[13, 5], '3'],
            [[13, -5], '3'],

            [[-13, 5], '-3'],
            [[-13, -5], '-3'],

            [[5, 6], '5'],

            [[1.1e+8, 1.1e-8], '0'],

            [[5.7, 1.3], '0.5'],

            [[10, 5, 3], '0'],

            [[30, 9, 3, 1], '0'],

            [fn() => expect(remainder(10, 5, 3))->toBe('0'), null],
        ];

        return array_merge(
            array_map(fn($z) => [[$z[0], 1], '0'], self::shuffleZeros()),
            $set
        );
    }

    public static function mathPower(): array
    {
        return [
            [2, 2, '4'],
            [2, -2, '0.25'],

            [2, 3, '8'],
            [2, -3, '0.125'],

            [2, 0, '1'],
            [0, 2, '0'],
            [0, 0, '1'],

            [1, 20, '1'],

            [1.1e+2, 2, '12100'],

            ['1234567891234567889999999', 2, '1524157878067367851562259605883269630864220000001'],
            ['1234567891234567889999999', 3, '1881676377434183981909558127466713752376807174114547646517403703669999999'],

            [[2, 3, 1.1e+2], 3, ['8', '27', '1331000']]
        ];
    }

    public static function mathSqrt(): array
    {
        $set = [
            [1, '1'],
            [2, '1.414213562373'],
            [3, '1.732050807568'],
            [4, '2'],
            [9, '3'],
            [16, '4'],

            ['1524157878067367851562259605883269630864220000001', '1234567891234567889999999'],
            [1.1e+2, '10.488088481701'],

            [[0, 1, 2, 3, 4, 9, 16], ['0', '1', '1.414213562373', '1.732050807568', '2', '3', '4']]
        ];

        return array_merge(self::shuffleZeros(3), $set);
    }

    public static function mathRoundUp(): array
    {
        return [
            [0, '0'],

            [0.01, '1'],
            [-0.01, '0'],

            [1, '1'],
            [1.1, '2'],
            [-1.1, '-1'],

            [1.999099, '2'],
            [-1.999099, '-1'],

            [1.1e+8, '110000000'],
            [1.1e-8, '1'],

            [[0, 0.01, -0.01, 1, 1.1, -1.1, 1.999099, -1.999099, 1.1e+8, 1.1e-8], ['0', '1', '0', '1', '2', '-1', '2', '-1', '110000000', '1']]
        ];
    }

    public static function mathRoundDown(): array
    {
        return [
            [0, '0'],

            [0.01, '0'],
            [-0.01, '-1'],

            [1, '1'],
            [1.1, '1'],
            [-1.1, '-2'],

            [1.999099, '1'],
            [-1.999099, '-2'],

            [1.1e+8, '110000000'],
            [1.1e-8, '0'],

            [[0, 0.01, -0.01, 1, 1.1, -1.1, 1.9999, -1.9999, 1.1e+8, 1.1e-8], ['0', '0', '-1', '1', '1', '-2', '1', '-2', '110000000', '0']]
        ];
    }

    public static function mathRoundClose(): array
    {
        return [
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

            [999.995,  0, RoundingMode::HalfAwayFromZero, '1000'],    // integer-boundary carry
            [-999.995, 0, RoundingMode::HalfAwayFromZero, '-1000'],
            [999.5,    0, RoundingMode::HalfAwayFromZero, '1000'],
            [998.5,    0, RoundingMode::HalfAwayFromZero, '999'],

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

            [999.995, 2, RoundingMode::HalfAwayFromZero, '1000'],  // precision=2, carry past integer boundary
            [-999.995, 2, RoundingMode::HalfAwayFromZero, '-1000'],

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

            [999.995, 0, RoundingMode::HalfTowardsZero, '1000'],   // integer-boundary, above halfway → normal rounding up
            [-999.995, 0, RoundingMode::HalfTowardsZero, '-1000'],
            [999.999, 0, RoundingMode::HalfTowardsZero, '1000'],   // above halfway → normal rounding up

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

            [999.5,   0, RoundingMode::HalfEven, '1000'],       // integer-boundary, halfway → even (1000)
            [998.5,   0, RoundingMode::HalfEven, '998'],        // halfway → even (998)
            [-999.5,  0, RoundingMode::HalfEven, '-1000'],      // halfway → even (-1000)
            [-998.5,  0, RoundingMode::HalfEven, '-998'],

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

            [999.5,   0, RoundingMode::HalfOdd, '999'],         // integer-boundary, halfway → odd (999)
            [998.5,   0, RoundingMode::HalfOdd, '999'],         // halfway → odd (999)
            [-999.5,  0, RoundingMode::HalfOdd, '-999'],
            [-998.5,  0, RoundingMode::HalfOdd, '-999'],

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

            [999.995, 0, RoundingMode::TowardsZero, '999'],    // integer-boundary, truncate toward zero
            [-999.995, 0, RoundingMode::TowardsZero, '-999'],
            [999.999, 0, RoundingMode::TowardsZero, '999'],

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

            [999.1,   0, RoundingMode::AwayFromZero, '1000'],   // integer-boundary, any fraction → away
            [-999.1,  0, RoundingMode::AwayFromZero, '-1000'],
            [999.001, 2, RoundingMode::AwayFromZero, '999.01'],   // precision=2, any fraction at rounding pos → away

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

            [999.995, 0, RoundingMode::NegativeInfinity, '999'],    // integer-boundary, round down
            [-999.995, 0, RoundingMode::NegativeInfinity, '-1000'], // floor: -1000 < -999.995
            [999.1,   0, RoundingMode::NegativeInfinity, '999'],
            [-999.1,  0, RoundingMode::NegativeInfinity, '-1000'],

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

            [999.995, 0, RoundingMode::PositiveInfinity,  '1000'],  // integer-boundary, any fraction → up
            [-999.995, 0, RoundingMode::PositiveInfinity,  '-999'], // ceil: -999 > -999.995
            [999.1,   0, RoundingMode::PositiveInfinity,  '1000'],
            [-999.1,  0, RoundingMode::PositiveInfinity,  '-999'],
        ];
    }

    public static function mathGreaterThan(): array
    {
        return [
            ['0', '0', false],

            ['0', '-0', false],
            ['-0', '0', false],
            ['-0', '-0', false],

            ['0', '+0', false],
            ['+0', '0', false],
            ['+0', '+0', false],

            ['0', '5', false],
            ['5', '0', true],

            ['0', '-5', true],
            ['-5', '0', false],

            ['+5', '+5', false],
            ['-5', '-5', false],

            ['+5', '-5', true],
            ['-5', '+5', false],

            [1.11, 1.112, false],
            [1.112, 1.11, true],
            [1.112, 1.112, false],

            [1.1e+8, 1.1e-8, true],
            [1.1e-8, 1.1e+8, false],

            [1.1e+8, 1.1e+8, false],
            [1.1e-8, 1.1e-8, false],
        ];
    }

    public static function mathGreaterThanOrEqual(): array
    {
        return [
            ['0', '0', true],

            ['0', '-0', true],
            ['-0', '0', true],
            ['-0', '-0', true],

            ['0', '+0', true],
            ['+0', '0', true],
            ['+0', '+0', true],

            ['0', '5', false],
            ['5', '0', true],

            ['0', '-5', true],
            ['-5', '0', false],

            ['+5', '+5', true],
            ['-5', '-5', true],

            ['+5', '-5', true],
            ['-5', '+5', false],

            [1.11, 1.112, false],
            [1.112, 1.11, true],
            [1.112, 1.112, true],

            [1.1e+8, 1.1e-8, true],
            [1.1e-8, 1.1e+8, false],

            [1.1e+8, 1.1e+8, true],
            [1.1e-8, 1.1e-8, true],
        ];
    }

    public static function mathLessThan(): array
    {
        return [
            ['0', '0', false],

            ['0', '-0', false],
            ['-0', '0', false],
            ['-0', '-0', false],

            ['0', '+0', false],
            ['+0', '0', false],
            ['+0', '+0', false],

            ['0', '5', true],
            ['5', '0', false],

            ['0', '-5', false],
            ['-5', '0', true],

            ['+5', '+5', false],
            ['-5', '-5', false],

            ['+5', '-5', false],
            ['-5', '+5', true],

            [1.11, 1.112, true],
            [1.112, 1.11, false],
            [1.112, 1.112, false],

            [1.1e+8, 1.1e-8, false],
            [1.1e-8, 1.1e+8, true],

            [1.1e+8, 1.1e+8, false],
            [1.1e-8, 1.1e-8, false],
        ];
    }

    public static function mathLessThanOrEqual(): array
    {
        return [
            ['0', '0', true],

            ['0', '-0', true],
            ['-0', '0', true],
            ['-0', '-0', true],

            ['0', '+0', true],
            ['+0', '0', true],
            ['+0', '+0', true],

            ['0', '5', true],
            ['5', '0', false],

            ['0', '-5', false],
            ['-5', '0', true],

            ['+5', '+5', true],
            ['-5', '-5', true],

            ['+5', '-5', false],
            ['-5', '+5', true],

            [1.11, 1.112, true],
            [1.112, 1.11, false],
            [1.112, 1.112, true],

            [1.1e+8, 1.1e-8, false],
            [1.1e-8, 1.1e+8, true],

            [1.1e+8, 1.1e+8, true],
            [1.1e-8, 1.1e-8, true],
        ];
    }

    public static function mathEqual(): array
    {
        return [
            ['0', '0', true],

            ['0', '-0', true],
            ['-0', '0', true],
            ['-0', '-0', true],

            ['0', '+0', true],
            ['+0', '0', true],
            ['+0', '+0', true],

            ['0', '5', false],
            ['5', '0', false],

            ['0', '-5', false],
            ['-5', '0', false],

            ['+5', '+5', true],
            ['-5', '-5', true],

            ['+5', '-5', false],
            ['-5', '+5', false],

            [1.11, 1.112, false],
            [1.112, 1.11, false],
            [1.112, 1.112, true],

            [1.1e+8, 1.1e-8, false],
            [1.1e-8, 1.1e+8, false],

            [1.1e+8, 1.1e+8, true],
            [1.1e-8, 1.1e-8, true],
        ];
    }

    public static function mathNotEqual(): array
    {
        return [
            ['0', '0', false],

            ['0', '-0', false],
            ['-0', '0', false],
            ['-0', '-0', false],

            ['0', '+0', false],
            ['+0', '0', false],
            ['+0', '+0', false],

            ['0', '5', true],
            ['5', '0', true],

            ['0', '-5', true],
            ['-5', '0', true],

            ['+5', '+5', false],
            ['-5', '-5', false],

            ['+5', '-5', true],
            ['-5', '+5', true],

            [1.11, 1.112, true],
            [1.112, 1.11, true],
            [1.112, 1.112, false],

            [1.1e+8, 1.1e-8, true],
            [1.1e-8, 1.1e+8, true],

            [1.1e+8, 1.1e+8, false],
            [1.1e-8, 1.1e-8, false],
        ];
    }
}
