<?php

namespace Fooino\Core\Support;

use Fooino\Core\Exceptions\InfiniteLoopException;
use Fooino\Core\Tasks\GetAllowedHTMLTagsTask;
use Fooino\Core\Tasks\GetForbiddenCharactersTask;
use Fooino\Core\Tasks\GetForbiddenFilesTask;

class Sanitizer
{
    private const int MAX_DEPTH = 25;

    /**
     * Accept the initial value and make it available to all sanitizer pipeline methods,
     * validating its nesting depth up front so deeply nested arrays fail before any recursive walk
     */
    public function __construct(private string|int|float|null|bool|array|object $value)
    {
        $this->validateValue(value: $value);
    }

    /**
     * Get the current value
     */
    public function value(): string|int|float|null|bool|array|object
    {
        return $this->value;
    }

    /**
     * Set a new value
     */
    private function setValue(string|int|float|null|bool|array|object $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Normalize the input by converting Persian/Arabic digits and letters,
     * removing zero-width non-joiners, stripping XSS vectors, and trimming whitespace
     */
    public function normalizeInput(array $includeHTMLTags = []): static
    {
        $value = $this->value();

        $allowedTags = array_merge(GetAllowedHTMLTagsTask::instance()->run(), $includeHTMLTags);

        $trimmed = is_string($value) ? $this->trimValue(value: $value) : $value;

        $isJson = isJson(value: $value) && !is_numeric($trimmed) && !in_array($trimmed, ['true', 'false', 'null', '{}']);

        if ($isJson) {

            $decoded = jsonDecode(json: $value);

            $value = match (true) {

                in_array(gettype($decoded), ['object', 'array']) => jsonDecodeToArray(json: $value),

                default                                          => $decoded
            };

            $this->validateValue(value: $value); // check again the depth of value when is decoded to array
        }

        if (is_array($value)) {

            array_walk_recursive($value, fn(mixed &$item) => $item = $this->normalizeValue(value: $item, allowedTags: $allowedTags));
        }

        if (is_string($value)) {

            $value = $this->normalizeValue(value: $value, allowedTags: $allowedTags);
        }

        return $this->setValue(value: ($isJson) ? jsonEncode($value) : $value);
    }

    /**
     * Remove or replace forbidden or harmful characters from the value
     */
    public function replaceForbiddenCharacters(array $excludes = [], string $replaceWith = ''): static
    {
        if ($this->valueIsSanitizable() === false) {

            return $this;
        }

        $forbiddens = $this->excludeFromSet(
            set: GetForbiddenCharactersTask::instance()->run(),
            excludes: $excludes
        );

        $value = $this->replace(
            search: $forbiddens,
            replace: $replaceWith,
            subject: $this->value()
        );

        return $this->setValue(value: $value);
    }

    /**
     * Remove or replace forbidden or sensitive file names and extensions from the value
     */
    public function replaceForbiddenFiles(array $excludes = [], string $replaceWith = ''): static
    {
        if ($this->valueIsSanitizable() === false) {

            return $this;
        }

        $forbiddens = $this->excludeFromSet(
            set: GetForbiddenFilesTask::instance()->run(),
            excludes: $excludes
        );

        $value = $this->replace(
            search: $forbiddens,
            replace: $replaceWith,
            subject: $this->value()
        );

        return $this->setValue(value: $value);
    }

    /**
     * Remove or replace emoji characters from the value
     */
    public function replaceEmoji(string $replaceWith = ''): static
    {
        if ($this->valueIsSanitizable() === false) {

            return $this;
        }

        return $this->setValue(value: $this->replaceEmojiValue(value: $this->value(), replaceWith: $replaceWith));
    }

    /**
     * Convert the value to lowercase
     */
    public function lowercase(): static
    {
        if ($this->valueIsSanitizable() === false) {

            return $this;
        }

        return $this->setValue(value: $this->toLowercase(value: $this->value()));
    }

    /**
     * Convert the value to uppercase
     */
    public function uppercase(): static
    {
        if ($this->valueIsSanitizable() === false) {

            return $this;
        }

        return $this->setValue(value: $this->toUppercase(value: $this->value()));
    }

    /**
     * Collapse consecutive occurrences of a character into a single occurrence
     */
    public function collapse(string $char): static
    {
        if ($this->valueIsSanitizable() === false) {

            return $this;
        }

        return $this->setValue(value: $this->collapseValue(value: $this->value(), char: $char));
    }

    /**
     * Trim characters from the beginning and end of the value
     */
    public function trim(string $char = " \n\r\t\v\0"): static
    {
        if ($this->valueIsSanitizable() === false) {

            return $this;
        }

        return $this->setValue(value: $this->trimValue(value: $this->value(), char: $char));
    }

    /**
     * Normalize a scalar value: convert digits, replace Arabic letters,
     * remove half-spaces, strip XSS vectors from allowed tags, and trim
     */
    private function normalizeValue(string|int|float|null|bool|array|object $value, array $allowedTags = []): string|int|float|null|bool|array|object
    {
        if (
            is_int($value) ||
            is_float($value) ||
            is_null($value) ||
            is_bool($value) ||
            is_array($value) ||
            is_object($value)
        ) {
            return $value;
        }

        // remove ZWNJ, ZWJ, BOM characters
        $value = preg_replace('/[\x{200C}\x{200D}\x{FEFF}]/u', '', $value);

        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = range(0, 9);
        $replaced = str_replace($arabic, $english, str_replace($persian, $english, $value));

        $arabicLetters = ['ي', 'ك'];
        $persianLetters = ['ی', 'ک'];
        $replaced = str_replace($arabicLetters, $persianLetters, $replaced);

        $replaced = strip_tags($replaced, $allowedTags);

        $replaced = $this->trimValue(value: $replaced);

        return $replaced;
    }

    /**
     * Replace search strings in the subject, handling arrays recursively.
     */
    private function replace(string|array $search, string|array $replace, string|array $subject): string|array
    {
        if (is_string($subject)) {

            return str_replace(search: $search, replace: $replace, subject: $subject);
        }

        return array_map(fn(mixed $item) => is_string($item) || is_array($item) ? $this->replace(search: $search, replace: $replace, subject: $item) : $item, $subject);
    }

    /**
     * Strip or replace emoji characters from a string or array of strings using Unicode range matching
     */
    private function replaceEmojiValue(string|array $value, string $replaceWith): string|array
    {
        if (is_string($value)) {

            $pattern = '/(' .
                '[\x{1F600}-\x{1F64F}]' .      // Emoticons                             😀, 😎, 😭, 🙏, 🤔
                '|[\x{1F300}-\x{1F5FF}]' .     // Misc Symbols & Pictographs            🐶, 🏠, ⌛, 🎉, 🔪
                '|[\x{1F680}-\x{1F6FF}]' .     // Transport & Map                       🚗, 🚲, ✈️, 🏁, 🚦
                '|[\x{1F1E6}-\x{1F1FF}]' .     // Regional Indicator (Flags)            🇦🇽, 🇧🇬, 🇧🇱, 🇧🇷, 🇨🇦
                '|[\x{1F900}-\x{1F9FF}]' .     // Supplemental Symbols & Pictographs    🤠, 🥳, 🧠, 🦾, 🥸
                '|[\x{1FA70}-\x{1FAFF}]' .     // Symbols & Pictographs Extended-A      🩰, 🪢, 🧈, 🪠
                '|[\x{1FB00}-\x{1FBFF}]' .     // Symbols Extended-B
                '|[\x{1F3FB}-\x{1F3FF}]' .     // Skin Tone Modifiers
                '|[\x{2600}-\x{26FF}]' .       // Misc Symbols                          ☀️, ♀️, ♿, ⚠️, ⭐
                '|[\x{2700}-\x{27BF}]' .       // Dingbats                              ✂️, ✈️, ✨, ❤️, ❌
                '|[\x{2B00}-\x{2BFF}]' .       // Misc Symbols & Arrows
                '|[\x{231A}-\x{231B}]' .       // Watch, Hourglass                      ⌛, ⌚, ⏰, 🕛, 🕐
                '|[\x{23E9}-\x{23F3}]' .       // Media controls                        🔀, 🔁, 🔂, 🔼, ⏩
                '|[\x{23F8}-\x{23FA}]' .       // Pause, stop buttons                   ⏸, ⏹, ⏺
                '|[\x{25AA}-\x{25FE}]' .       // Geometric shapes                      🔷, 🔶, 🔺, 🔻,🔹
                '|[\x{2614}-\x{2615}]' .       // Umbrella, hot beverage                ☔, ☕
                '|[\x{FE00}-\x{FE0F}]' .       // Variation Selectors
                '|\x{200D}' .                  // Zero Width Joiner
                '|\x{20E3}' .                  // Combining Enclosing Keycap
                '|[\x{E0020}-\x{E007F}]' .     // Tags (subdivision flags)              🏴󠁧󠁢󠁥󠁮󠁧󠁿, 🏴󠁧󠁢󠁳󠁣󠁴󠁿, 🏴󠁧󠁢󠁷󠁬󠁳󠁿
                ')/u';

            return preg_replace(pattern: $pattern, replacement: $replaceWith, subject: $value);
        }

        return array_map(fn(mixed $item) => is_string($item) || is_array($item) ? $this->replaceEmojiValue(value: $item, replaceWith: $replaceWith) : $item, $value);
    }

    /**
     * Convert value to lowercase, handling arrays recursively
     */
    private function toLowercase(string|array $value): string|array
    {
        if (is_string($value)) {

            return mb_strtolower(string: $value);
        }

        return array_map(fn(mixed $item) => is_string($item) || is_array($item) ? $this->toLowercase(value: $item) : $item, $value);
    }

    /**
     * Convert value to uppercase, handling arrays recursively
     */
    private function toUppercase(string|array $value): string|array
    {
        if (is_string($value)) {

            return mb_strtoupper(string: $value);
        }

        return array_map(fn(mixed $item) => is_string($item) || is_array($item) ? $this->toUppercase(value: $item) : $item, $value);
    }

    /**
     * Collapse consecutive characters in value, handling arrays recursively
     */
    private function collapseValue(string|array $value, string $char): string|array
    {
        if ($char === '') {

            return $value;
        }

        if (is_string($value)) {

            return preg_replace(pattern: '/' . preg_quote($char, '/') . '+/u', replacement: $char, subject: $value);
        }

        return array_map(fn(mixed $item) => is_string($item) || is_array($item) ? $this->collapseValue(value: $item, char: $char) : $item, $value);
    }

    /**
     * Trim characters from value, handling arrays recursively
     */
    private function trimValue(string|array $value, string $char = " \n\r\t\v\0"): string|array
    {
        if (is_string($value)) {

            return mb_trim(string: $value, characters: $char);
        }

        return array_map(fn(mixed $item) => is_string($item) || is_array($item) ? $this->trimValue(value: $item, char: $char) : $item, $value);
    }

    /**
     * Abort when an array value is nested deeper than the allowed limit, since every recursive
     * pipeline walk follows the full depth and an overly deep input would exhaust the PHP stack
     */
    private function validateValue(string|int|float|null|bool|array|object $value): void
    {
        if (!is_array($value)) {

            return;
        }

        $depth = $this->valueDepth(value: $value);

        if ($depth > self::MAX_DEPTH) {

            app(InfiniteLoopException::class)
                ->_252()
                ->with([
                    'depth' => $depth,
                    'value' => $value
                ])
                ->throw();
        }
    }

    /**
     * Compute the deepest array nesting level of the value, stopping as soon as the allowed limit is exceeded
     */
    private function valueDepth(array $value, int $depth = 1): int
    {
        $maxDepth = $depth;

        foreach ($value as $item) {

            if (!is_array($item)) {

                continue;
            }

            if ($depth + 1 > self::MAX_DEPTH) {

                return self::MAX_DEPTH + 1;
            }

            $maxDepth = max($maxDepth, $this->valueDepth(value: $item, depth: $depth + 1));
        }

        return $maxDepth;
    }

    /**
     * Determine whether the current value is worth processing, skipping scalars and empty values that sanitizing would not change
     */
    private function valueIsSanitizable(): bool
    {
        $value = $this->value();

        if (
            (!is_string($value) && !is_array($value)) ||
            $value === '' || $value === []
        ) {
            return false;
        }

        return true;
    }

    /**
     * Remove the given entries from the set so callers can keep specific characters or file names untouched
     */
    private function excludeFromSet(array $set, array $excludes): array
    {
        foreach ($excludes as $exclude) {

            foreach ($set as $key => $value) {

                if ($exclude === $value) {

                    unset($set[$key]);
                    break;
                }
            }
        }

        return $set;
    }
}
