<?php

namespace Fooino\Core\Tasks\Tools;

use Illuminate\Database\Eloquent\Model;
use Exception;

class GenerateCodeTask
{
    private string $model = '';
    private string $field = 'code';
    private int $length = 5;

    private bool $isNumeric = true;
    private bool $lowerCase = false;
    private bool $upperCase = false;
    private bool $timestampStyle = false;
    private bool $easyNumericOtpStyle = false;

    private int $attempts = 0;
    private int $maxAttempts = 100;
    private array $duplicateCodes = [];

    public function model(string|Model $model): self
    {
        $this->model = ($model instanceof Model) ? get_class($model) : $model;
        return $this;
    }

    public function field(string $field): self
    {
        $this->field = $field;
        return $this;
    }

    public function length(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function isNumeric(bool $isNumeric): self
    {
        $this->isNumeric = $isNumeric;
        return $this;
    }

    public function lowerCase(): self
    {
        $this->lowerCase = true;
        return $this;
    }

    public function upperCase(): self
    {
        $this->upperCase = true;
        return $this;
    }

    public function timestampStyle(): self
    {
        $this->timestampStyle = true;
        return $this;
    }

    public function easyNumericOtpStyle(): self
    {
        $this->isNumeric(true);

        $this->easyNumericOtpStyle = true;

        return $this;
    }

    public function run(): string|int|float
    {
        $this->attempted();

        $this->validateSettings();

        $code = $this->generateCode();

        if (
            filled($this->model) &&
            (
                in_array($code, $this->duplicateCodes) ||
                app($this->model)->where($this->field, $code)->exists()
            )
        ) {

            $this->duplicateCodes = array_merge($this->duplicateCodes, [$code]);

            return $this->run();
        }

        return $code;
    }


    private function attempted(): void
    {
        $this->attempts++;

        if (
            $this->attempts > $this->maxAttempts
        ) {
            throw new Exception("The task attempts more than {$this->maxAttempts} times and can not generate code anymore");
        }
    }

    private function validateSettings(): void
    {
        if (
            $this->length <= 0
        ) {
            throw new Exception('The length must greater than zero');
        }

        if (
            $this->isNumeric &&
            $this->length > 100
        ) {
            throw new Exception('No more than 100 digits can be produced');
        }

        if (
            !$this->isNumeric &&
            $this->length > 1000
        ) {
            throw new Exception('No more than 1000 characters can be produced');
        }
    }

    private function generateCode(): string|int|float
    {
        return match (true) {

            $this->timestampStyle               => $this->generateInTimestampStyle(),

            $this->easyNumericOtpStyle          => $this->generateEasyNumericOtpStyle(),

            $this->isNumeric                    => $this->generateInNumeric(),

            default                             => $this->generateInAlphanumeric()
        };
    }


    private function generateInTimestampStyle(): string
    {
        return replaceForbiddenCharacters(value: strtolower(str()->random($this->length) . time() . str()->random($this->length)));
    }

    private function generateEasyNumericOtpStyle(): string
    {
        $code = $this->generateInNumeric();

        if (
            $this->length > 1
        ) {

            $randomChar = rand(1, ($this->length - 1));

            $code[$randomChar - 1] = $code[$randomChar];
        }

        return $code;
    }

    private function generateInNumeric(): string
    {
        $code = "";

        $code .= random_int(1, 9); // the first digit must not be 0 to prevent unwanted problems

        for ($i = 0; $i < ($this->length - 1); $i++) {
            $code .= random_int(0, 9);
        }

        return $code;
    }


    private function generateInAlphanumeric(): string
    {
        return replaceForbiddenCharacters(value: $this->prettifyCode(str()->random($this->length)));
    }

    private function prettifyCode(string $code): string
    {
        if (
            $this->lowerCase
        ) {
            return strtolower($code);
        }

        if (
            $this->upperCase
        ) {
            return strtoupper($code);
        }

        return $code;
    }
}
