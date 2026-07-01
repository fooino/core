# TokenGenerator

Generate unique tokens for various purposes — OTP codes, passwords, UUIDs, and more. Configurable through a fluent API with built-in database uniqueness checking.

---

## Basic Usage

```php
use Fooino\Core\Support\TokenGenerator;

app(TokenGenerator::class)->value();                    // '58371' (default: numeric, length 5)
app(TokenGenerator::class)->length(8)->numeric()->value();      // '26498135'
app(TokenGenerator::class)->length(16)->alphaNumeric()->value(); // 'aH3kF9mX2pQ7vB1n'
```

---

## Formats

Each format sets the character set and generation algorithm for the token.

| Method | Characters | Length default | Notes |
|---|---|---|---|
| `numeric()` | `0-9` | 5 | First digit is never `0` |
| `alphaNumeric()` | `0-9`, `a-z`, `A-Z` | 5 | |
| `alphabet()` | `a-z`, `A-Z` | 5 | |
| `weakPassword()` | `0-9` | 5 | First digit is never `0` (same guarantee as `numeric`) |
| `password()` | `0-9`, `a-z`, `A-Z` | 5 | Minimum length: 8 |
| `strongPassword()` | `0-9`, `a-z`, `A-Z`, symbols | 5 | Minimum length: 12 |
| `uuid4()` | UUID v4 format | 36 (fixed) | Length setting is ignored — always 36 chars |
| `uuid7()` | UUID v7 format (time-ordered) | 36 (fixed) | Length setting is ignored — always 36 chars |
| `memorableOtp()` | `0-9` with adjacent pair | 5 | First digit is never `0`. Guarantees at least one pair of adjacent identical digits (e.g. `247719`). Minimum length: 2 |

```php
app(TokenGenerator::class)->numeric()->value();              // '82514'
app(TokenGenerator::class)->alphaNumeric()->value();         // 'kR8x2'
app(TokenGenerator::class)->alphabet()->value();             // 'aBxYq'
app(TokenGenerator::class)->weakPassword()->value();         // '39017'
app(TokenGenerator::class)->password()->length(12)->value(); // 'kR8x2mN9pQ3v'
app(TokenGenerator::class)->strongPassword()->length(12)->value(); // 'kR8x!mN9pQ3v'
app(TokenGenerator::class)->uuid4()->value();                // '550e8400-e29b-41d4-a716-446655440000'
app(TokenGenerator::class)->uuid7()->value();                // '018f3a6e-1b3c-7d45-a123-456789abcdef'
app(TokenGenerator::class)->memorableOtp()->length(6)->value(); // '247719'
```

---

## Configuration

### length(int $length)

Set the token length. Must be between 1 and 255 (inclusive). Format-specific minimums apply.

```php
app(TokenGenerator::class)->length(8)->numeric()->value();    // 8 digits
app(TokenGenerator::class)->length(255)->numeric()->value();  // maximum
```

### model(string|Model $model)

Set the Eloquent model class for database uniqueness checking. Accepts a class name string or a Model instance.

```php
app(TokenGenerator::class)->model(User::class)->field('code')->numeric()->length(6)->value();
```

### field(string $field)

Set the database column name to check for token uniqueness. Required when `model` is set.

```php
app(TokenGenerator::class)->model(User::class)->field('referral_code')->value();
```

### where(array $where)

Set additional where conditions for the uniqueness query. Accepts a flat array `['col', '=', 'val']` or an array of arrays `[['col1', '=', 'val1'], ['col2', '>', 'val2']]`.

```php
app(TokenGenerator::class)
    ->model(Coupon::class)
    ->field('code')
    ->where(['status', 'ACTIVE'])
    ->alphanumeric()
    ->length(8)
    ->value();
```

---

## Pipeline Transformations

Apply `lowercase()` or `uppercase()` after token generation. Transformations are applied sequentially, so `lowercase()->uppercase()` results in uppercase.

```php
app(TokenGenerator::class)->alphaNumeric()->length(16)->lowercase()->value(); // all lowercase
app(TokenGenerator::class)->alphaNumeric()->length(16)->uppercase()->value(); // all uppercase
```

---

## Database Uniqueness

When `model` and `field` are configured, the generator checks the database for existing tokens and retries until a unique one is found. If all possible values in the current space are exhausted, an `InfiniteLoopException` is thrown after 100 attempts.

The attempt counter is reset after each successful `value()` call, so the same instance can be reused for multiple generations.

---

## Exception Reference

| code | Exception | Condition |
|---|---|---|
| 1201 | `TokenGeneratorException` | Length < 1 |
| 1202 | `TokenGeneratorException` | Length > 255 |
| 1203 | `TokenGeneratorException` | `strongPassword` length < 12 |
| 1204 | `TokenGeneratorException` | `password` length < 8 |
| 1205 | `TokenGeneratorException` | `model` set but `field` is empty |
| 1206 | `TokenGeneratorException` | `memorableOtp` length < 2 |
| 253 | `InfiniteLoopException` | Uniqueness retry exhausted (100 attempts) |

```php
use Fooino\Core\Exceptions\TokenGeneratorException;
use Fooino\Core\Exceptions\InfiniteLoopException;

// Length too large
app(TokenGenerator::class)->length(256)->value(); // throws TokenGeneratorException (1202)

// Password too short
app(TokenGenerator::class)->password()->length(5)->value(); // throws TokenGeneratorException (1204)

// All tokens taken (uniqueness space exhausted)
app(TokenGenerator::class)->model(User::class)->field('code')->numeric()->length(1)->value();
// throws InfiniteLoopException (253) after 100 retries
```

---

## Instance Reuse

The same instance can be used for multiple generations. The attempt counter is automatically reset after each successful `value()` call.

```php
$gen = app(TokenGenerator::class)->numeric()->length(8);
$first = $gen->value();   // '49173582'
$second = $gen->value();  // '62048139' — fresh retry budget
```
