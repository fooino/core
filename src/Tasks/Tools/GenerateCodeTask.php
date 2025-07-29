<?php

namespace Fooino\Core\Tasks\Tools;

use Illuminate\Support\Str;
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

    public function lowerCase(bool $lowerCase = true): self
    {
        $this->lowerCase = $lowerCase;
        return $this;
    }

    public function upperCase(bool $upperCase = true): self
    {
        $this->upperCase = $upperCase;
        return $this;
    }

    public function timestampStyle(bool $timestampStyle = true): self
    {
        $this->timestampStyle = $timestampStyle;
        return $this;
    }

    public function run(): string|int
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

        $this->attempts++;
        if (
            $this->attempts > $this->maxAttempts
        ) {
            throw new Exception("The task attempts more than {$this->maxAttempts} times and can not generate code anymore");
        }

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

    private function generateCode(): int|string
    {
        if (
            $this->timestampStyle
        ) {

            return replaceForbiddenCharacters(
                strtolower(
                    Str::random($this->length) . time() . Str::random($this->length)
                )
            );

            // 
        } elseif (
            $this->isNumeric
        ) {
            $code = "";

            for ($i = 0; $i < $this->length; $i++) {
                $code .= ($i == 0) ? rand(1, 9) : rand(0, 9);
            }

            return $code;

            //
        } else {

            return replaceForbiddenCharacters($this->prettifyCode(Str::random($this->length)));

            // 
        }
    }

    private function prettifyCode(string $code): string
    {
        if (
            $this->lowerCase
        ) {
            $code = strtolower($code);
            return $code;
        }

        if (
            $this->upperCase
        ) {
            $code = strtoupper($code);
            return $code;
        }

        return $code;
    }
}
