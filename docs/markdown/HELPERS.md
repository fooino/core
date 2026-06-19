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

## removeSpace

Strip all whitespace characters from strings, for cleaning user input and formatted text.

```php
removeSpace(value: ' 0912 123 1234 ');                  // '09121231234'
removeSpace(value: ' 0912 123 1234 ', replace: '_');    // '_0912_123_1234_'
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

## perPage

Resolve and validate the per-page value from the request, falling back to the `FOOINO_PER_PAGE` constant.

```php
perPage();                                      // FOOINO_PER_PAGE (when no 'per_page' in request)
perPage(key: 'per_page', maxPerPage: 100);      // 100 or FOOINO_PER_PAGE if exceeded
perPage(request: $customRequest);               // resolve from a specific request instance
```
