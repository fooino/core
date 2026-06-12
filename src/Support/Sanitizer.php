<?php

namespace Fooino\Core\Support;

use Fooino\Core\Exceptions\InfiniteLoopException;

class Sanitizer
{
    private array $attempted = [];

    private const int MAX_ATTEMPT = 25;

    public function __construct(private string|int|float|null|bool|array $value) {}

    /**
     * Get the current value
     */
    public function value(): string|int|float|null|bool|array
    {
        return $this->value;
    }

    /**
     * Set a new value
     */
    private function setValue(string|int|float|null|bool|array $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Remove or replace forbidden characters from the value
     */
    public function replaceForbiddenCharacters(array $excludes = [], string $replaceWith = ''): static
    {
        $value = $this->value();

        if (
            (!is_string($value) && !is_array($value)) ||
            $value === '' || $value === []
        ) {
            return $this;
        }

        $forbiddens = $this->forbiddenCharacters();

        foreach ($excludes as $exclude) {

            foreach ($forbiddens as $key => $forbidden) {

                if ($exclude === $forbidden) {

                    unset($forbiddens[$key]);
                }
            }
        }

        return $this->setValue(value: $this->replace(search: $forbiddens, replace: $replaceWith, subject: $value));
    }

    /**
     * Convert the value to lowercase
     */
    public function lowercase(): static
    {
        $value = $this->value();

        if (
            (!is_string($value) && !is_array($value)) ||
            $value === '' || $value === []
        ) {
            return $this;
        }

        return $this->setValue(value: $this->toLowercase(value: $value));
    }

    /**
     * Convert the value to uppercase
     */
    public function uppercase(): static
    {
        $value = $this->value();

        if (
            (!is_string($value) && !is_array($value)) ||
            $value === '' || $value === []
        ) {
            return $this;
        }

        return $this->setValue(value: $this->toUppercase(value: $value));
    }

    /**
     * Collapse consecutive occurrences of a character into a single occurrence
     */
    public function collapse(string $char): static
    {
        $value = $this->value();

        if (
            (!is_string($value) && !is_array($value)) ||
            $value === '' || $value === []
        ) {
            return $this;
        }

        return $this->setValue(value: $this->collapseValue(value: $value, char: $char));
    }

    /**
     * Trim characters from the beginning and end of the value
     */
    public function trim(string $char): static
    {
        $value = $this->value();

        if (
            (!is_string($value) && !is_array($value)) ||
            $value === '' || $value === []
        ) {
            return $this;
        }

        return $this->setValue(value: $this->trimValue(value: $value, char: $char));
    }

    /**
     * Default set of characters considered forbidden or harmful
     */
    private function forbiddenCharacters(): array
    {
        $chars = [
            ' ',
            '-',
            '.',
            '!',
            '@',
            '#',
            '$',
            '%',
            '^',
            '&',
            '*',
            '(',
            ')',
            '=',
            '+',
            '{',
            '}',
            ':',
            ';',
            '"',
            "'",
            '?',
            '؟',
            '<',
            '>',
            ',',
            '|',
            '`',
            '/',
            '\\',
            '[',
            ']',
            '~',
            '°',
            '../',
            '_'
        ];

        usort($chars, fn($a, $b) => strlen($b) <=> strlen($a));

        return $chars;
    }

    /**
     * Replace search strings in the subject, handling arrays recursively.
     */
    private function replace(string|array $search, string|array $replace, string|array $subject): string|array
    {
        $this->assertRecursionLimit(method: 'replace');

        if (is_string($subject)) {
            return str_replace(search: $search, replace: $replace, subject: $subject);
        }

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->replace(search: $search, replace: $replace, subject: $item) : $item, $subject);
    }

    /**
     * Convert value to lowercase, handling arrays recursively
     */
    private function toLowercase(string|array $value): string|array
    {
        $this->assertRecursionLimit(method: 'toLowercase');

        if (is_string($value)) {
            return mb_strtolower(string: $value);
        }

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->toLowercase(value: $item) : $item, $value);
    }

    /**
     * Convert value to uppercase, handling arrays recursively
     */
    private function toUppercase(string|array $value): string|array
    {
        $this->assertRecursionLimit(method: 'toUppercase');

        if (is_string($value)) {
            return mb_strtoupper(string: $value);
        }

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->toUppercase(value: $item) : $item, $value);
    }

    /**
     * Collapse consecutive characters in value, handling arrays recursively
     */
    private function collapseValue(string|array $value, string $char): string|array
    {
        $this->assertRecursionLimit(method: 'collapseValue');

        if ($char === '') {
            return $value;
        }

        if (is_string($value)) {
            return preg_replace(pattern: '/' . preg_quote($char, '/') . '+/u', replacement: $char, subject: $value);
        }

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->collapseValue(value: $item, char: $char) : $item, $value);
    }

    /**
     * Trim characters from value, handling arrays recursively
     */
    private function trimValue(string|array $value, string $char): string|array
    {
        $this->assertRecursionLimit(method: 'trimValue');

        if (is_string($value)) {

            return trim($value, $char);
        }

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->trimValue(value: $item, char: $char) : $item, $value);
    }

    private function assertRecursionLimit(string $method): void
    {
        $this->attempted[$method] ??= 0;
        $this->attempted[$method] += 1;

        if ($this->attempted[$method] > self::MAX_ATTEMPT) {

            app(InfiniteLoopException::class)
                ->setMessage(FE['SANITIZER_MADE_INFINITE_LOOP_MESSAGE'])
                ->setCode(FE['SANITIZER_MADE_INFINITE_LOOP_CODE'])
                ->critical()
                ->shouldReport()
                ->with([
                    'method'    => $method,
                    'attempted' => $this->attempted[$method],
                ])
                ->throw();
        }
    }
}
