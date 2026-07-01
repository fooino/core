<?php

namespace Fooino\Core\Concretes\Math;

use Fooino\Core\Interfaces\Mathable;
use Fooino\Core\Exceptions\MathCalculationException;
use RoundingMode;

class FooinoMathHandler implements Mathable
{
    private static array $instances = [];

    private const int BC_SCALE = 12;

    private const array TWO_OPERAND_FUNCTIONS = ['bcadd', 'bcsub', 'bcmul', 'bcdiv', 'bcmod'];

    private const array ONE_OPERAND_FUNCTIONS = ['bcpow', 'bcsqrt', 'bcceil', 'bcfloor', 'bcround'];

    /**
     * Difference Between BC_SCALE and $precision
     * 
     * All bc functions must use BC_SCALE for calculations
     * The $precision is just for returning truncated number(not rounded) and It will not used in calculations
     * 
     * Truncated number is good for each country policy
     * for example 1000.01 is not valid in Iran since 0.01 is worthless
     * but in other countries like USA it means cent.
     * So we use $precision = 0 For Iran and $precision = 2 for USA
     * 
     * Example: Math::setPrecision(precision: 0)->number(Math::sum(5.599, 5.499)));
     * 
     * Base on BC_SCALE the result is 11.098. if we assumed BC_SCALE = $precision = 0 the result was 10 which is wrong
     * To output number we use $precision = 0 and the result is 11
     * For calculations we use BC_SCALE which is a high and enough number to not lose precision
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1101 when precision is out of valid range
     */
    public function __construct(private int $precision = 12)
    {
        if (
            $this->getPrecision() > self::BC_SCALE ||
            $this->getPrecision() < 0
        ) {
            $this->throwInvalidPrecisionException();
        }
    }

    /**
     * Retrieve the current precision value used for truncating output numbers
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * Set a new precision value and return a fresh instance configured
     */
    public function setPrecision(int $precision): Mathable
    {
        /**  
         * Facade in laravel use singleton design pattern and we need new fresh instance by $precision
         * Do not chain setPrecision with setPrecision like Math::setPrecision(2)->setPrecision(3)
         * It can increase memory usage
         */
        return self::$instances[$precision] ??= new static(precision: $precision);
    }

    /**
     * Expand a number expressed in scientific notation (e.g. 1.5E+4) into its full numeric string representation
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1105 when the number is infinite or the exponent exceeds the allowable range
     */
    public function convertScientificNumber(string|int|float|array $number): string|array
    {
        if ($number === INF || $number === -INF) {
            // Very Big or small number is not allowed. 1.1E999 in float format will cast to INF. the INF is numeric so check it before is_numeric()
            $this->throwInvalidValueErrorException(method: 'convertScientificNumber', operand: $number);
        }

        if (!is_numeric($number)) {
            return $number;
        }

        $number = trim((string) $number);

        $regex = [
            '/',
            '^',                            // start with
            '([+-]?)',                      // the sign can be '+' or '-' or '' ---> first group: sign
            '(\d+\.\d*|\.\d+|\d+)',         // zero or more digits before dot  optional decimal dot  zero or more digits after decimal dot ---> second group: mantissa. it can be 123 | 123. | 123.45 | .45  (never empty, never just ".")
            '[Ee]',                         // the letter E or e: the exponent separator
            '([+-]?\d+)',                   // optional '+' or '-' sign   one or more digits ---> third group: exponent. it can be 3 or +3 or -3
            '$',                            // end with
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

        // If mantissa is zero, the whole number is zero like 0.0E+8
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

    /**
     * Remove all trailing zeros after the decimal point from a number, returning a clean numeric string
     */
    public function trimTrailingZeros(string|int|float $number): string
    {
        return $this->_trimTrailingZeros(number: $number, expandScientific: true);
    }

    /**
     * Strip trailing zeros from a numeric string after the decimal point, optionally expanding scientific notation first
     */
    private function _trimTrailingZeros(string|int|float $number, bool $expandScientific = false): string
    {
        if ($expandScientific) {

            $number = $this->convertScientificNumber(number: $number);
        }

        return (is_numeric($number) && strpos($number, '.') !== false) ? rtrim(rtrim($number, '0'), '.') : $number;
    }

    /**
     * Count how many decimal places a number has after trimming any trailing zeros
     */
    public function countDecimalPlaces(string|int|float $number): int
    {
        $number = $this->trimTrailingZeros(number: $number);

        $dotPos = strrpos($number, '.');

        return (!is_numeric($number) || $dotPos === false) ? 0 : strlen(substr($number, $dotPos + 1));
    }

    /**
     * Format one or more numbers by truncating them to the configured precision, removing trailing zeros, and returning clean numeric strings
     */
    public function number(string|int|float|array ...$number): string|array
    {
        return $this->_number(true, ...$number);
    }

    /**
     * Internal implementation that truncates each number to the configured precision and returns clean numeric strings
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1102 when no numbers are provided
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1103 when a non-numeric value is provided
     */
    private function _number(bool $expandScientific = false, string|int|float|array ...$number): string|array
    {
        $wasArray = count($number) === 1 && is_array($number[0]);

        $numbers = $wasArray ? $number[0] : $number;

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

        return $wasArray || count($numbers) !== 1 ? $numbers : $numbers[0];
    }

    /**
     * Format a number with thousands separators and apply precision truncation, returning a locale-friendly currency-style string
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1103 when a non-numeric value is provided
     */
    public function numberFormat(string|int|float $number, string $thousandsSeparator = ','): string
    {
        $sanitized = trim((string) $number);
        $sign = '';

        if (in_array($sanitized[0] ?? '', ['-', '+'])) {

            $sign = $sanitized[0]; // the number can be -2-000-000.001 which is negtive number with - thousandsSeparator

            $sanitized = substr($sanitized, 1);
        }

        $sanitized = str_replace(array_unique([$thousandsSeparator, ',']), '', $sanitized);

        $sanitized = $this->convertScientificNumber(number: $sanitized);

        if (!is_numeric($sanitized)) {
            $this->throwInvalidArgumentTypeException(method: 'numberFormat', operand: $number);
        }

        $sanitized = $this->_number(false, $sanitized); // apply precision

        list($sign, $integer, $decimal) = $this->numberParts(number: ($sign . $sanitized));

        $integerWithSeparators = preg_replace(pattern: '/\B(?=(\d{3})+(?!\d))/', replacement: $thousandsSeparator, subject: $integer);

        return trim($sign . $integerWithSeparators . ((isZero($decimal) || trim($decimal) === '') ? '' : '.' . $decimal));
    }

    /**
     * Add a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    public function sum(string|int|float|array ...$operand): string
    {
        return $this->calc(method: 'bcadd', operand: $this->resolveVariadicParameter($operand));
    }

    /**
     * Subtract a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    public function subtract(string|int|float|array ...$operand): string
    {
        return $this->calc(method: 'bcsub', operand: $this->resolveVariadicParameter($operand));
    }

    /**
     * Multiply a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    public function multiply(string|int|float|array ...$operand): string
    {
        return $this->calc(method: 'bcmul', operand: $this->resolveVariadicParameter($operand));
    }

    /**
     * Divide a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    public function divide(string|int|float|array ...$operand): string
    {
        return $this->calc(method: 'bcdiv', operand: $this->resolveVariadicParameter($operand));
    }

    /**
     * Compute the modulus (remainder) of a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    public function remainder(string|int|float|array ...$operand): string
    {
        return $this->calc(method: 'bcmod', operand: $this->resolveVariadicParameter($operand));
    }

    /**
     * Raise an arbitrary precision number to a given exponent
     */
    public function power(string|int|float|array $number, int $exponent = 2): string|array
    {
        return $this->calc(method: 'bcpow', operand: $number, args: ['exponent' => $exponent]);
    }

    /**
     * Get the square root of an arbitrary precision number
     */
    public function sqrt(string|int|float|array $number): string|array
    {
        return $this->calc(method: 'bcsqrt', operand: $number);
    }

    /**
     * Round a number up to the next integer (ceiling), away from zero
     */
    public function roundUp(string|int|float|array $number): string|array
    {
        return $this->calc(method: 'bcceil', operand: $number);
    }

    /**
     * Round a number down to the previous integer (floor), toward zero
     */
    public function roundDown(string|int|float|array $number): string|array
    {
        return $this->calc(method: 'bcfloor', operand: $number);
    }

    /**
     * Round a number to a specified precision using a configurable rounding mode
     */
    public function roundClose(string|int|float|array $number, int $precision = 0, RoundingMode $mode = RoundingMode::HalfAwayFromZero): string|array
    {
        return $this->calc(method: 'bcround', operand: $number, args: ['precision' => $precision, 'mode' => $mode]);
    }

    /**
     * Check if the first number is strictly greater than the second using arbitrary precision comparison
     */
    public function greaterThan(string|int|float $num1, string|int|float $num2): bool
    {
        return $this->compare(num1: $num1, num2: $num2) === 1;
    }

    /**
     * Check if the first number is greater than or equal to the second using arbitrary precision comparison
     */
    public function greaterThanOrEqual(string|int|float $num1, string|int|float $num2): bool
    {
        return $this->compare(num1: $num1, num2: $num2) !== -1;
    }

    /**
     * Check if the first number is strictly less than the second using arbitrary precision comparison
     */
    public function lessThan(string|int|float $num1, string|int|float $num2): bool
    {
        return $this->compare(num1: $num1, num2: $num2) === -1;
    }

    /**
     * Check if the first number is less than or equal to the second using arbitrary precision comparison
     */
    public function lessThanOrEqual(string|int|float $num1, string|int|float $num2): bool
    {
        return $this->compare(num1: $num1, num2: $num2) !== 1;
    }

    /**
     * Check if two numbers are exactly equal using arbitrary precision comparison
     */
    public function equal(string|int|float $num1, string|int|float $num2): bool
    {
        return $this->compare(num1: $num1, num2: $num2) === 0;
    }

    /**
     * Check if two numbers differ from each other using arbitrary precision comparison
     */
    public function notEqual(string|int|float $num1, string|int|float $num2): bool
    {
        return !$this->equal(num1: $num1, num2: $num2);
    }

    /**
     * Perform arbitrary precision comparison between two numbers to determine their ordering
     */
    private function compare(string|int|float $num1, string|int|float $num2): int
    {
        list($num1, $num2) = $this->validateAndNormalizeNumbersForCompare(num1: $num1, num2: $num2);

        return bccomp(num1: $num1, num2: $num2, scale: self::BC_SCALE);
    }

    /**
     * Split a numeric string into its sign, integer, and decimal components for reconstruction
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

        $integer = (string) ((isZero($integer) || trim($integer) === '') ? '0' : $integer);
        $decimal = (string) ((isZero($decimal) || trim($decimal) === '') ? '0' : $decimal);

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
     * Rebuild a numeric string from its sign, integer, and decimal parts, applying precision truncation
     */
    private function assembleNumber(string|int|float $number, int|null $precision = null): string
    {
        list($sign, $integer, $decimal) = $this->numberParts(number: $number);

        $decimal = isZero($decimal) ? '' : ('.' . (is_null($precision) ? $decimal : substr($decimal, 0, $precision)));

        return trim($sign . $integer . $decimal);
    }

    /**
     * Route a math operation to the correct calculation handler based on the number of operands it requires
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1106 when the method is not recognised
     */
    private function calc(string $method, string|int|float|array $operand, array $args = []): string|array
    {
        if (in_array($method, self::TWO_OPERAND_FUNCTIONS)) {

            return $this->calcTwoOperand(method: $method, operand: $operand, args: $args);
        }

        if (in_array($method, self::ONE_OPERAND_FUNCTIONS)) {

            return $this->calcOneOperand(method: $method, operand: $operand, args: $args);
        }

        $this->throwUnsupportedFunctionException(method: $method, operand: $operand, args: $args);
    }

    /**
     * Chain binary bcmath operations across a series of numbers to produce a single result
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1106 when the method is not recognised
     */
    private function calcTwoOperand(string $method, string|int|float|array $operand, array $args = []): string
    {
        $numbers = $this->validateAndNormalizeNumbers(method: $method, operand: $operand, args: $args);

        $defaultTemplate = [
            'num1'  => 'bc_args.result',
            'num2'  => 'bc_args.number',
            'scale' => 'bc_args.scale'
        ];

        list($result, $start, $template) = match ($method) {

            'bcadd'             => ['0', 0, $defaultTemplate],

            'bcmul'             => ['1', 0, $defaultTemplate],

            'bcsub'             => [$numbers[0], 1, $defaultTemplate],

            'bcdiv'             => [$numbers[0], 1, $defaultTemplate],

            'bcmod'             => [$numbers[0], 1, $defaultTemplate],

            default             => $this->throwUnsupportedFunctionException(method: $method, operand: $operand, args: $args),
        };

        $data = [
            'bc_args'   => [
                'result'    => $result,
                'number'    => null,
                'scale'     => self::BC_SCALE
            ]
        ];

        for ($i = $start; $i < count($numbers); $i++) {

            $data['bc_args']['number'] = $numbers[$i];

            $mapped = [];
            foreach ($template as $argKey => $search) {

                $mapped[$argKey] = data_get($data, $search);

                // 
            }

            $result = $data['bc_args']['result'] = call_user_func($method, ...$mapped);
        }

        return $this->_trimTrailingZeros(number: $result);
    }

    /**
     * Apply a unary bcmath operation to each number in the operand set
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1106 when the method is not recognised
     */
    private function calcOneOperand(string $method, string|int|float|array $operand, array $args = []): string|array
    {
        $numbers = $this->validateAndNormalizeNumbers(method: $method, operand: $operand, args: $args);

        $template = match ($method) {

            'bcpow'             => ['num' => 'bc_args.value', 'exponent' => 'bc_args.args.exponent', 'scale'  => 'bc_args.scale'],

            'bcsqrt'            => ['num' => 'bc_args.value', 'scale' => 'bc_args.scale'],

            'bcceil'            => ['num' => 'bc_args.value'],

            'bcfloor'           => ['num' => 'bc_args.value'],

            'bcround'           => ['num' => 'bc_args.value', 'precision' => 'bc_args.args.precision', 'mode' => 'bc_args.args.mode'],

            default             => $this->throwUnsupportedFunctionException(method: $method, operand: $operand, args: $args),
        };

        $data = [
            'bc_args'         => [
                'value'           => null,
                'scale'           => self::BC_SCALE,
                'args'            => $args
            ],
        ];

        foreach ($numbers as $key => $value) {

            $data['bc_args']['value'] = $value;

            $mapped = [];
            foreach ($template as $argKey => $search) {

                $mapped[$argKey] = data_get($data, $search);

                // 
            }

            $numbers[$key] = $this->_trimTrailingZeros(number: call_user_func($method, ...$mapped));
        }

        return is_array($operand) ? $numbers : $numbers[0];
    }


    /**
     * Convert scientific notation and type-validate every operand before calculation
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1102 when insufficient operands are provided
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1103 when a non-numeric operand is provided
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1104 when attempting to divide by zero
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1105 when the operand is negative for sqrt, infinite, or the exponent exceeds the allowable range
     */
    private function validateAndNormalizeNumbers(string $method, string|int|float|array $operand, array $args = []): array
    {
        $numbers = !is_array($operand) ? [$operand] : $operand;

        if (
            count($numbers) === 0 ||
            (count($numbers) < 2 && in_array($method, self::TWO_OPERAND_FUNCTIONS))
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

                $this->throwDivisionByZeroException(method: $method, operand: $operand, args: $args);
            }

            if (
                $method === 'bcpow' &&
                isZero($number) &&
                $args['exponent'] < 0
            ) {

                $this->throwDivisionByZeroException(method: $method, operand: $operand, args: $args);
            }

            if (
                $method === 'bcsqrt' &&
                lessThan($number, 0)
            ) {

                $this->throwInvalidValueErrorException(method: $method, operand: $operand, args: $args);
            }
        }

        return $numbers;
    }

    /**
     * Unwrap a single array passed as a variadic argument into a flat operand list
     */
    private function resolveVariadicParameter(array $parameter): array
    {
        return count($parameter) === 1 && is_array($parameter[0]) ? $parameter[0] : $parameter;
    }

    /**
     * Convert scientific notation and type-validate both comparison operands
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1103 when a non-numeric operand is provided
     */
    private function validateAndNormalizeNumbersForCompare(string|int|float $num1, string|int|float $num2): array
    {
        $num1 = $this->convertScientificNumber(number: $num1);

        $num2 = $this->convertScientificNumber(number: $num2);

        if (
            !is_numeric($num1) ||
            !is_numeric($num2)
        ) {
            $this->throwInvalidArgumentTypeException(method: 'bccomp', operand: [$num1, $num2]);
        }

        return [
            $num1,
            $num2
        ];
    }

    /**
     * Abort execution when the configured precision falls outside the allowed range
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1101
     */
    private function throwInvalidPrecisionException(): never
    {
        app(MathCalculationException::class)
            ->_1101()
            ->with([
                'precision' => $this->getPrecision(),
                'bc_scale'  => self::BC_SCALE
            ])
            ->throw();
    }

    /**
     * Abort execution when an insufficient number of operands are provided for the operation
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1102
     */
    private function throwInvalidArgumentsCountException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->_1102()
            ->with([
                'method'    => $method,
                'operand'   => $operand,
                'args'      => $args
            ])
            ->throw();
    }

    /**
     * Abort execution when a non-numeric value is encountered where a number is required
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1103
     */
    private function throwInvalidArgumentTypeException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->_1103()
            ->with([
                'method'    => $method,
                'operand'   => $operand,
                'args'      => $args
            ])
            ->throw();
    }

    /**
     * Abort execution when a division or modulo by zero is attempted
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1104
     */
    private function throwDivisionByZeroException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->_1104()
            ->with([
                'method'      => $method,
                'operand'     => $operand,
                'args'        => $args,
            ])
            ->throw();
    }

    /**
     * Abort execution when an operand value is invalid for the requested operation
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1105
     */
    private function throwInvalidValueErrorException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->_1105()
            ->with([
                'method'          => $method,
                'operand'         => $operand,
                'args'            => $args,
            ])
            ->throw();
    }

    /**
     * Abort execution when an unrecognised bcmath function name is provided
     *
     * @throws \Fooino\Core\Exceptions\MathCalculationException  with 1106
     */
    private function throwUnsupportedFunctionException(string $method, string|int|float|array $operand, array $args = []): never
    {
        app(MathCalculationException::class)
            ->_1106()
            ->with([
                'method'        => $method,
                'operand'       => $operand,
                'args'          => $args
            ])
            ->throw();
    }
}
