<?php

namespace Fooino\Core\Concretes\Math;

use Fooino\Core\Exceptions\MathCalculationException;
use Fooino\Core\Interfaces\Mathable;

class FooinoMathHandler implements Mathable
{
    private const int BC_SCALE = 12;

    private array $instances = [];

    public function __construct(private int $precision = 12)
    {
        /**
         *  Difference Between BC_SCALE and $precision
         * 
         *  All bc functions must use BC_SCALE for calculations
         *  The $precision is just for returning number, not using in calculations
         * 
         *  Example: Math::setPrecision(precision: 0)->sum(5.599, 5.499));
         * 
         *  Base on BC_SCALE the result is 11.098. if we assumed BC_SCALE = $precision = 0 the result was 10 which is wrong
         *  To output number we use $precision = 0 and the result is 11
         * 
         */

        bcscale(scale: self::BC_SCALE);

        if (
            $this->getPrecision() > bcscale() ||
            $this->getPrecision() < 0
        ) {
            app(MathCalculationException::class)
                ->setMessage('msg.mathCalculationExceptionInvalidPrecision')
                ->setCode(10101)
                ->critical()
                ->with([
                    'precision' => $this->getPrecision(),
                    'bc_scale'  => bcscale()
                ])
                ->throw();
        }
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function setPrecision(int $precision): Mathable
    {
        return $this->instances[$precision] ??= new static(precision: $precision); // Facade in laravel use singleton design pattern and we need new fresh instance by $precision
    }

    public function convertScientificNumber(string|int|float $number): string
    {
        $number = trim((string) $number);

        $regex = [
            '/',
            '^',                    // start with
            '([+-]?)',              // the sign can be '+' or '-' or '' ---> first group: sign
            '(\d*\.?\d*)',          // zero or more digits before dot  optional decimal dot  zero or more digits after decimal dot ---> second group: mantissa. it can be 123 or .5 or 5. or 1.23
            '[Ee]',                 // the letter E or e: the exponent separator
            '([+-]?\d+)',           // optional '+' or '-' sign   one or more digits ---> third group: exponent. it can be 3 or +3 or -3
            '$',                    // end with
            '/'
        ];

        if (!preg_match(implode('', $regex), $number, $matches)) {

            return $this->standardizeNumber(number: $number);
        }

        $sign      = $matches[1];
        $mantissa  = $matches[2];
        $exponent  = (int) $matches[3]; // safe – exponent is always a small integer

        // Split mantissa into integer and decimal parts
        list($sign, $integerPart, $decimalPart) = $this->numberParts(number: ($sign . $mantissa));

        // All significant digits, without decimal point
        $digits = ltrim($integerPart . $decimalPart, '0');

        // If mantissa is zero, the whole number is zero like 0.1E+8
        if ($digits === '') {
            return '0';
        }

        $shift = $exponent - strlen($decimalPart); // right shift (+), left shift (-)

        if ($shift >= 0) {

            // No decimal point needed (or decimal shifted right beyond end)
            $result = $digits . str_repeat('0', $shift);

            // 
        } else {

            $absShift = abs($shift);
            $len      = strlen($digits);

            if ($absShift >= $len) {

                // Decimal point goes after '0.', padding with leading zeros
                $result = '0.' . str_repeat('0', $absShift - $len) . rtrim($digits, '0');

                // 
            } else {

                // Insert decimal point inside the digit string
                $splitPos = $len - $absShift;
                $result   = substr($digits, 0, $splitPos) . '.' . rtrim(substr($digits, $splitPos), '0');
            }
        }

        return $sign . $result;
    }

    public function trimTrailingZeros(string|int|float $number, string $decimalSeparator = '.'): string
    {
        $number = $this->convertScientificNumber(number: $number);

        return strpos($number, $decimalSeparator) !== false ? rtrim(rtrim($number, '0'), $decimalSeparator) : $number;
    }

    public function decimalPlaceNumber(string|int|float $number, string $decimalSeparator = '.'): int
    {
        return (int) (strlen(substr(strrchr($this->trimTrailingZeros(number: $number), $decimalSeparator), 1)));
    }

    public function number(string|int|float $number): string
    {
        return $this->trimTrailingZeros(number: $this->standardizeNumber(number: $this->convertScientificNumber(number: $number), precision: $this->getPrecision()));
    }

    public function numberFormat(string|int|float $number, string $decimalSeparator = '.', string $thousandsSeparator = ','): string
    {
        $number = trim((string) $number);
        $sign = '';

        if (in_array($number[0] ?? '', ['-', '+'])) {

            $sign = $number[0];

            $number = substr($number, 1);
        }

        $sanitized = str_replace([$thousandsSeparator, ','], '', str_replace($decimalSeparator, '.', (string) $number));

        $number = $this->convertScientificNumber(number: $sanitized);

        list($sign, $integer, $decimal) = $this->numberParts(number: $sign . $number);

        $integerWithSeparators = (string) preg_replace(
            '/\B(?=(\d{3})+(?!\d))/',
            $thousandsSeparator,
            $integer
        );

        $number = $sign . $integerWithSeparators . $decimalSeparator . $decimal;

        return $this->number(number: $this->trimTrailingZeros(number: $number, decimalSeparator: $decimalSeparator));
    }

    public function sum(mixed ...$args): string
    {
        return $this->calc('bcadd', ...$args);
    }

    public function subtract(mixed ...$args): string
    {
        return $this->calc('bcsub', ...$args);
    }

    public function multiply(mixed ...$args): string
    {
        return $this->calc('bcmul', ...$args);
    }

    public function divide(mixed ...$args): string
    {
        return $this->calc('bcdiv', ...$args);
    }

    public function modulus(mixed ...$args): string
    {
        return $this->calc('bcmod', ...$args);
    }

    public function power(string|int|float $number, int $exponent = 2): string
    {
        return $this->number(number: bcpow($this->convertScientificNumber(number: $number), $exponent));
    }

    public function sqrt(string|int|float $number): string
    {
        return $this->number(number: bcsqrt($this->convertScientificNumber(number: $number)));
    }

    public function roundUp(string|int|float $number): string
    {
        return $this->number(number: bcceil($this->convertScientificNumber(number: $number)));
    }

    public function roundDown(string|int|float $number): string
    {
        return $this->number(number: bcfloor($this->convertScientificNumber(number: $number)));
    }

    public function roundClose(string|int|float $number, int $precision = 0): string
    {
        return $this->number(number: bcround(num: $this->convertScientificNumber(number: $number), precision: $precision));
    }

    public function greaterThan(string|int|float $a, string|int|float $b): bool
    {
        return $this->bccomp($a, $b) === 1;
    }

    public function greaterThanOrEqual(string|int|float $a, string|int|float $b): bool
    {
        return $this->bccomp($a, $b) !== -1;
    }

    public function lessThan(string|int|float $a, string|int|float $b): bool
    {
        return $this->bccomp($a, $b) === -1;
    }

    public function lessThanOrEqual(string|int|float $a, string|int|float $b): bool
    {
        return $this->bccomp($a, $b) !== 1;
    }

    public function equal(string|int|float $a, string|int|float $b): bool
    {
        return $this->bccomp($a, $b) === 0;
    }

    public function notEqual(string|int|float $a, string|int|float $b): bool
    {
        return !$this->equal($a, $b);
    }

    /**
     * return sign, integer, decimal part of number
     */
    private function numberParts(string|int|float $number): array
    {
        $number = trim((string) $number);
        $sign = '';

        if (in_array($number[0] ?? '', ['-', '+'])) {

            $sign = $number[0];

            $number = substr($number, 1);
        }

        $dotPos = strpos($number, '.');

        list($integer, $decimal) = match ($dotPos) {

            false           => [
                (string) nullIfBlankOrZero(value: $number, fallback: '0'),
                '0'
            ],

            default         => [
                (string) nullIfBlankOrZero(value: substr($number, 0, $dotPos), fallback: '0'),
                (string) nullIfBlankOrZero(value: substr($number, $dotPos + 1), fallback: '0')
            ]
        };

        $num = nullIfBlankOrZero($integer . '.' . $decimal);

        $sign = ((is_numeric($num) && $sign === '+') || is_null($num)) ? '' : $sign;

        return [
            $sign,
            $integer,
            $decimal
        ];
    }

    /**
     * Make number base on parts
     */
    private function standardizeNumber(string|int|float $number, int|null $precision = null): string
    {
        list($sign, $integer, $decimal) = $this->numberParts(number: $number);

        return trim($sign . $integer . (nullIfBlankOrZero($decimal) ? ('.' . (is_null($precision) ? $decimal : substr($decimal, 0, $precision))) : ''));
    }

    private function bccomp(string|int|float $a, string|int|float $b): int
    {
        return bccomp($this->convertScientificNumber(number: $a), $this->convertScientificNumber(number: $b));
    }

    private function calc(string $func, mixed ...$args): string
    {
        $numbers = $this->getNumbersFromArgs($func, ...$args);

        list($result, $start) = match ($func) {

            'bcadd'             => [0, 0],

            'bcsub'             => [$numbers[0], 1],

            'bcmul'             => [1, 0],

            'bcdiv'             => [$numbers[0], 1],

            'bcmod'             => [$numbers[0], 1],

            default             => app(MathCalculationException::class)->setMessage('msg.mathCalculationExceptionInvalidFunction')->setCode(10105)->with(['func' => $func, 'args' => $args])->throw(),
        };

        for ($i = $start; $i < count($numbers); $i++) {

            $number = $numbers[$i];

            $result = call_user_func($func, $result, $number);
        }

        return $this->number(number: $result);
    }

    private function getNumbersFromArgs(string $func, mixed ...$args): array
    {
        $numbers = count($args) === 1 && is_array($args[0]) ? $args[0] : $args;

        if (
            count($numbers) === 0 ||
            (count($numbers) < 2 && in_array($func, ['bcadd', 'bcsub', 'bcmul', 'bcdiv', 'bcmod']))
        ) {

            app(MathCalculationException::class)
                ->setMessage('msg.mathCalculationExceptionInvalidArgumentsCount')
                ->setCode(10102)
                ->with([
                    'func' => $func,
                    'args' => $args
                ])
                ->throw();
        }

        foreach ($numbers as $key => $number) {

            if (!is_numeric($number)) {

                app(MathCalculationException::class)
                    ->setMessage('msg.mathCalculationExceptionInvalidArgumentType')
                    ->setCode(10103)
                    ->with([
                        'func' => $func,
                        'args' => $args
                    ])
                    ->throw();
            }

            if (
                in_array($func, ['bcdiv', 'bcmod']) &&
                ((float) $number) === 0.0 &&
                $key !== 0
            ) {
                app(MathCalculationException::class)
                    ->setMessage('msg.mathCalculationExceptionDivisionByZero')
                    ->setCode(10104)
                    ->critical()
                    ->with([
                        'func' => $func,
                        'args' => $args
                    ])
                    ->throw();
            }
        }

        return array_map($this->convertScientificNumber(...), $numbers);
    }
}
