<?php

namespace Fooino\Core\Concretes\Math;

use Fooino\Core\Interfaces\Mathable;
use Fooino\Core\Exceptions\MathCalculationException;
use RoundingMode;

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
         *  The $precision is just for returning truncated number(not rounded) and It will not used in calculations
         * 
         *  Truncate number is good for each country policy for example 1000.01 is not valid in Iran since 0.01 is worthless but in other countries like America it means cent.
         *  So we use $precision = 0 For Iran and $precision = 2 for America
         * 
         *  Example: Math::setPrecision(precision: 0)->number(Math::sum(5.599, 5.499)));
         * 
         *  Base on BC_SCALE the result is 11.098. if we assumed BC_SCALE = $precision = 0 the result was 10 which is wrong
         *  To output number we use $precision = 0 and the result is 11
         * 
         *  All calculations use BC_SCALE which is a high number to not loss precision
         */

        bcscale(scale: self::BC_SCALE);

        if (
            $this->getPrecision() > bcscale() ||
            $this->getPrecision() < 0
        ) {
            $this->throwInvalidPrecisionException();
        }
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function setPrecision(int $precision): Mathable
    {
        /**  
         * Facade in laravel use singleton design pattern and we need new fresh instance by $precision
         * Do not chain setPrecision with setPrecision like Math::setPrecision(2)->setPrecision(3)
         * It can increase memory usage
         */
        return $this->instances[$precision] ??= new static(precision: $precision);
    }

    public function convertScientificNumber(string|int|float $number): string
    {
        if ($number === INF || $number === -INF) {
            // Very Big or small number is not allowed. 1.1E999 in float format will cast to INF. the INF is numeric so check it before is_numeric()
            $this->throwInvalidValueErrorException(method: 'convertScientificNumber', operand: $number);
        }

        if (
            !is_numeric($number) &&
            !isZero($number) // the scientific numbers like .e+8 is not numeric but it is zero
        ) {
            return $number;
        }

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

        if (preg_match(implode('', $regex), $number, $matches) === 0) {

            return $this->assembleNumber(number: $number);
        }

        $sign      = $matches[1];
        $mantissa  = $matches[2];
        $exponent  = (int) $matches[3];

        if (abs($exponent) > 99) {
            // Very Big or Small number is not allowed. '1.1E999' in string format has high exponent value
            $this->throwInvalidValueErrorException(method: 'convertScientificNumber', operand: $number);
        }

        list($sign, $integerPart, $decimalPart) = $this->numberParts(number: ($sign . $mantissa));

        // All significant digits, without decimal point
        $digits = ltrim($integerPart . $decimalPart, '0');

        // If mantissa is zero, the whole number is zero like 0.1E+8
        if ($digits === '') {
            return '0';
        }

        $shift = $exponent - strlen($decimalPart); // right shift (+), left shift (-)

        if ($shift >= 0) {

            // example: 1.1E+5
            // No decimal point needed (or decimal shifted right beyond end)
            $result = $digits . str_repeat('0', $shift);

            // 
        } else {

            $absShift = abs($shift);
            $len      = strlen($digits);

            if ($absShift >= $len) {

                // example: 1.1E-5
                // Decimal point goes after '0.', padding with leading zeros
                $result = '0.' . str_repeat('0', $absShift - $len) . rtrim($digits, '0');

                // 
            } else {

                // example: 1002.1E-2
                // Insert decimal point inside the digit string
                $splitPos = $len - $absShift;
                $result   = substr($digits, 0, $splitPos) . '.' . rtrim(substr($digits, $splitPos), '0');
            }
        }

        return $sign . $result;
    }

    public function trimTrailingZeros(string|int|float $number): string
    {
        return $this->_trimTrailingZeros(number: $number, expandScientific: true);
    }

    private function _trimTrailingZeros(string|int|float $number, bool $expandScientific = false): string
    {
        if ($expandScientific) {

            $number = $this->convertScientificNumber(number: $number);
        }

        return (is_numeric($number) && strpos($number, '.') !== false) ? rtrim(rtrim($number, '0'), '.') : $number;
    }

    public function countDecimalPlaces(string|int|float $number): int
    {
        $number = $this->trimTrailingZeros(number: $number);

        $dotPos = strrpos($number, '.');

        return (!is_numeric($number) || $dotPos === false) ? 0 : strlen(substr($number, $dotPos + 1));
    }

    public function number(mixed ...$number): string|array
    {
        return $this->_number(true, ...$number);
    }

    private function _number(bool $expandScientific = false, mixed ...$number): string|array
    {
        $numbers = count($number) === 1 && is_array($number[0]) ? $number[0] : $number;

        if (count($numbers) === 0) {
            $this->throwInvalidArgumentsCountException(method: 'number', operand: $numbers);
        }

        foreach ($numbers as $key => $value) {

            if ($expandScientific) {
                $value = $this->convertScientificNumber(number: $value);
            }

            if (!is_numeric($value)) {
                $this->throwInvalidArgumentTypeException(method: 'number', operand: $numbers);
            }

            $numbers[$key] = $this->_trimTrailingZeros(number: $this->assembleNumber(number: $value, precision: $this->getPrecision()));
        }

        return count($numbers) === 1 ? $numbers[0] : $numbers;
    }

    public function numberFormat(string|int|float $number, string $thousandsSeparator = ','): string
    {
        $sanitized = trim((string) $number);
        $sign = '';

        if (in_array($sanitized[0] ?? '', ['-', '+'])) {

            $sign = $sanitized[0]; // the number can be -2-000-000.001 which is negtive number with - thousandsSeparator

            $sanitized = substr($sanitized, 1);
        }

        $sanitized = str_replace([$thousandsSeparator, ','], '',  (string) $sanitized);

        $sanitized = $this->convertScientificNumber(number: $sanitized);

        if (!is_numeric($sanitized)) {
            $this->throwInvalidArgumentTypeException(method: 'numberFormat', operand: $number);
        }

        $sanitized = $this->_number(false, $sanitized); // apply precision

        list($sign, $integer, $decimal) = $this->numberParts(number: ($sign . $sanitized));

        $integerWithSeparators = (string) preg_replace(
            '/\B(?=(\d{3})+(?!\d))/',
            $thousandsSeparator,
            $integer
        );

        return trim($sign . $integerWithSeparators . ((isZero($decimal) || blank($decimal)) ? '' : '.' . $decimal));
    }

    public function sum(mixed ...$operand): string
    {
        return $this->calc(method: 'bcadd', operand: $operand);
    }

    public function subtract(mixed ...$operand): string
    {
        return $this->calc(method: 'bcsub', operand: $operand);
    }

    public function multiply(mixed ...$operand): string
    {
        return $this->calc(method: 'bcmul', operand: $operand);
    }

    public function divide(mixed ...$operand): string
    {
        return $this->calc(method: 'bcdiv', operand: $operand);
    }

    public function remainder(mixed ...$operand): string
    {
        return $this->calc(method: 'bcmod', operand: $operand);
    }

    public function power(string|int|float|array $number, int $exponent = 2): string|array
    {
        return $this->calc(method: 'bcpow', operand: (array) $number, args: ['exponent' => $exponent]);
    }

    public function sqrt(string|int|float|array $number): string|array
    {
        return $this->calc(method: 'bcsqrt', operand: (array) $number);
    }

    public function roundUp(string|int|float|array $number): string|array
    {
        return $this->calc(method: 'bcceil', operand: (array) $number);
    }

    public function roundDown(string|int|float|array $number): string|array
    {
        return $this->calc(method: 'bcfloor', operand: (array) $number);
    }

    public function roundClose(string|int|float|array $number, int $precision = 0, RoundingMode $mode = RoundingMode::HalfAwayFromZero): string|array
    {
        return $this->calc(method: 'bcround', operand: (array) $number, args: ['precision' => $precision, 'mode' => $mode]);
    }

    public function greaterThan(string|int|float $a, string|int|float $b): bool
    {
        return $this->compare($a, $b) === 1;
    }

    public function greaterThanOrEqual(string|int|float $a, string|int|float $b): bool
    {
        return $this->compare($a, $b) !== -1;
    }

    public function lessThan(string|int|float $a, string|int|float $b): bool
    {
        return $this->compare($a, $b) === -1;
    }

    public function lessThanOrEqual(string|int|float $a, string|int|float $b): bool
    {
        return $this->compare($a, $b) !== 1;
    }

    public function equal(string|int|float $a, string|int|float $b): bool
    {
        return $this->compare($a, $b) === 0;
    }

    public function notEqual(string|int|float $a, string|int|float $b): bool
    {
        return !$this->equal($a, $b);
    }

    private function compare(string|int|float $a, string|int|float $b): int
    {
        return bccomp($this->convertScientificNumber(number: $a), $this->convertScientificNumber(number: $b));
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
                $number,
                '0'
            ],

            default         => [
                substr($number, 0, $dotPos),
                substr($number, $dotPos + 1)
            ]
        };

        $integer = (string) ((isZero($integer) || blank($integer)) ? '0' : $integer);
        $decimal = (string) ((isZero($decimal) || blank($decimal)) ? '0' : $decimal);

        $integer = ltrim($integer, '0') ?: '0'; // to handle '000123.1'

        $assembled = $integer . '.' . $decimal;

        $sign = ((is_numeric($assembled) && $sign === '+') || isZero($assembled)) ? '' : $sign; // when the number is zero or positive: make it empty string

        return [
            $sign,
            $integer,
            $decimal
        ];
    }

    /**
     * Make number base on parts and precision
     */
    private function assembleNumber(string|int|float $number, int|null $precision = null): string
    {
        list($sign, $integer, $decimal) = $this->numberParts(number: $number);

        $decimal = isZero($decimal) ? '' : ('.' . (is_null($precision) ? $decimal : substr($decimal, 0, $precision)));

        return trim($sign . $integer . $decimal);
    }

    private function calc(string $method, array $operand, array $args = []): string|array
    {
        $numbers = $this->validateAndNormalizeNumbers(method: $method, operand: $operand, args: $args);

        $twoOperand = ['bcadd', 'bcsub', 'bcmul', 'bcdiv', 'bcmod'];
        $oneOperand = ['bcpow', 'bcsqrt', 'bcceil', 'bcfloor', 'bcround'];

        if (in_array($method, $twoOperand)) {

            $defaultTwoOperandTemplate = ['num1' => 'result', 'num2' => 'number', 'scale' => null];

            list($result, $start, $template) = match ($method) {

                'bcadd'             => ['0', 0, $defaultTwoOperandTemplate],

                'bcsub'             => [$numbers[0], 1, $defaultTwoOperandTemplate],

                'bcmul'             => ['1', 0, $defaultTwoOperandTemplate],

                'bcdiv'             => [$numbers[0], 1, $defaultTwoOperandTemplate],

                'bcmod'             => [$numbers[0], 1, $defaultTwoOperandTemplate],

                default             => $this->throwUnsupportedFunctionException(method: $method, operand: $operand, args: $args),
            };

            for ($i = $start; $i < count($numbers); $i++) {

                $number = $numbers[$i]; // DO NOT remove this since it will called in the map dynamically

                $mapped = [];
                foreach ($template as $argKey => $argValue) {

                    $mapped[$argKey] = !is_null($argValue) ? ${$argValue} : null;

                    // 
                }

                $result = call_user_func($method, ...$mapped);
            }

            return $this->_trimTrailingZeros(number: $result);
        }

        if (in_array($method, $oneOperand)) {

            $template = match ($method) {

                'bcpow'             => ['num' => 'value', 'exponent' => 'args.exponent', 'scale' => null],

                'bcsqrt'            => ['num' => 'value', 'scale' => null],

                'bcceil'            => ['num' => 'value'],

                'bcfloor'           => ['num' => 'value'],

                'bcround'           => ['num' => 'value', 'precision' => 'args.precision', 'mode' => 'args.mode'],

                default             => $this->throwUnsupportedFunctionException(method: $method, operand: $operand, args: $args),
            };

            $operandAndArgs = ['operand' => $operand, 'args' => $args];

            foreach ($numbers as $key => $value) { // DO NOT remove or change $value, it wall call dynamically

                $mapped = [];
                foreach ($template as $argKey => $argValue) {

                    $mapped[$argKey] = !is_null($argValue) ? ((strpos($argValue, '.') !== false) ? data_get($operandAndArgs, $argValue) : ${$argValue}) : null;

                    // 
                }

                $numbers[$key] = $this->_trimTrailingZeros(number: call_user_func($method, ...$mapped));
            }

            return count($numbers) === 1 ? $numbers[0] : $numbers;
        }

        $this->throwUnsupportedFunctionException(method: $method, operand: $operand, args: $args);
    }

    private function validateAndNormalizeNumbers(string $method, array $operand, array $args = []): array
    {
        $numbers = count($operand) === 1 && is_array($operand[0]) ? $operand[0] : $operand;

        if (
            count($numbers) === 0 ||
            (count($numbers) < 2 && in_array($method, ['bcadd', 'bcsub', 'bcmul', 'bcdiv', 'bcmod']))
        ) {
            $this->throwInvalidArgumentsCountException(method: $method, operand: $operand, args: $args);
        }

        foreach ($numbers as $key => $number) {

            $numbers[$key] = $number = $this->convertScientificNumber(number: $number);

            if (!is_numeric($number)) {
                $this->throwInvalidArgumentTypeException(method: $method, operand: $operand, args: $args);
            }

            if (
                in_array($method, ['bcdiv', 'bcmod']) &&
                isZero($number) &&
                $key !== 0
            ) {
                $this->throwDivisionByZeroException(method: $method, operand: $operand);
            }

            if (
                $method === 'bcpow' &&
                isZero($number) &&
                (count($args) !== 1 || !is_int($args['exponent'] ?? null) || $args['exponent'] < 0)
            ) {
                $this->throwDivisionByZeroException(method: $method, operand: $operand, args: $args);
            }

            if (
                $method === 'bcsqrt' &&
                $number < 0
            ) {
                $this->throwInvalidValueErrorException(method: $method, operand: $operand);
            }
        }

        return $numbers;
    }

    private function throwInvalidPrecisionException(): never
    {
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

    private function throwInvalidArgumentsCountException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->setMessage('msg.mathCalculationExceptionInvalidArgumentsCount')
            ->setCode(10102)
            ->with([
                'method'    => $method,
                'operand'   => $operand,
                'args'      => $args
            ])
            ->throw();
    }

    private function throwInvalidArgumentTypeException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->setMessage('msg.mathCalculationExceptionInvalidArgumentType')
            ->setCode(10103)
            ->with([
                'method'    => $method,
                'operand'   => $operand,
                'args'      => $args
            ])
            ->throw();
    }

    private function throwDivisionByZeroException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->setMessage('msg.mathCalculationExceptionDivisionByZero')
            ->setCode(10104)
            ->critical()
            ->with([
                'method'      => $method,
                'operand'     => $operand,
                'args'        => $args,
            ])
            ->throw();
    }

    private function throwInvalidValueErrorException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->setMessage('msg.mathCalculationExceptionInvalidValueError')
            ->setCode(10105)
            ->critical()
            ->with([
                'method'          => $method,
                'operand'         => $operand,
                'args'            => $args,
            ])
            ->throw();
    }

    private function throwUnsupportedFunctionException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)->setMessage('msg.mathCalculationExceptionUnsupportedFunction')
            ->setCode(10106)
            ->with([
                'method'        => $method,
                'operand'       => $operand,
                'args'          => $args
            ])
            ->throw();
    }
}
