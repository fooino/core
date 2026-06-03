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

        bcscale(self::BC_SCALE);

        if (
            $this->getPrecision() > self::BC_SCALE ||
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

        // Matches optional minus, mantissa (integer or decimal), and exponent
        if (!preg_match('/^(-?)(\d*\.?\d+)[Ee]([+-]?\d+)$/', $number, $matches)) {
            return $number; // Not scientific notation; return as-is
        }

        $sign      = $matches[1];          // '-' or ''
        $mantissa  = $matches[2];          // e.g. "1.23", ".5", "5."
        $exponent  = (int) $matches[3];    // e.g. -2, +5, 3

        // Split mantissa into integer and decimal parts
        $dotPos = strpos($mantissa, '.');
        if ($dotPos === false) {
            $intPart  = $mantissa;
            $decPart  = '';
        } else {
            $intPart  = substr($mantissa, 0, $dotPos);
            $decPart  = substr($mantissa, $dotPos + 1);
        }

        // All significant digits, without decimal point
        $digits = ltrim($intPart . $decPart, '0');

        // If mantissa is zero, the whole number is zero
        if ($digits === '') {
            return '0';
        }

        $decPlaces = strlen($decPart);
        $shift     = $exponent - $decPlaces; // right shift (+), left shift (-)

        if ($shift >= 0) {

            // No decimal point needed (or decimal shifted right beyond end)
            $result = $digits . str_repeat('0', $shift);

            // 
        } else {

            $absShift = abs($shift);
            $len      = strlen($digits);

            if ($absShift >= $len) {

                // Decimal point goes after '0.', padding with leading zeros
                $result = '0.' . str_repeat('0', $absShift - $len) . $digits;

                // 
            } else {

                // Insert decimal point inside the digit string
                $splitPos = $len - $absShift;
                $result   = substr($digits, 0, $splitPos) . '.' . substr($digits, $splitPos);
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
        return (int) (strlen(substr(strrchr($this->number(number: $number), $decimalSeparator), 1)));
    }

    public function number(string|int|float $number): string
    {
        $number = $this->convertScientificNumber(number: $number);

        $parts = explode('.', $number);

        $real = nullIfBlank(value: $parts[0] ?? 0, fallback: 0);

        $decimal = substr(nullIfBlank(value: $parts[1] ?? 0, fallback: 0), 0, $this->getPrecision());

        $number = $real . "." . $decimal;

        return $this->trimTrailingZeros(number: $number);
    }

    public function numberFormat(string|int|float $number, string $decimalSeparator = '.', string $thousandsSeparator = ','): string
    {
        // 1. Convert the input to a standard numeric string
        //    (first replace the decimal separator, then remove the thousands separator)
        $cleaned = str_replace(
            $thousandsSeparator,
            '',
            str_replace($decimalSeparator, '.', (string) $number)
        );

        // 2. Validate that what remains is truly numeric
        if (!is_numeric($cleaned)) {
            app(MathCalculationException::class)
                ->setMessage('msg.mathCalculationExceptionInvalidArgumentType')
                ->setCode(10102)
                ->with([
                    'func' => 'numberFormat',
                    'args' => [
                        'number'             => $number,
                        'decimalSeparator'   => $decimalSeparator,
                        'thousandsSeparator' => $thousandsSeparator,
                    ],
                ])
                ->throw();
        }

        // 3. Apply the handler's precision and trailing zero trimming
        //    (internally uses convertScientificNumber, so no float loss)
        $sanitized = $this->number($cleaned);   // e.g. "-1234567.890"  or "0"  or "5.2"

        // 4. Extract the sign (if any)
        $sign = '';
        if ($sanitized[0] === '-') {
            $sign = '-';
            $sanitized = substr($sanitized, 1);
        }

        // 5. Split into integer and decimal parts
        $dotPos = strpos($sanitized, '.');
        if ($dotPos === false) {
            $integer = $sanitized;
            $decimal = '';
        } else {
            $integer = substr($sanitized, 0, $dotPos);
            $decimal = substr($sanitized, $dotPos + 1);
        }

        // 6. Add thousands separators to the integer part (absolute value)
        $integerWithSeparators = (string) preg_replace(
            '/\B(?=(\d{3})+(?!\d))/',
            $thousandsSeparator,
            $integer
        );

        // 7. Assemble the final formatted number
        $result = $sign . $integerWithSeparators;
        if ($decimal !== '') {
            $result .= $decimalSeparator . $decimal;
        }

        return $result;
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

    private function bccomp(string|int|float $a, string|int|float $b): int
    {
        return bccomp($this->convertScientificNumber(number: $a), $this->convertScientificNumber(number: $b));
    }

    private function calc(string $func, mixed ...$args): string
    {
        $numbers = count($args) === 1 && is_array($args[0]) ? $args[0] : $args;

        if (count($numbers) < 2) {

            app(MathCalculationException::class)
                ->setMessage('msg.mathCalculationExceptionInvalidArgumentsCount')
                ->setCode(10101)
                ->with([
                    'func' => $func,
                    'args' => $args
                ])
                ->throw();
        }

        foreach ($numbers as $number) {

            if (!is_numeric($number)) {

                app(MathCalculationException::class)
                    ->setMessage('msg.mathCalculationExceptionInvalidArgumentType')
                    ->setCode(10102)
                    ->with([
                        'func' => $func,
                        'args' => $args
                    ])
                    ->throw();
            }

            if (
                in_array($func, ['bcdiv', 'bcmod']) &&
                ((float)$number) == 0
            ) {
                app(MathCalculationException::class)
                    ->setMessage('msg.mathCalculationExceptionDivisionByZero')
                    ->setCode(10103)
                    ->critical()
                    ->with([
                        'func' => $func,
                        'args' => $args
                    ])
                    ->throw();
            }
        }

        $numbers = array_map($this->convertScientificNumber(...), $numbers);

        list($result, $start) = match ($func) {
            'bcadd'             => [0, 0],
            'bcsub'             => [$numbers[0], 1],
            'bcmul'             => [1, 0],
            'bcdiv'             => [$numbers[0], 1],
            'bcmod'             => [$numbers[0], 1],
        };

        for ($i = $start; $i < count($numbers); $i++) {

            $number = $numbers[$i];

            // since we are in the loop and 0 will be + - * / to number for first time it can effect on scale and make not accurate result. so we increase the scale
            // using bc function directly does not make non accurate result

            $result = call_user_func($func, $result, $number, self::BC_SCALE + 3);
        }


        return $this->number(number: $result);
    }
}
