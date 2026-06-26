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
