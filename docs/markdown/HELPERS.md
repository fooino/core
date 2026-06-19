# Helpers

## unwrapBackedEnum

Normalize a value to its primitive form by extracting the scalar value from BackedEnum instances.

```php
unwrapBackedEnum(value: WrappedBackedEnum::ACTIVE);     // 'ACTIVE'
unwrapBackedEnum(value: 'some string');                 // 'some string'
```
## mergeArraysByKey

Aggregate values from multiple arrays into grouped sub-arrays keyed by their original keys.

```php
mergeArraysByKey(['a' => [1]], ['a' => 2, 'b' => 3]);
// ['a' => [1, 2], 'b' => [3]]

mergeArraysByKey(['x' => ['k' => 1]], ['x' => ['k' => 2]]);
// ['x' => ['k' => 2]]
// (sub-array keys are overridden via array_merge)
```
