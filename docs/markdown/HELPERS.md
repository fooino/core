# Helpers

## unwrapBackedEnum

Normalize a value to its primitive form by extracting the scalar value from BackedEnum instances.

```php
unwrapBackedEnum(value: WrappedBackedEnum::ACTIVE); // 'ACTIVE'
unwrapBackedEnum(value: 'some string');              // 'some string'
```
