<?php

use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Exceptions\FooinoRuntimeException;
use Fooino\Core\Exceptions\TransactionRollBackedException;

use Fooino\Core\Facades\Date;
use Fooino\Core\Facades\Json;
use Fooino\Core\Facades\Math;

use Fooino\Core\Interfaces\Mathable;
use Fooino\Core\Support\Sanitizer;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

if (!defined('FOOINO_CORE_CONSTANTS_DEFINED')) {

    define('STANDARD_DATE_TIME_FORMAT', 'Y-m-d H:i:s');
    define('STANDARD_DATE_FORMAT', 'Y-m-d');

    define('FOOINO_PER_PAGE', 30);
    define('FOOINO_MAX_PER_PAGE', 300);

    define('FOOINO_PRIORITY_STEP', 10_000);

    define('FOOINO_CORE_CONSTANTS_DEFINED', true);
}

if (!function_exists('isJson')) {
    /**
     * Determine whether a string is valid JSON
     */
    function isJson(int|float|string|null|bool|array|object $value): bool
    {
        return Json::is(value: $value);
    }
}

if (!function_exists('jsonEncode')) {
    /**
     * Serialize a value to a JSON string, passing through values that are already valid JSON
     */
    function jsonEncode(int|float|string|null|bool|array|object $value, int $flags = 0, int $depth = 512): string|false
    {
        return Json::encode(value: $value, flags: $flags, depth: $depth);
    }
}

if (!function_exists('jsonEncodePretty')) {
    /**
     * Serialize a value to a human-readable JSON string with HTML-safe escaping for display
     */
    function jsonEncodePretty(string|array $value): string
    {
        return Json::encodePretty(value: $value);
    }
}

if (!function_exists('jsonDecode')) {
    /**
     * Convert a JSON string back to its original PHP value
     */
    function jsonDecode(int|float|string|null|bool|array|object $json, bool|null $associative = null, int $depth = 512, int $flags = 0): int|float|string|null|bool|array|object
    {
        return Json::decode(json: $json, associative: $associative, depth: $depth, flags: $flags);
    }
}

if (!function_exists('jsonDecodeToArray')) {
    /**
     * Convert a JSON string to an associative array
     */
    function jsonDecodeToArray(int|float|string|null|bool|array|object $json): array
    {
        return Json::decodeToArray(json: $json);
    }
}

if (!function_exists('jsonRespond')) {
    /**
     * Build a JSON HTTP response with a standardized structure for API responses
     */
    function jsonRespond(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [], array $headers = [], int $options = 0): JsonResponse
    {
        return Json::respond(status: $status, message: $message, errors: $errors, data: $data, additional: $additional, headers: $headers, options: $options);
    }
}

if (!function_exists('dateConvert')) {
    /**
     * Convert a date between timezones and calendar systems (Gregorian, Jalali, Hijri)
     *
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     */
    function dateConvert(string|int|null $date, string $format = STANDARD_DATE_TIME_FORMAT, DateTimeZone|string $from = 'UTC', DateTimeZone|string $to = 'UTC', string $fallback = '', bool $throwException = false): string
    {
        return Date::convert(date: $date, format: $format, from: $from, to: $to, fallback: $fallback, throwException: $throwException);
    }
}

if (!function_exists('datesBetween')) {
    /**
     * Generate an array of dates within a given period(from, to) at specified intervals and format.
     *
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     *
     * @throws \Fooino\Core\Exceptions\FooinoRuntimeException
     *
     * @throws \Fooino\Core\Exceptions\InfiniteLoopException
     */
    function datesBetween(string|int $from, string|int $to, string $format = STANDARD_DATE_FORMAT, string $interval = 'P1D'): array
    {
        return Date::datesBetween(from: $from, to: $to, format: $format, interval: $interval);
    }
}

if (!function_exists('math')) {
    /**
     * Get a fresh Mathable instance configured with the given precision
     */
    function math(int $precision = 12): Mathable
    {
        return Math::setPrecision(precision: $precision);
    }
}

if (!function_exists('number')) {
    /**
     * Format one or more numbers by truncating them to the configured precision, removing trailing zeros, and returning clean numeric strings
     */
    function number(string|int|float|array ...$number): string|array
    {
        return Math::number(...$number);
    }
}

if (!function_exists('numberFormat')) {
    /**
     * Format a number with thousands separators and apply precision truncation, returning a locale-friendly currency-style string
     */
    function numberFormat(string|int|float $number, string $thousandsSeparator = ','): string
    {
        return Math::numberFormat(number: $number, thousandsSeparator: $thousandsSeparator);
    }
}

if (!function_exists('sum')) {
    /**
     * Add a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    function sum(string|int|float|array ...$operand): string
    {
        return Math::sum(...$operand);
    }
}

if (!function_exists('subtract')) {
    /**
     * Subtract a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function subtract(string|int|float|array ...$operand): string
    {
        return Math::subtract(...$operand);
    }
}

if (!function_exists('multiply')) {
    /**
     * Multiply a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    function multiply(string|int|float|array ...$operand): string
    {
        return Math::multiply(...$operand);
    }
}

if (!function_exists('divide')) {
    /**
     * Divide a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function divide(string|int|float|array ...$operand): string
    {
        return Math::divide(...$operand);
    }
}

if (!function_exists('remainder')) {
    /**
     * Compute the modulus (remainder) of a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function remainder(string|int|float|array ...$operand): string
    {
        return Math::remainder(...$operand);
    }
}

if (!function_exists('roundUp')) {
    /**
     * Round a number up to the next integer (ceiling), away from zero
     */
    function roundUp(string|int|float|array $number): string|array
    {
        return Math::roundUp(number: $number);
    }
}

if (!function_exists('roundDown')) {
    /**
     * Round a number down to the previous integer (floor), toward zero
     */
    function roundDown(string|int|float|array $number): string|array
    {
        return Math::roundDown(number: $number);
    }
}

if (!function_exists('roundClose')) {
    /**
     * Round a number to a specified precision using a configurable rounding mode
     */
    function roundClose(string|int|float|array $number, int $precision = 0, RoundingMode $mode = RoundingMode::HalfAwayFromZero): string|array
    {
        return Math::roundClose(number: $number, precision: $precision, mode: $mode);
    }
}

if (!function_exists('greaterThan')) {
    /**
     * Check if the first number is strictly greater than the second using arbitrary precision comparison
     */
    function greaterThan(string|int|float $num1, string|int|float $num2): bool
    {
        return Math::greaterThan(num1: $num1, num2: $num2);
    }
}

if (!function_exists('greaterThanOrEqual')) {
    /**
     * Check if the first number is greater than or equal to the second using arbitrary precision comparison
     */
    function greaterThanOrEqual(string|int|float $num1, string|int|float $num2): bool
    {
        return Math::greaterThanOrEqual(num1: $num1, num2: $num2);
    }
}

if (!function_exists('lessThan')) {
    /**
     * Check if the first number is strictly less than the second using arbitrary precision comparison
     */
    function lessThan(string|int|float $num1, string|int|float $num2): bool
    {
        return Math::lessThan(num1: $num1, num2: $num2);
    }
}

if (!function_exists('lessThanOrEqual')) {
    /**
     * Check if the first number is less than or equal to the second using arbitrary precision comparison
     */
    function lessThanOrEqual(string|int|float $num1, string|int|float $num2): bool
    {
        return Math::lessThanOrEqual(num1: $num1, num2: $num2);
    }
}

if (!function_exists('equal')) {
    /**
     * Check if two numbers are exactly equal using arbitrary precision comparison
     */
    function equal(string|int|float $num1, string|int|float $num2): bool
    {
        return Math::equal(num1: $num1, num2: $num2);
    }
}

if (!function_exists('notEqual')) {
    /**
     * Check if two numbers differ from each other using arbitrary precision comparison
     */
    function notEqual(string|int|float $num1, string|int|float $num2): bool
    {
        return Math::notEqual(num1: $num1, num2: $num2);
    }
}

if (!function_exists('isZero')) {
    /**
     * Check the value is zero or not
     */
    function isZero(int|float|string|null|bool|array|object|callable $value): bool
    {
        $value = (is_object($value) && $value instanceof Stringable) ? $value->__toString() : $value;

        return is_numeric($value) && ((float) $value) === 0.0;
    }
}

if (!function_exists('nullIfBlank')) {
    /**
     * Returns a fallback value when the input is considered "blank" or a null-like string which usually produced by js.
     */
    function nullIfBlank(int|float|string|null|bool|array|object|callable $value, int|float|string|null|bool|array|object|callable $fallback = null): int|float|string|null|bool|array|object|callable
    {
        return ((blank($value) || (is_string($value) && trim(str_replace([" ", "\n", "\t", "'", "`", '"', "null", "undefined", "nan"], '', strtolower($value))) === '')) ? null : $value) ?? $fallback;
    }
}

if (!function_exists('nullIfBlankOrZero')) {
    /**
     * Returns a fallback value when the input is considered "blank" or a null-like string or 0.
     */
    function nullIfBlankOrZero(int|float|string|null|bool|array|object|callable $value, int|float|string|null|bool|array|object|callable $fallback = null): int|float|string|null|bool|array|object|callable
    {
        $value = nullIfBlank(value: $value);

        return (isZero(value: $value) ? null : $value) ?? $fallback;
    }
}

if (!function_exists('nullIfBlankInput')) {
    /**
     * Retrieve an input value from the request and return null if it is blank
     */
    function nullIfBlankInput(string $key, int|float|string|null|bool|array|object|callable $fallback = null, Request|null $request = null): int|float|string|null|bool|array|object|callable
    {
        $request ??= request();

        return nullIfBlank(value: $request->input($key), fallback: $fallback);
    }
}

if (!function_exists('nullIfBlankOrZeroInput')) {
    /**
     * Retrieve an input value from the request and return null if it is blank or zero
     */
    function nullIfBlankOrZeroInput(string $key, int|float|string|null|bool|array|object|callable $fallback = null, Request|null $request = null): int|float|string|null|bool|array|object|callable
    {
        $request ??= request();

        return nullIfBlankOrZero(value: $request->input($key), fallback: $fallback);
    }
}

if (!function_exists('unwrapBackedEnum')) {
    /**
     * Normalize a value to its primitive form by extracting the scalar value from BackedEnum instances
     */
    function unwrapBackedEnum(int|float|string|null|bool|array|object $value): int|float|string|null|bool|array|object
    {
        return ($value instanceof \BackedEnum) ? $value->value : $value;
    }
}

if (!function_exists('mergeArraysByKey')) {
    /**
     * Aggregate values from multiple arrays into grouped sub-arrays keyed by their original keys
     */
    function mergeArraysByKey(array ...$arrays): array
    {
        $merged = [];

        foreach ($arrays as $array) {

            foreach ($array as $key => $value) {

                if (!array_key_exists(key: $key, array: $merged)) {

                    $merged[$key] = [];
                }

                if (is_array(value: $value)) {

                    $merged[$key] = array_merge($merged[$key], $value);

                    //
                } else {

                    $merged[$key][] = $value;

                    //
                }
            }
        }

        return $merged;
    }
}

if (!function_exists('removeComma')) {
    /**
     * Normalize strings by stripping commas, for sanitizing numeric input and text from external sources
     */
    function removeComma(int|float|string|null|bool|array $value, string $replace = ''): int|float|string|null|bool|array
    {
        if (is_string($value)) {
            return str_replace(',', $replace, $value);
        }

        if (is_array($value)) {

            $result = [];

            foreach ($value as $key => $item) {
                $result[$key] = is_string($item) ? str_replace(',', $replace, $item) : $item;
            }

            return $result;
        }

        return $value;
    }
}

if (!function_exists('removeWhitespace')) {
    /**
     * Strip all whitespace characters (spaces, newlines, tabs) from strings, for cleaning user input and formatted text
     */
    function removeWhitespace(int|float|string|null|bool|array $value, string $replace = ''): int|float|string|null|bool|array
    {
        if (is_string($value)) {
            return str_replace([' ', "\n", "\t"], $replace, $value);
        }

        if (is_array($value)) {

            $result = [];

            foreach ($value as $key => $item) {
                $result[$key] = is_string($item) ? str_replace([' ', "\n", "\t"], $replace, $item) : $item;
            }

            return $result;
        }

        return $value;
    }
}

if (!function_exists('sanitizeNumber')) {
    /**
     * Remove spaces and commas from strings, for sanitizing phone numbers and numeric input
     */
    function sanitizeNumber(int|float|string|null|bool|array $value): int|float|string|null|bool|array
    {
        return removeWhitespace(value: removeComma(value: $value));
    }
}

if (!function_exists('replaceSlashWithDash')) {
    /**
     * Normalize date strings by converting slashes to dashes
     */
    function replaceSlashWithDash(int|float|string|null|bool|array $value): int|float|string|null|bool|array
    {
        if (is_string($value)) {
            return str_replace('/', '-', $value);
        }

        if (is_array($value)) {

            $result = [];

            foreach ($value as $key => $item) {
                $result[$key] = is_string($item) ? str_replace('/', '-', $item) : $item;
            }

            return $result;
        }

        return $value;
    }
}

if (!function_exists('setUserTimezone')) {
    /**
     * Store the user timezone in the config so it can be retrieved later
     */
    function setUserTimezone(string $timezone): void
    {
        config(['user-timezone' => $timezone]);
    }
}

if (!function_exists('getUserTimezone')) {
    /**
     * Retrieve the user timezone from config, falling back to UTC
     */
    function getUserTimezone(): string
    {
        return (config(key: 'user-timezone', default: 'UTC')) ?: 'UTC';
    }
}

if (!function_exists('setDefaultLocale')) {
    /**
     * Setter for 'app.locale' config
     */
    function setDefaultLocale(string $locale): void
    {
        config(['app.locale' => $locale]);
    }
}

if (!function_exists('getDefaultLocale')) {
    /**
     * Getter for 'app.locale' config
     */
    function getDefaultLocale(): string
    {
        return (config(key: 'app.locale', default: 'fa')) ?: 'fa';
    }
}

if (!function_exists('perPage')) {
    /**
     * Resolve the per-page value from the request with validation against a maximum
     */
    function perPage(string $key = 'per_page', int $maxPerPage = FOOINO_MAX_PER_PAGE, Request|null $request = null): int
    {
        $request ??= request();

        $perPage = $request->input($key);

        if (is_null($perPage) || !is_numeric($perPage) || $perPage <= 0) {
            return FOOINO_PER_PAGE;
        }

        return min((int) $perPage, $maxPerPage);
    }
}

if (!function_exists('currentDate')) {
    /**
     * Return date in 'Y-m-d' format
     */
    function currentDate(): string
    {
        return date(STANDARD_DATE_FORMAT);
    }
}

if (!function_exists('currentDateTime')) {
    /**
     * Return date in 'Y-m-d H:i:s' format
     */
    function currentDateTime(): string
    {
        return date(STANDARD_DATE_TIME_FORMAT);
    }
}

if (!function_exists('currentDateTs')) {
    /**
     * Get the current date as a Unix timestamp
     */
    function currentDateTs(): int
    {
        return strtotime(currentDate());
    }
}

if (!function_exists('currentDateTimeTs')) {
    /**
     * Get the current datetime as a Unix timestamp
     */
    function currentDateTimeTs(): int
    {
        return strtotime(currentDateTime());
    }
}

if (!function_exists('strToDate')) {
    /**
     * Convert a date string to the standard date format (Y-m-d)
     * The helper use php strtotime function to parse $str
     *
     * @throws \Fooino\Core\Exceptions\FooinoRuntimeException with code 3
     */
    function strToDate(string $str): string
    {
        $timestamp = strtotime($str);

        if ($timestamp === false) {

            app(FooinoRuntimeException::class)
                ->_3()
                ->with([
                    'method'    => 'strToDate',
                    'input'     => $str
                ])
                ->throw();
        }

        return date(STANDARD_DATE_FORMAT, $timestamp);
    }
}

if (!function_exists('strToDateTime')) {
    /**
     * Convert a date string to the standard datetime format (Y-m-d H:i:s)
     * The helper use php strtotime function to parse $str
     *
     * @throws \Fooino\Core\Exceptions\FooinoRuntimeException with code 3
     */
    function strToDateTime(string $str): string
    {
        $timestamp = strtotime($str);

        if ($timestamp === false) {

            app(FooinoRuntimeException::class)
                ->_3()
                ->with([
                    'method'    => 'strToDateTime',
                    'input'     => $str
                ])
                ->throw();
        }

        return date(STANDARD_DATE_TIME_FORMAT, $timestamp);
    }
}

if (!function_exists('callMethodIfExists')) {
    /**
     * Safely call a method on an object or class if it exists, otherwise return a fallback value.
     */
    function callMethodIfExists(object|string $object, string $method, mixed $fallback = null, array $methodArgs = [], array $constructorArgs = []): mixed
    {
        return method_exists($object, $method) ? (is_string($object) ? (new $object(...$constructorArgs)) : $object)->{$method}(...$methodArgs) : value($fallback, ...$methodArgs);
    }
}

if (!function_exists('percentageChange')) {
    /**
     * Calculate the relative percentage change from $from to $to.
     */
    function percentageChange(string|int|float $from, string|int|float $to, int $precision = 2): string
    {
        return match (true) {

            isZero($from) && isZero($to)    => '0',

            isZero($from)                   => '100',

            isZero($to)                     => '-100',

            default                         => math(precision: $precision)->number(multiply(divide(subtract($to, $from), abs($from)), 100))
        };
    }
}

if (!function_exists('unitNumberFormat')) {
    /**
     * Format a number with a unit and abbreviate large numbers (thousands, millions, billions, trillions)
     */
    function unitNumberFormat(string|int|float $number, string $unit = '', int $precision = 3): string
    {
        $threshold = match (true) {
            greaterThanOrEqual(abs($number), '1000000000000') => '1000000000000',
            greaterThanOrEqual(abs($number), '1000000000')    => '1000000000',
            greaterThanOrEqual(abs($number), '1000000')       => '1000000',
            greaterThanOrEqual(abs($number), '1000')          => '1000',
            default                                           => null,
        };

        $unitKey = match ($threshold) {
            '1000000000000' => 'msg.trillion',
            '1000000000'    => 'msg.billion',
            '1000000'       => 'msg.million',
            '1000'          => 'msg.thousand',
            default         => null,
        };

        if ($threshold === null) {
            return trim(math(precision: $precision)->numberFormat($number) . ' ' . $unit);
        }

        $divided = divide($number, $threshold);

        $count = equal(abs($divided), '1') ? 1 : 2;

        return trim(
            math(precision: $precision)->numberFormat($divided)
                . ' '
                . __($unitKey, ['count' => $count])
                . ' '
                . $unit
        );
    }
}

if (!function_exists('unitSizeFormat')) {
    /**
     * Format bytes into a human-readable file size string (Bytes, KB, MB, GB, TB). the method followd Binary standard but show the unit in SI standarad
     */
    function unitSizeFormat(string|int|float $bytes, int $precision = 3): string
    {
        return match (true) {

            greaterThanOrEqual($bytes, '1099511627776')   => math(precision: $precision)->numberFormat(divide($bytes, '1099511627776')) . ' TB',

            greaterThanOrEqual($bytes, '1073741824')      => math(precision: $precision)->numberFormat(divide($bytes, '1073741824')) . ' GB',

            greaterThanOrEqual($bytes, '1048576')         => math(precision: $precision)->numberFormat(divide($bytes, '1048576')) . ' MB',

            greaterThanOrEqual($bytes, '1024')            => math(precision: $precision)->numberFormat(divide($bytes, '1024')) . ' KB',

            greaterThan($bytes, '1')                      => $bytes . ' Bytes',

            greaterThanOrEqual($bytes, '0')               => $bytes . ' Byte',

            default                                       => $bytes . ' ' . __('msg.isInvalid'),
        };
    }
}

if (!function_exists('sanitizer')) {
    /**
     * Create a new Sanitizer instance for the given value
     */
    function sanitizer(string|int|float|null|bool|array|object $value): Sanitizer
    {
        return new Sanitizer(value: $value);
    }
}

if (!function_exists('normalizeInput')) {
    /**
     * Normalize the input by converting Persian/Arabic digits and letters,
     * removing zero-width non-joiners, stripping XSS vectors, and trimming whitespace
     */
    function normalizeInput(string|int|float|null|bool|array|object $value): string|int|float|null|bool|array|object
    {
        return sanitizer(value: $value)->normalizeInput()->value();
    }
}

if (!function_exists('sanitizeUrl')) {
    /**
     * Clean a value for use in URLs base on RFC 3986 by replacing forbidden characters with dashes
     */
    function sanitizeUrl(string|int|float|null|bool|array $value): string|int|float|null|bool|array
    {
        return sanitizer(value: $value)
            ->replaceEmoji(replaceWith: '-')
            ->replaceForbiddenCharacters(excludes: ['/', '=', ':', '?', '&', '.', '-', '#', '@', '~', '%', '+', ' ', '../', '_'], replaceWith: '-')
            ->collapse(char: '-')
            ->value();
    }
}

if (!function_exists('sanitizeSlug')) {
    /**
     * Generate a URL-friendly slug from the given value
     */
    function sanitizeSlug(string|int|float|null|bool|array $value): string|int|float|null|bool|array
    {
        $result = sanitizer(value: $value)
            ->replaceEmoji(replaceWith: '-')
            ->replaceForbiddenCharacters(excludes: ['-'], replaceWith: '-')
            ->collapse(char: '-')
            ->trim(char: '-')
            ->lowercase()
            ->value();

        if (is_string($result)) {

            return str()->slug($result);
        }

        if (is_array($result)) {

            array_walk_recursive($result, fn(&$item) => $item = is_string($item) ? str()->slug($item) : $item);
        }

        return $result;
    }
}

if (!function_exists('jsonAttribute')) {
    /**
     * Cast an Eloquent attribute to/from JSON automatically
     */
    function jsonAttribute(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !is_null(nullIfBlank($value)) ? jsonDecodeToArray($value) : [],
            set: fn($value) => !is_null(nullIfBlank($value)) ? jsonEncode($value)        : null,
        );
    }
}

if (!function_exists('resolveRequest')) {
    /**
     * Resolve and validate a FormRequest with the given data and optional authenticated user
     */
    function resolveRequest(string $request, array $data = [], User|null $user = null): FormRequest
    {
        $req = new $request();

        $req->merge($data);

        if (!is_null($user)) {
            $req->setUserResolver(fn() => $user);
        }

        $container = app();

        $req
            ->setContainer($container)
            ->setRedirector($container->make('redirect'))
            ->validateResolved();

        return $req;
    }
}

if (!function_exists('dbTransaction')) {
    /**
     * Execute a callback within a database transaction, rethrowing any exception as a TransactionRollBackedException
     */
    function dbTransaction(callable $callback): mixed
    {
        try {
            DB::beginTransaction();

            $result = $callback();

            DB::commit();

            return $result;

            //
        } catch (FooinoException | Exception $e) {

            DB::rollBack();

            app(TransactionRollBackedException::class)
                ->cause($e)
                ->setMessage($e->getMessage())
                ->setCode($e->getCode())
                ->setLevel(callMethodIfExists(object: $e, method: 'getLevel', fallback: 'error'))
                ->report(callMethodIfExists(object: $e, method: 'reportable', fallback: true))
                ->setHttpStatusCode(callMethodIfExists(object: $e, method: 'getHttpStatusCode', fallback: 500))
                ->with(callMethodIfExists(object: $e, method: 'getWith', fallback: []))
                ->throw();
        }
    }
}


if (!function_exists('userInfo')) {
    /**
     * Extract user information from a loaded polymorphic relationship
     */
    function userInfo(Model $model, string $relation): array
    {

        if (!$model->relationLoaded($relation)) {

            return [
                'id'                    => 0,
                'country_id'            => 0,
                'full_name'             => '',
                'country_code'          => '',
                'phone_number'          => '',
                'phone_number_original' => '',
                'type'                  => __(key: 'msg.unknown'),
            ];
        }

        $user = $model?->{$relation};

        if (is_null($user)) {
            return [
                'id'                    => 0,
                'country_id'            => 0,
                'full_name'             => '',
                'country_code'          => '',
                'phone_number'          => '',
                'phone_number_original' => '',
                'type'                  => __(key: 'msg.unknown'),
            ];
        }

        return [
            'id'                        => (float) ($user?->id ?? 0),

            'country_id'                => (float) ($user?->country_id ?? 0),

            'full_name'                 => (string) ($user?->full_name ?? $user?->name ?? trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''))),

            'country_code'              => (string) ($user?->country_code ?? ''),

            'phone_number'              => (string) ($user?->phone_number ?? ''),

            'phone_number_original'     => (string) ($user?->getRawOriginal('phone_number', '') ?? ''),

            'type'                      => callMethodIfExists(object: $user, method: 'objectName', fallback: [])['type'] ?? __(key: 'msg.unknown'),
        ];
    }
}

if (!function_exists('getUserable')) {
    /**
     * Resolve the authenticated user into polymorphic relation columns (e.g. creatorable_type, creatorable_id)
     */
    function getUserable(string $able, Request|Model|null $user = null, string $guard = 'web', bool $throwException = false): array
    {
        $user = match (true) {

            ($user instanceof Request)  => $user->user(guard: $guard),

            ($user instanceof Model)    => $user,

            default                     => request()->user(guard: $guard)
        };

        $id = $user?->id ?? null;

        if ($throwException && (blank($user) || blank($id))) {
            throw new Exception('The user is empty');
        }

        return [
            ($able . '_type') => (filled($user) && filled($id)) ? get_class($user) : null,
            ($able . '_id')   => $id,
        ];
    }
}
