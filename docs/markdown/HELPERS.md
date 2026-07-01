# Fooino Core Helpers

## unwrapBackedEnum

Normalize a value to its primitive form by extracting the scalar value from BackedEnum instances.

```php
unwrapBackedEnum(value: WrappedBackedEnum::ACTIVE);     // 'ACTIVE'
unwrapBackedEnum(value: 'some string');                 // 'some string'
```

## mergeArraysByKey

Aggregate values from multiple arrays into grouped sub-arrays keyed by their original keys.

```php
mergeArraysByKey(['a' => [1]], ['a' => 2, 'b' => 3]);       // ['a' => [1, 2], 'b' => [3]]

mergeArraysByKey(['x' => ['k' => 1]], ['x' => ['k' => 2]]); // ['x' => ['k' => 2]] (sub-array keys are overridden via array_merge)
```

## removeComma

Normalize strings by stripping commas, for sanitizing numeric input and text from external sources.

```php
removeComma(value: '12,345,678');           // '12345678'
removeComma(value: '1,2,3', replace: '.');  // '1.2.3'
```

## removeWhitespace

Strip all whitespace characters from strings, for cleaning user input and formatted text.

```php
removeWhitespace(value: ' 0912 123 1234 ');                  // '09121231234'
removeWhitespace(value: ' 0912 123 1234 ', replace: '_');    // '_0912_123_1234_'
removeWhitespace(value: "foo\nbar\tbaz");                     // 'foobarbaz'
```

## sanitizeNumber

Remove spaces and commas from strings, for sanitizing phone numbers and numeric input.

```php
sanitizeNumber(value: '+98 912 111 2222 '); // '+989121112222'
sanitizeNumber(value: ' 1,222 333,444');    // '1222333444'
```

## replaceSlashWithDash

Normalize date strings by converting slashes to dashes.

```php
replaceSlashWithDash(value: '2023/01/02');                  // '2023-01-02'
replaceSlashWithDash(value: 'a//b');                        // 'a--b'
replaceSlashWithDash(value: ['2023/01/02', 'hi/hello']);    // ['2023-01-02', 'hi-hello']
```

## setUserTimezone / getUserTimezone

Store and retrieve the user timezone in the config, falling back to UTC.

```php
setUserTimezone(timezone: 'Asia/Tehran');
getUserTimezone(); // 'Asia/Tehran'

config(['user-timezone' => null]);
getUserTimezone(); // 'UTC'
```

## setDefaultLocale / getDefaultLocale

Override and retrieve the application locale for the current request, falling back to 'fa'.

```php
setDefaultLocale(locale: 'fa');
getDefaultLocale(); // 'fa'

config(['app.locale' => null]);
getDefaultLocale(); // 'fa'
```

## perPage

Resolve and validate the per-page value from the request, falling back to the `FOOINO_PER_PAGE` constant.

```php
perPage();                                      // FOOINO_PER_PAGE (when no 'per_page' in request)
perPage(key: 'per_page', maxPerPage: 100);      // less than 100 or 100 if exceeded
perPage(request: $customRequest);               // resolve from a specific request instance
```

## currentDate

Get today's date in ISO date format for consistent date storage.

```php
currentDate(); // '2026-06-19'
```

## currentDateTime

Get the current timestamp in MySQL-compatible datetime format.

```php
currentDateTime(); // '2026-06-19 16:33:58'
```

## currentDateTs

Get the current date as a Unix timestamp.

```php
currentDateTs(); // 1768262400
```

## currentDateTimeTs

Get the current datetime as a Unix timestamp.

```php
currentDateTimeTs(); // 1768262400
```

## strToDate

Convert a date string to the standard date format (Y-m-d). When the input cannot be parsed by PHP's `strtotime`, a `FooinoRuntimeException` is thrown with code `3`.

```php
strToDate(str: '2026-06-27');                 // '2026-06-27'
strToDate(str: '2026-06-27 14:30:00');        // '2026-06-27'
strToDate(str: 'next monday');                // '2026-06-29' (depends on current date)
strToDate(str: 'not a date');                 // throws FooinoRuntimeException (code 3)
```

## strToDateTime

Convert a date string to the standard datetime format (Y-m-d H:i:s). When the input cannot be parsed by PHP's `strtotime`, a `FooinoRuntimeException` is thrown with code `3`.

```php
strToDateTime(str: '2026-06-27');             // '2026-06-27 00:00:00'
strToDateTime(str: '2026-06-27 14:30:00');    // '2026-06-27 14:30:00'
strToDateTime(str: 'next monday');            // '2026-06-29 00:00:00' (depends on current date)
strToDateTime(str: 'not a date');             // throws FooinoRuntimeException (code 3)
```

## callMethodIfExists

Safely call a method on an object or class if it exists, otherwise return a fallback value.

```php
callMethodIfExists(object: new CustomClass, method: 'pi', fallback: 'fooino');                                  // 3.14
callMethodIfExists(object: CustomClass::class, method: 'getPrecision', constructorArgs: ['precision' => 2]);    // 2
callMethodIfExists(object: new CustomClass, method: 'nonexistent', fallback: 'default');                        // 'default'
```

## isZero

Check whether a value is numerically zero, supporting numeric strings and Stringable objects.

```php
isZero(value: 0);           // true
isZero(value: 0.0);         // true
isZero(value: '0');         // true
isZero(value: '0.0E+10');   // true
isZero(value: 'foobar');    // false
isZero(value: null);        // false
isZero(value: []);          // false
isZero(value: new stdClass);// false
```

## nullIfBlank

Cast JavaScript falsy/null-like values (null, undefined, NaN, empty strings with quote chars, whitespace-only) to null for consistent server-side validation.

```php
nullIfBlank(value: null);                       // null
nullIfBlank(value: 'null');                     // null
nullIfBlank(value: ' NaN ');                    // null
nullIfBlank(value: 'undefined');                // null
nullIfBlank(value: "nu\tll");                   // null
nullIfBlank(value: '');                         // null
nullIfBlank(value: 'foobar');                   // 'foobar'
nullIfBlank(value: 0, fallback: 'foobar');      // 0
```

## nullIfBlankOrZero

Return null (or a fallback) when the value is blank or numerically zero. Combines `nullIfBlank` and `isZero`.

```php
nullIfBlankOrZero(value: 0);                         // null
nullIfBlankOrZero(value: '0');                       // null
nullIfBlankOrZero(value: 0.0);                       // null
nullIfBlankOrZero(value: '-0.0');                    // null
nullIfBlankOrZero(value: 5);                         // 5
nullIfBlankOrZero(value: 0.0000001);                 // 0.0000001
nullIfBlankOrZero(value: '');                        // null
nullIfBlankOrZero(value: 'null');                    // null
nullIfBlankOrZero(value: 'foobar');                  // 'foobar'
nullIfBlankOrZero(value: 0, fallback: 'fooino');     // 'fooino'
nullIfBlankOrZero(value: '', fallback: 'fooino');    // 'fooino'
```

## nullIfBlankInput

Retrieve a request input and return null if it is blank or a JS null-like value.

```php
nullIfBlankInput(key: 'title');                                     // null when 'title' is missing or blank
nullIfBlankInput(key: 'title', fallback: 'default');                // 'default' when blank
nullIfBlankInput(key: 'title', request: $customRequest);            // resolve from a specific request instance
```

## nullIfBlankOrZeroInput

Retrieve a request input and return null if it is blank or zero. The request-aware variant of `nullIfBlankOrZero`.

```php
nullIfBlankOrZeroInput(key: 'title');                                      // null when missing, blank, or '0'
nullIfBlankOrZeroInput(key: 'title', fallback: 'fallback');                // 'fallback' when blank or zero
nullIfBlankOrZeroInput(key: 'title', request: $customRequest);             // resolve from a specific request instance
```

## percentageChange

Calculate the relative percentage change from `$from` to `$to`, handling division-by-zero when the base value is zero.

```php
percentageChange(from: 200, to: 50);                // '-75'   (50% of 200 = 100, remaining 50)
percentageChange(from: 20, to: 40);                 // '100'   (doubled)
percentageChange(from: 0, to: 15);                  // '100'   (had none → now has some)
percentageChange(from: 15, to: 0);                  // '-100'  (had some → now has none)
percentageChange(from: 0, to: 0);                   // '0'     (no change)
percentageChange(from: 13, to: 14);                 // '7.69'  (truncated to 2 decimal places)
percentageChange(from: 13, to: 14, precision: 12);  // '7.6923076923'
```

## unitNumberFormat

Format a number with a unit and abbreviate large numbers (thousands, millions, billions, trillions). Supports Laravel pluralization via the `count` parameter.

define translations as

```php
  'trillion' => '[1] Trillion|[2,*] Trillions',
  'billion'  => '[1] Billion|[2,*] Billions',
  'million'  => '[1] Million|[2,*] Millions',
  'thousand' => '[1] Thousand|[2,*] Thousands',
```

in `en/msg.php` file.

```php
unitNumberFormat(number: 1_000_000, unit: 'USD Dollar');              // '1 Million USD Dollar'
unitNumberFormat(number: 5_000_000, unit: '$');                       // '5 Millions $'
unitNumberFormat(number: -123_076_012, unit: '$');                    // '-123.076 Millions $'
unitNumberFormat(number: 1.e20, unit: 'Persons');                     // '100,000,000 Trillions Persons'
unitNumberFormat(number: 2501, unit: 'Persons');                      // '2.501 Thousands Persons'
unitNumberFormat(number: 0.011, unit: 'seconds');                     // '0.011 seconds'
unitNumberFormat(number: 0, unit: 'seconds');                         // '0 seconds'
unitNumberFormat(number: 123_076_012, unit: '$', precision: 5);       // '123.07601 Millions $'
```

## unitSizeFormat

Format bytes into a human-readable file size string using binary units (1 KB = 1024 B).

```php
unitSizeFormat(bytes: 1649267441664);                // '1.5 TB'
unitSizeFormat(bytes: 1610612736);                   // '1.5 GB'
unitSizeFormat(bytes: 1572864);                      // '1.5 MB'
unitSizeFormat(bytes: 1536);                         // '1.5 KB'
unitSizeFormat(bytes: 500);                          // '500 Bytes'
unitSizeFormat(bytes: 1);                            // '1 byte'
unitSizeFormat(bytes: 0);                            // '0 byte'
unitSizeFormat(bytes: -10);                          // '-10 msg.isInvalid'
unitSizeFormat(bytes: 1234567);                      // '1.177 MB'
unitSizeFormat(bytes: 1234567, precision: 5);        // '1.17737 MB'
```

## sanitizeUrl

Normalize a value for use in URLs by replacing forbidden characters with dashes. Emoji are replaced, then a defined set of characters are replaced with `-`. Consecutive dashes are collapsed.

Characters that are **preserved** (excluded from replacement) per RFC 3986 URL syntax:
`/` `=` `:` `?` `&` `.` `-` `#` `@` `~` `%` `+` `whitespace` `../` `_`

The helper does **not** validate the input — if the result is not a valid URL, downstream validation rules will reject it.

```php
sanitizeUrl(value: 'https://example.com/search?q=hello&page=1');     // unchanged  (all chars excluded)
sanitizeUrl(value: 'https://example.com/laravel_tips!for-2025');     // 'https://example.com/laravel_tips-for-2025'
sanitizeUrl(value: 'https://example.com/page;param=value');          // 'https://example.com/page-param=value'
sanitizeUrl(value: "https://example.com/(test)");                    // 'https://example.com/-test-'
sanitizeUrl(value: 'https://example.com/api/v1/../v2');              // unchanged  (../ excluded)
sanitizeUrl(value: 'https://x.com/test 🎉');                         // 'https://x.com/test -'
sanitizeUrl(value: ['https://a.com/b_c', 'https://a.com/d/e']);     // ['https://a.com/b_c', 'https://a.com/d/e']
```

## sanitizeSlug

Generate a URL-friendly slug from the given value. Emoji are replaced, forbidden characters are replaced with `-`, consecutive dashes are collapsed, leading/trailing dashes are trimmed, the result is lowercased, then passed through `Str::slug()` for transliteration of non-ASCII characters.

Only `-` is excluded from replacement — all other forbidden characters (spaces, punctuation, special chars) become `-`.

```php
sanitizeSlug(value: 'Laravel_tips!for-2025');            // 'laravel-tips-for-2025'
sanitizeSlug(value: 'café');                             // 'cafe'          (transliterated)
sanitizeSlug(value: 'straße');                           // 'strasse'       (transliterated)
sanitizeSlug(value: 'über cool');                        // 'ueber-cool'    (transliterated)
sanitizeSlug(value: 'hello 🎉 world');                   // 'hello-world'   (emoji removed)
sanitizeSlug(value: ['café', 'hello world']);            // ['cafe', 'hello-world']
sanitizeSlug(value: 1);                                  // 1               (non-string: passthrough)
```

## jsonAttribute

Cast an Eloquent attribute to/from JSON automatically. Designed for storing arrays as JSON in the database — the getter always returns an array (via `jsonDecodeToArray`) and the setter encodes via `jsonEncode`, which passes through already-valid JSON strings.

Blank values (`null`, `''`, `[]`) are stored as `null` in the database and read back as `[]`.

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

public function metadata(): Attribute
{
    return jsonAttribute();
}

// Storing:
$model->metadata = ['key' => 'value'];
$model->metadata = [0, false, null];
$model->metadata = '{"a":1}';        // pass-through: already-valid JSON string
$model->metadata = [];               // stored as null, read back as []

// Retrieving (always array):
$model->metadata; // ['key' => 'value']
$model->metadata; // []
```

## resolveRequest

Resolve and validate a Laravel `FormRequest` with the given data and optional authenticated user. Useful in tests to prepare a validated request instance and pass its output to commands, actions, or tasks.

The helper creates the form request, merges the data, sets the container and redirector, calls `validateResolved()` (which triggers `prepareForValidation` and authorization), and returns the validated instance.

```php
use App\Http\Requests\StoreArticleRequest;

$request = resolveRequest(
    request: StoreArticleRequest::class,
    data: ['title' => 'My Article', 'body' => 'Content'],
    user: $user,
);

$request->validated();   // ['title' => 'My Article', 'body' => 'Content']
$request->safe();        // ValidatedInput instance
$request->user();        // the authenticated user passed to the helper
```

When validation fails, a `ValidationException` is thrown:

```php
resolveRequest(request: StoreArticleRequest::class);
// throws ValidationException when required fields are missing
```

When `authorize()` returns `false`, an `AuthorizationException` is thrown:

```php
resolveRequest(request: ProtectedRequest::class, data: [...]);
// throws AuthorizationException when access is denied
```

Fields not present in the request's `rules()` are silently stripped from the `validated()` output, as per standard Laravel behaviour.

```php
resolveRequest(
    request: StoreArticleRequest::class,
    data: ['title' => 'OK', 'body' => 'OK', 'injected' => 'will be stripped'],
)->validated();
// ['title' => 'OK', 'body' => 'OK']   — 'injected' is not in rules
```
