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
