<?php

namespace Fooino\Core\Support;

use Fooino\Core\Exceptions\InfiniteLoopException;

class Sanitizer
{
    private const int MAX_ATTEMPT = 25;

    private array $attempted = [];

    public function __construct(private string|int|float|null|bool|array|object $value) {}

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
    public function normalizeInput(): static
    {
        $value = $this->value();

        $trimmed = is_string($value) ? $this->trimValue(value: $value) : $value;

        $isJson = isJson(value: $value) && !is_numeric($trimmed) && !in_array($trimmed, ['true', 'false', 'null', '{}']);

        if ($isJson) {

            $decoded = jsonDecode(json: $value);

            $value = match (true) {

                in_array(gettype($decoded), ['object', 'array']) => jsonDecodeToArray(json: $value),

                default                                          => $decoded
            };
        }

        if (is_array($value)) {

            array_walk_recursive($value, fn(&$item) => $item = $this->normalizeValue(value: $item));
        }

        if (is_string($value)) {
            $value = $this->normalizeValue(value: $value);
        }

        return $this->setValue(value: ($isJson) ? jsonEncode($value) : $value);
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
                    break;
                }
            }
        }

        return $this->setValue(value: $this->replace(search: $forbiddens, replace: $replaceWith, subject: $value));
    }

    /**
     * Remove or replace sensitive file names and extensions from the value
     */
    public function replaceSensitiveFiles(array $excludes = [], string $replaceWith = ''): static
    {
        $value = $this->value();

        if (
            (!is_string($value) && !is_array($value)) ||
            $value === '' || $value === []
        ) {
            return $this;
        }

        $sensitives = $this->sensitiveFiles();

        foreach ($excludes as $exclude) {

            foreach ($sensitives as $key => $sensitive) {

                if ($exclude === $sensitive) {

                    unset($sensitives[$key]);
                    break;
                }
            }
        }

        return $this->setValue(value: $this->replace(search: $sensitives, replace: $replaceWith, subject: $value));
    }

    /**
     * Remove or replace emoji characters from the value
     */
    public function replaceEmoji(string $replaceWith = ''): static
    {
        $value = $this->value();

        if (
            (!is_string($value) && !is_array($value)) ||
            $value === '' || $value === []
        ) {
            return $this;
        }

        return $this->setValue(value: $this->replaceEmojiValue(value: $value, replaceWith: $replaceWith));
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
    public function trim(string $char = " \n\r\t\v\0"): static
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
     * Default set of sensitive file names and extensions to remove or replace
     */
    private function sensitiveFiles(): array
    {
        $files = [
            'oauth-private.key',
            'oauth-public.key',
            'package-lock.json',
            'package.json',
            'composer.json',
            'composer.lock',
            '.gitlab-ci.yml',
            '.gitlab-ci.yaml',
            '.env.example',
            '.env.testing',
            '.env.local',
            '.env.production',
            '.env.staging',
            '.env.development',
            '.env.encrypted',
            '.env.decrypted',
            '.env.old',
            '.env.backup',
            '.env',
            'index.php',
            'supervisor.log',
            'phpunit.xml',
            'error_log',
            '.gitignore',
            '.gitkeep',
            'laravel.logs',
            'laravel.log',
            'api-docs.json',
            '.editorconfig',
            '.htaccess',
            '.htpasswd',
            '.key',
            '.pem',
            '.crt',
            '.logs',
            '.log',
            '.php',
            '.phtml',
            '.phar',
            '.pht',
            '.php3',
            '.php4',
            '.php5',
            '.php7',
            '.php8',
            '.shtml',
            '.shtm',
            '.git',
            '.sql',
            '.sqlite',
            '.json',
            '.zip',
            '.rar',
            '.js',
            '.css',
            '.html',
            '.xml',
            '.yml',
            '.yaml',
            '.md',
            '.blade',
            '.stub',
            '.bak',
            '.swp',
            '.swo',
            '.old',
            '.orig',
            '.py',
            '.pyc',
            '.bat',
            '.bash',
            '.sh',
            '.exe',
            '.cmd',
            '.asp',
            '.aspx',
            '.jsp',
            '.cgi',
            '.pl',
            '.rb',
            'artisan',
            'Dockerfile',
            'Makefile',
            'Procfile',
            'docker-compose.yml',
            'docker-compose.yaml',
            'next.config.js',
            'next.config.ts',
            'nginx.conf',
            'phpstan.neon',
            'phpstan.neon.dist',
            'phpunit.xml.dist',
            'tailwind.config.js',
            'tailwind.config.ts',
            'vite.config.js',
            'vite.config.ts',
            'yarn.lock',
            '.dockerignore',
            '.gitattributes',
            '.npmrc',
            '.php-cs-fixer.php',
            '.php-cs-fixer.dist.php',
            'makefile'
        ];

        usort($files, fn($a, $b) => strlen($b) <=> strlen($a));

        return $files;
    }

    /**
     * Normalize a scalar value: convert digits, replace Arabic letters,
     * remove half-spaces, strip XSS vectors from allowed tags, and trim
     */
    private function normalizeValue(string|int|float|null|bool|array|object $value): string|int|float|null|bool|array|object
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

        $replaced = strip_tags($replaced, $this->allowedTags());

        $replaced = mb_trim($replaced, " \n\r\t\v\0");

        return $replaced;
    }

    private function allowedTags(): array
    {
        return [
            '<b>',
            '<strong>',
            '<em>',
            '<i>',
            '<u>',
            '<s>',
            '<sub>',
            '<sup>',
            '<p>',
            '<br>',
            '<hr>',
            '<pre>',
            '<code>',
            '<img>',
            '<button>',
            '<div>',
            '<span>',
            '<h1>',
            '<h2>',
            '<h3>',
            '<h4>',
            '<h5>',
            '<h6>',
            '<table>',
            '<caption>',
            '<col>',
            '<colgroup>',
            '<td>',
            '<tr>',
            '<th>',
            '<thead>',
            '<tbody>',
            '<ul>',
            '<ol>',
            '<li>',
            '<dl>',
            '<dt>',
            '<dd>',
            '<blockquote>',
            '<q>',
            '<figure>',
            '<figcaption>',
            '<mark>',
            '<small>',
            '<del>',
            '<ins>',
            '<abbr>',
            '<cite>',
            '<a>',
            '<picture>'
        ];
    }

    /**
     * Replace search strings in the subject, handling arrays recursively.
     */
    private function replace(string|array $search, string|array $replace, string|array $subject): string|array
    {
        if (is_string($subject)) {
            return str_replace(search: $search, replace: $replace, subject: $subject);
        }

        $this->assertRecursionLimit(method: 'replace');

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->replace(search: $search, replace: $replace, subject: $item) : $item, $subject);
    }

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

        $this->assertRecursionLimit(method: 'replaceEmojiValue');

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->replaceEmojiValue(value: $item, replaceWith: $replaceWith) : $item, $value);
    }

    /**
     * Convert value to lowercase, handling arrays recursively
     */
    private function toLowercase(string|array $value): string|array
    {
        if (is_string($value)) {
            return mb_strtolower(string: $value);
        }

        $this->assertRecursionLimit(method: 'toLowercase');

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->toLowercase(value: $item) : $item, $value);
    }

    /**
     * Convert value to uppercase, handling arrays recursively
     */
    private function toUppercase(string|array $value): string|array
    {
        if (is_string($value)) {
            return mb_strtoupper(string: $value);
        }

        $this->assertRecursionLimit(method: 'toUppercase');

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->toUppercase(value: $item) : $item, $value);
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

        $this->assertRecursionLimit(method: 'collapseValue');

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->collapseValue(value: $item, char: $char) : $item, $value);
    }

    /**
     * Trim characters from value, handling arrays recursively
     */
    private function trimValue(string|array $value, string $char = " \n\r\t\v\0"): string|array
    {
        if (is_string($value)) {

            return mb_trim($value, $char);
        }

        $this->assertRecursionLimit(method: 'trimValue');

        return array_map(fn($item) => is_string($item) || is_array($item) ? $this->trimValue(value: $item, char: $char) : $item, $value);
    }

    private function assertRecursionLimit(string $method): void
    {
        $this->attempted[$method] ??= 0;
        $this->attempted[$method] += 1;

        if ($this->attempted[$method] > self::MAX_ATTEMPT) {

            app(InfiniteLoopException::class)
                ->_252()
                ->with([
                    'method'    => $method,
                    'attempted' => $this->attempted[$method],
                    'value'     => $this->value()
                ])
                ->throw();
        }
    }
}
