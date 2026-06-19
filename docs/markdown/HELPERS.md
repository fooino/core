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
