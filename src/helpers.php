<?php

use Fooino\Core\Exceptions\FooinoException;
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

if (!defined('CONSTANTS_DEFINED')) {

    define('FOOINO_PER_PAGE', 30);
    define('FOOINO_PRIORITY_STEP', 1000);

    define('FOOINO_IMAGE_EXTENSION', ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp']);
    define('FOOINO_VIDEO_EXTENSION', ['mp4']);
    define('FOOINO_EXCEL_EXTENSION', ['xlsx', 'xls']);
    define('FOOINO_IMAGE_AND_VIDEO_EXTENSION', [...FOOINO_IMAGE_EXTENSION, ...FOOINO_VIDEO_EXTENSION]);

    define('FOOINO_VERY_LOW_TTL_TIME', (60 * 5)); // 5 minutes
    define('FOOINO_LOW_TTL_TIME', (60 * 60)); // 1 hour
    define('FOOINO_MEDIUM_TTL_TIME', (60 * 60 * 24)); // 1 day
    define('FOOINO_HIGH_TTL_TIME', (60 * 60 * 24 * 7)); // 1 week
    define('FOOINO_VERY_HIGH_TTL_TIME', (60 * 60 * 24 * 30)); // 1 month

    define('FOOINO_CACHE_KEY', [
        'ACTIVE_LANGUAGES'  => 'fooino:languages:active',
        'MODELS'            => 'fooino:models',
        'ALL_COUNTRIES'     => 'fooino:countries:all',
        'ACTIVE_COUNTRIES'  => 'fooino:countries:active',
    ]);

    define('CONSTANTS_DEFINED', true);
}

if (!function_exists('isJson')) {
    /**
     * Validate a value is json or not.
     */
    function isJson(int|float|string|null|bool|array|object $value): bool
    {
        return Json::is(value: $value);
    }
}

if (!function_exists('jsonEncode')) {
    /**
     * Encode a value to json format.
     */
    function jsonEncode(int|float|string|null|bool|array|object $value, int $flags = 0, int $depth = 512): string|false
    {
        return Json::encode(value: $value, flags: $flags, depth: $depth);
    }
}

if (!function_exists('jsonEncodePrettified')) {
    /**
     * Encode a value to json format for showing purpose.
     */
    function jsonEncodePrettified(string|array $value): string
    {
        return Json::encodePrettified(value: $value);
    }
}

if (!function_exists('jsonDecode')) {
    /**
     * Decode a json to value.
     */
    function jsonDecode(int|float|string|null|bool|array|object $json, bool|null $associative = null, int $depth = 512, int $flags = 0): mixed
    {
        return Json::decode(json: $json, associative: $associative, depth: $depth, flags: $flags);
    }
}

if (!function_exists('jsonDecodeToArray')) {
    /**
     * Decode a json to array.
     */
    function jsonDecodeToArray(int|float|string|null|bool|array|object $json): array
    {
        return Json::decodeToArray(json: $json);
    }
}

if (!function_exists('jsonResponse')) {
    /**
     * Return response to user.
     */
    function jsonResponse(int $status = 200, string $message = '', array $errors = [], array $data = [], array $additional = [], array $headers = [], int $options = 0): JsonResponse
    {
        return Json::response(status: $status, message: $message, errors: $errors, data: $data, additional: $additional, headers: $headers, options: $options);
    }
}

if (!function_exists('dateConvert')) {
    /**
     * Convert date base on timezone and the format you desire.
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     */
    function dateConvert(string|int|null $date, string $format = 'Y-m-d H:i:s', DateTimeZone|string $from = 'UTC', DateTimeZone|string $to = 'UTC', string $fallback = '', bool $throwException = false): string
    {
        return Date::convert(date: $date, format: $format, from: $from, to: $to, fallback: $fallback, throwException: $throwException);
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
    function number(mixed ...$number): string|array
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
        return Math::numberFormat(number: $number, thousandsSeparator: $thousandsSeparator,);
    }
}

if (!function_exists('sum')) {
    /**
     * Add a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    function sum(mixed ...$operand): string
    {
        return Math::sum(...$operand);
    }
}

if (!function_exists('subtract')) {
    /**
     * Subtract a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function subtract(mixed ...$operand): string
    {
        return Math::subtract(...$operand);
    }
}

if (!function_exists('multiply')) {
    /**
     * Multiply a series of numbers (or an array of numbers) together using arbitrary precision arithmetic
     */
    function multiply(mixed ...$operand): string
    {
        return Math::multiply(...$operand);
    }
}

if (!function_exists('divide')) {
    /**
     * Divide a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function divide(mixed ...$operand): string
    {
        return Math::divide(...$operand);
    }
}

if (!function_exists('remainder')) {
    /**
     * Compute the modulus (remainder) of a series of numbers (or an array of numbers) sequentially using arbitrary precision arithmetic
     */
    function remainder(mixed ...$operand): string
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
     * Compare two numbers
     */
    function greaterThan(string|int|float $a, string|int|float $b): bool
    {
        return Math::greaterThan(a: $a, b: $b);
    }
}

if (!function_exists('greaterThanOrEqual')) {
    /**
     * Compare two numbers
     */
    function greaterThanOrEqual(string|int|float $a, string|int|float $b): bool
    {
        return Math::greaterThanOrEqual(a: $a, b: $b);
    }
}

if (!function_exists('lessThan')) {
    /**
     * Compare two numbers
     */
    function lessThan(string|int|float $a, string|int|float $b): bool
    {
        return Math::lessThan(a: $a, b: $b);
    }
}

if (!function_exists('lessThanOrEqual')) {
    /**
     * Compare two numbers
     */
    function lessThanOrEqual(string|int|float $a, string|int|float $b): bool
    {
        return Math::lessThanOrEqual(a: $a, b: $b);
    }
}

if (!function_exists('equal')) {
    /**
     * Compare two numbers
     */
    function equal(string|int|float $a, string|int|float $b): bool
    {
        return Math::equal(a: $a, b: $b);
    }
}

if (!function_exists('notEqual')) {
    /**
     * Compare two numbers
     */
    function notEqual(string|int|float $a, string|int|float $b): bool
    {
        return Math::notEqual(a: $a, b: $b);
    }
}

if (!function_exists('isZero')) {
    /**
     * Check the value is zero or not
     */
    function isZero(int|float|string|null|bool|array|object|callable $value): bool
    {
        $value = (is_object($value) && $value instanceof Stringable) ? $value->__toString() : $value;

        if (
            is_null($value) ||
            is_bool($value) ||
            is_array($value) ||
            is_object($value) ||
            $value instanceof Closure
        ) {
            return false;
        }

        return preg_match(pattern: '/^[+-]?(?:0+\.?0*|\.0+|(?:0+\.?0*|\.0*)[Ee][+-]?\d+)$/', subject: trim((string) $value)) === 1;
    }
}

if (!function_exists('nullIfBlank')) {
    /**
     * Returns a fallback value when the input is considered "blank" or a null-like string which usually produce by js.
     */
    function nullIfBlank(int|float|string|null|bool|array|object|callable $value, int|float|string|null|bool|array|object|callable $fallback = null): int|float|string|null|bool|array|object|callable
    {
        return ((blank($value) || (is_string($value) && trim(str_replace(["'", "`", '"', "null", "undefined", "nan"], '', strtolower($value))) === '')) ? null : $value) ?? $fallback;
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

if (!function_exists('removeSpace')) {
    /**
     * Strip all whitespace characters from strings, for cleaning user input and formatted text
     */
    function removeSpace(int|float|string|null|bool|array $value, string $replace = ''): int|float|string|null|bool|array
    {
        if (is_string($value)) {
            return str_replace(' ', $replace, $value);
        }

        if (is_array($value)) {

            $result = [];

            foreach ($value as $key => $item) {
                $result[$key] = is_string($item) ? str_replace(' ', $replace, $item) : $item;
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
        return removeSpace(value: removeComma(value: $value));
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
        return (config('user-timezone', 'UTC')) ?: 'UTC';
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
        return (config('app.locale', 'fa')) ?: 'fa';
    }
}

if (!function_exists('perPage')) {
    /**
     * Resolve the per-page value from the request with validation against a maximum
     */
    function perPage(string $key = 'per_page', int $maxPerPage = 300, Request|null $request = null): int
    {
        $request ??= request();

        $perPage = $request->input($key);

        return (is_null($perPage) || !is_numeric($perPage) || $perPage <= 0 || $perPage > $maxPerPage) ? FOOINO_PER_PAGE : $perPage;
    }
}

if (!function_exists('currentDate')) {
    /**
     * Return date in 'Y-m-d' format
     */
    function currentDate(): string
    {
        return \date('Y-m-d');
    }
}

if (!function_exists('currentDateTime')) {
    /**
     * Return date in 'Y-m-d H:i:s' format
     */
    function currentDateTime(): string
    {
        return \date('Y-m-d H:i:s');
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
    function percentageChange(
        int|float $from,
        int|float $to,
        int $precision = 2
    ): string {

        return match (true) {

            isZero($from)   => '100',

            isZero($to)     => '-100',

            default         => math(precision: $precision)
                ->number(
                    multiply(
                        divide(
                            subtract($to, $from),
                            abs($from)
                        ),
                        100
                    )
                )
        };
    }
}

if (!function_exists('unitNumberFormat')) {
    /**
     * Format a number with a unit and abbreviate large numbers (thousands, millions, billions, trillions)
     */
    function unitNumberFormat(int|float|string $number, string $unit = '', int $precision = 3): string
    {
        return match (true) {

            greaterThanOrEqual(abs($number), '1000000000000')      => trim(math(precision: $precision)->numberFormat(divide($number, '1000000000000')) . ' ' . __('msg.trillion') . ' ' . $unit),

            greaterThanOrEqual(abs($number), '1000000000')         => trim(math(precision: $precision)->numberFormat(divide($number, '1000000000')) . ' ' . __('msg.billion') . ' ' . $unit),

            greaterThanOrEqual(abs($number), '1000000')            => trim(math(precision: $precision)->numberFormat(divide($number, '1000000')) . ' ' . __('msg.million') . ' ' . $unit),

            greaterThanOrEqual(abs($number), '1000')               => trim(math(precision: $precision)->numberFormat(divide($number, '1000')) . ' ' . __('msg.thousand') . ' ' . $unit),

            default                                                => trim(math(precision: $precision)->numberFormat($number) . ' ' . $unit),
        };
    }
}

if (!function_exists('unitSizeFormat')) {
    /**
     * Format bytes into a human-readable file size string (B, KB, MB, GB, TB)
     */
    function unitSizeFormat(int|float|string $bytes, int $precision = 3): string
    {
        return match (true) {

            greaterThanOrEqual($bytes, '1099511627776')   => math(precision: $precision)->numberFormat(divide($bytes, '1099511627776')) . ' TB',

            greaterThanOrEqual($bytes, '1073741824')      => math(precision: $precision)->numberFormat(divide($bytes, '1073741824')) . ' GB',

            greaterThanOrEqual($bytes, '1048576')         => math(precision: $precision)->numberFormat(divide($bytes, '1048576')) . ' MB',

            greaterThanOrEqual($bytes, '1024')            => math(precision: $precision)->numberFormat(divide($bytes, '1024')) . ' KB',

            greaterThan($bytes, '1')                      => $bytes . ' bytes',

            greaterThanOrEqual($bytes, '0')               => $bytes . ' byte',

            default                                       => $bytes . ' ' . __('msg.isInvalid'),
        };
    }
}

if (!function_exists('datesBetween')) {
    /**
     * Generate an array of dates within a given range at specified intervals and format.
     * 
     * @throws \Fooino\Core\Exceptions\CanNotConvertDateException
     * 
     * @throws \Fooino\Core\Exceptions\FooinoException
     */
    function datesBetween(
        string|int $from,
        string|int $to,
        string $format = 'Y-m-d',
        string $interval = 'P1D'
    ): array {

        $originalFrom = $from;
        $originalTo = $to;

        $from = dateConvert(date: $from, throwException: true);
        $to = dateConvert(date: $to, throwException: true);

        if ($to < $from) {

            app(FooinoException::class)
                ->_1001()
                ->with([
                    'from'      => $originalFrom,
                    'to'        => $originalTo,
                    'format'    => $format,
                    'interval'  => $interval,
                ])
                ->throw();
        }

        $output = [];
        $utc = new DateTimeZone(timezone: 'UTC');

        $period = new DatePeriod(
            start: new DateTime(datetime: $from, timezone: $utc),
            interval: new DateInterval(duration: $interval),
            end: new DateTime(datetime: $to, timezone: $utc),
            options: DatePeriod::INCLUDE_END_DATE
        );

        foreach ($period as $value) {
            $output[] = $value->format($format);
        }

        return $output;
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
     * Clean a value for use in URLs by replacing forbidden characters with dashes
     */
    function sanitizeUrl(string|int|float|null|bool|array $value): string|int|float|null|bool|array
    {
        return sanitizer(value: $value)
            ->replaceForbiddenCharacters(excludes: ['/', '=', ':', '?', '&', '.', '-', '#', '@', '~', '%', '+'], replaceWith: '-')
            ->collapse('-')
            ->value();
    }
}

if (!function_exists('sanitizeSlug')) {
    /**
     * Generate a URL-friendly slug from the given value
     */
    function sanitizeSlug(string|int|float|null|bool|array $value): string|int|float|null|bool|array
    {
        return sanitizer(value: $value)
            ->replaceForbiddenCharacters(excludes: ['-'], replaceWith: '-')
            ->collapse(char: '-')
            ->trim(char: '-')
            ->lowercase()
            ->value();
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
