<?php

namespace Fooino\Core\Concretes\Math;


class MathManager
{
    /**
     * set precision and trim trailing zeroes with construct method.
     *
     * @param  int  $precision
     * @param  bool $trimTrailingZeroes
     * 
     */
    public function __construct(
        public int $precision = 10,
        public bool $trimTrailingZeroes = true
    ) {
        \bcscale(10);
    }

    /**
     * Get new instance of math if you want change precision or trimTrailingZeroes default value
     *
     * @param  int  $precision
     * @param  bool  $trimTrailingZeroes
     * 
     * @return self
     */
    public function instance(...$args): self
    {
        return new MathManager(...$args);
    }

    /**
     * get the precision
     * 
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * get the trim trailing zeros
     * 
     * @return bool
     */
    public function getTrimTrailingZeroes(): bool
    {
        return $this->trimTrailingZeroes;
    }

    /**
     * Trim trailing zeroes from end of number base on decimal separator
     *
     * @param  string|int|float|null $number
     * @param  string $decimalSeparator
     * 
     * @return string
     */
    public function trimTrailingZeroes(
        string|int|float|null $number,
        string $decimalSeparator = '.'
    ): string {

        $number = $this->convertScientificNumber(number: $number);

        if (
            $this->trimTrailingZeroes
        ) {
            return \strpos($number, $decimalSeparator) !== false ? \rtrim(\rtrim($number, '0'), $decimalSeparator) : $number;
        }

        return $number;
    }

    /**
     * Convert scientific number to well format string
     *
     * @param  string|int|float|null $number
     * 
     * @return string
     */
    public function convertScientificNumber(string|int|float|null $number): string
    {
        if (is_null($number)) {
            return '0';
        }

        return (string) \preg_match(pattern: '/-?\d*\.?\d+[Ee][+-]?\d+/', subject: $number) ? \sprintf("%.10f", \floatval($number)) : $number;
    }

    /**
     * Convert number to well format base on precision and trimTrailingZeroes values
     *
     * @param  string|int|float|null $number
     * 
     * @return string
     */
    public function number(string|int|float|null $number): string
    {
        $number = $this->convertScientificNumber(number: $number);

        $explode = \explode('.', $number);

        $real = $explode[0] ?? 0;
        $real = filled($real) ? $real : 0;

        $decimal = \substr($explode[1] ?? 0, 0, $this->precision);


        $number = $real . "." . $decimal;

        $number = $this->trimTrailingZeroes(number: $number);

        return (string) $number;
    }


    /**
     * Convert number to well format base on precision and trimTrailingZeroes values like 2,000,000.23
     *
     * @param  string|int|float|null $number
     * @param string $decimalSeparator
     * @param string $thousandsSeparator
     * @param int|float $divisor
     * 
     * @return string
     */
    public function numberFormat(
        string|float|int|null $number,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ',',
        int|float $divisor = 1
    ): string {

        if (
            $divisor != 1
        ) {
            $number = $this->divide(
                a: $number,
                b: $divisor
            );
        }

        $number = \number_format(
            num: $this->number(number: $number),
            decimals: $this->decimalPlaceNumber(number: $this->trimTrailingZeroes(number: $number), decimalSeparator: $decimalSeparator),
            decimal_separator: $decimalSeparator,
            thousands_separator: $thousandsSeparator
        );

        return $this->trimTrailingZeroes(
            number: $number,
            decimalSeparator: $decimalSeparator
        );
    }

    /**
     * Add two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return string
     */
    public function add(
        string|int|float|null $a,
        string|int|float|null $b
    ): string {

        return $this->number(
            number: \bcadd(
                $this->convertScientificNumber(number: $a),
                $this->convertScientificNumber(number: $b),
            )
        );
    }

    /**
     * subtract two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return string
     */
    public function subtract(
        string|int|float|null $a,
        string|int|float|null $b
    ): string {

        return $this->number(
            number: \bcsub(
                $this->convertScientificNumber(number: $a),
                $this->convertScientificNumber(number: $b),
            )
        );
    }

    /**
     * multiply two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return string
     */
    public function multiply(
        string|int|float|null $a,
        string|int|float|null $b
    ): string {

        return $this->number(
            number: \bcmul(
                $this->convertScientificNumber(number: $a),
                $this->convertScientificNumber(number: $b),
            )
        );
    }

    /**
     * divide two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return string
     */
    public function divide(
        string|int|float|null $a,
        string|int|float|null $b
    ): string {

        return $this->number(
            number: \bcdiv(
                $this->convertScientificNumber(number: $a),
                $this->convertScientificNumber(number: $b),
            )
        );
    }

    /**
     * modulus two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return string
     */
    public function modulus(
        string|int|float|null $a,
        string|int|float|null $b
    ): string {

        return $this->number(
            number: \bcmod(
                $this->convertScientificNumber(number: $a),
                $this->convertScientificNumber(number: $b),
            )
        );
    }

    /**
     * Raise an arbitrary precision number to another
     *
     * @param  string|int|float|null $number
     * @param  string|int|float|null $exponent
     * 
     * @return string
     */
    public function power(
        string|int|float|null $number,
        string|int|float|null $exponent = 2
    ): string {
        return $this->number(
            number: \bcpow(
                $this->convertScientificNumber(number: $number),
                $this->convertScientificNumber(number: $exponent),
            )
        );
    }

    /**
     * Get the square root of an arbitrary precision number
     *
     * @param  string|int|float|null $number
     * 
     * @return string
     */
    public function sqrt(string|int|float|null $number): string
    {
        return $this->number(
            number: \bcsqrt(
                $this->convertScientificNumber(number: $number),
            )
        );
    }


    /**
     * TODO[epic=upgrade] after updating to php 8.4 , replace the rounds function with bcMath functions
     * 
     * Round the number up
     * 
     * @param string|int|float|null $number
     * 
     * @return string
     */
    public function roundUp(string|int|float|null $number): string
    {
        return $this->number(
            number: \ceil($this->convertScientificNumber(number: $number))
        );
    }

    /**
     * Round the number down
     * 
     * @param string|int|float|null $number
     * 
     * @return string
     */
    public function roundDown(string|int|float|null $number): string
    {
        return $this->number(
            number: \floor($this->convertScientificNumber(number: $number))
        );
    }

    /**
     * Round the number to the nearest integer
     * 
     * @param string|int|float|null $number
     * @param int $precision
     * 
     * @return string
     */
    public function roundClose(
        string|int|float|null $number,
        int $precision = 0
    ): string {
        return $this->number(
            number: \round(
                num: $this->convertScientificNumber(number: $number),
                precision: $precision
            )
        );
    }

    /**
     * compare two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return bool
     */
    public function greaterThan(
        string|int|float|null $a,
        string|int|float|null $b
    ): bool {
        return (bool)(\bccomp(
            $this->convertScientificNumber(number: $a),
            $this->convertScientificNumber(number: $b),
        ) === 1);
    }

    /**
     * compare two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return bool
     */
    public function greaterThanOrEqual(
        string|int|float|null $a,
        string|int|float|null $b
    ): bool {
        return (bool)(\bccomp(
            $this->convertScientificNumber(number: $a),
            $this->convertScientificNumber(number: $b),
        ) !== -1);
    }

    /**
     * compare two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return bool
     */
    public function lessThan(
        string|int|float|null $a,
        string|int|float|null $b
    ): bool {
        return (bool)(\bccomp(
            $this->convertScientificNumber(number: $a),
            $this->convertScientificNumber(number: $b),
        ) === -1);
    }

    /**
     * compare two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return bool
     */
    public function lessThanOrEqual(
        string|int|float|null $a,
        string|int|float|null $b
    ): bool {
        return (bool)(\bccomp(
            $this->convertScientificNumber(number: $a),
            $this->convertScientificNumber(number: $b),
        ) !== 1);
    }

    /**
     * compare two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return bool
     */
    public function equal(
        string|int|float|null $a,
        string|int|float|null $b
    ): bool {
        return (bool)(\bccomp(
            $this->convertScientificNumber(number: $a),
            $this->convertScientificNumber(number: $b),
        ) === 0);
    }


    /**
     * compare two number
     *
     * @param  string|int|float|null $a
     * @param  string|int|float|null $b
     * 
     * @return bool
     */
    public function notEqual(
        string|int|float|null $a,
        string|int|float|null $b
    ): bool {
        return (bool)!$this->equal(a: $a, b: $b);
    }

    /**
     * get decimal count of number
     *
     * @param  string|int|float|null $number
     * 
     * @param string $decimalSeparator
     * 
     * @return int
     */
    public function decimalPlaceNumber(
        string|int|float|null $number,
        string $decimalSeparator = '.'
    ): int {
        return (int) \strlen(\substr(\strrchr($this->number(number: $number), $decimalSeparator), 1));
    }
}
