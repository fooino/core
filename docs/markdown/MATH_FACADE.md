# Math Facade

Arbitrary-precision arithmetic with BC Math, rounding for all common modes, and output truncation by configurable precision — no precision loss in calculations.

## Architecture

```
Mathable (Interface — Fooino\Core\Interfaces\Mathable)
  └── FooinoMathHandler (Concrete — Fooino\Core\Concretes\Math\FooinoMathHandler)
      └── MathManager (Laravel driver manager — Fooino\Core\Concretes\Math\MathManager)
          └── Math (Facade — Fooino\Core\Facades\Math)
```

## Precision & Scale

Two separate concepts:

| Setting | Constant / Property | Purpose |
|---|---|---|
| `BC_SCALE` | `12` (hardcoded) | Used internally by all `bc*` functions during calculation. High enough to avoid precision loss. |
| `precision` | configurable, default `12` | Truncates the **output** for display. Not used in calculations. |

The gap between `BC_SCALE` and `precision` lets you compute with high precision and truncate at the end for locale-specific policies (e.g., `precision=0` for Iran where fractional subunits are worthless, `precision=2` for cent-based currencies).

```php
Math::setPrecision(precision: 0)->number(Math::sum(5.599, 5.499));
// BC_SCALE=12 computes 11.098, then truncated to 11

Math::setPrecision(precision: 2)->number(Math::sum(5.599, 5.499));
// BC_SCALE=12 computes 11.098, then truncated to 11.09
```

Precision must be between `0` and `12` (inclusive). Values outside this range throw `MathCalculationException` (code `1101`).

```php
math(20);  // throws MathCalculationException — precision > BC_SCALE
math(-1);  // throws MathCalculationException — negative precision
```

### Get / set precision

```php
Math::getPrecision();                // 12 (default)

Math::setPrecision(precision: 5)->getPrecision(); // 5
math(precision: 5)->getPrecision();                // 5 — global helper

math()->getPrecision();              // 12 — helper with default precision
```

Repeated calls with the same precision return the same cached instance:

```php
Math::setPrecision(precision: 5) === Math::setPrecision(precision: 5); // true
```

## Basic Arithmetic

All operations accept variadic numbers or a single array. At least 2 operands required.

### Sum

```php
Math::sum(1, 2, 3, 4);                   // '10'
Math::sum([1, 2, 3, 4]);                 // '10' — array also accepted
sum(1, 2, 3, 4);                         // '10' — global helper

Math::sum('1234567891234567889999999.000000000011', '1234567891234567889999999.000000000009');
// '2469135782469135779999998.00000000002'
```

### Subtract

```php
Math::subtract(10, 3, 2);                // '5'
Math::subtract([5.599, 5.499]);          // '0.1'
subtract(5, 6);                          // '-1' — global helper
```

### Multiply

```php
Math::multiply(5, 6, 2);                 // '60'
Math::multiply([5.125, 6.11]);           // '31.31375'
multiply(1, 2, 3, 4);                    // '24' — global helper

Math::multiply('1234567891234567889999999', '1234567891234567889999999');
// '1524157878067367851562259605883269630864220000001'
```

### Divide

```php
Math::divide(10, 3);                     // '3.333333333333'
Math::divide([361, 1.15]);               // '313.91304347826'
divide(10, 2, 5, 2);                     // '0.5' — global helper
```

Division by zero throws `MathCalculationException` (code `1104`):

```php
Math::divide(1, 0);                      // throws MathCalculationException
Math::divide([1, 2, 0]);                 // throws MathCalculationException
```

### Remainder (Modulo)

```php
Math::remainder(13, 5);                  // '3'
Math::remainder([13, -5]);               // '3'
remainder(10, 5, 3);                     // '0' — global helper
```

Modulo by zero throws `MathCalculationException` (code `1104`).

## Power

```php
Math::power(2, 3);                       // '8'
Math::power(2, -2);                      // '0.25'
Math::power(0, 0);                       // '1'
Math::power(0, 2);                       // '0'

Math::power([2, 3, 4], 3);              // ['8', '27', '64'] — array input → array output
Math::power('1234567891234567889999999', 2);
// '1524157878067367851562259605883269630864220000001'
```

`0 ** -1` (zero to a negative exponent) throws `MathCalculationException` (code `1104`).

## Square Root

```php
Math::sqrt(2);                           // '1.414213562373'
Math::sqrt(4);                           // '2'

Math::sqrt([0, 1, 2, 3, 4]);           // ['0', '1', '1.414213562373', '1.732050807568', '2']
```

Square root of a negative number throws `MathCalculationException` (code `1105`):

```php
Math::sqrt(-1);                          // throws MathCalculationException
```

## Rounding

### Round up (ceiling, away from zero)

```php
Math::roundUp(1.1);                      // '2'
Math::roundUp(-1.1);                     // '-1'
Math::roundUp(1.1e-8);                   // '1'

Math::roundUp([0.01, -0.01, 1.1]);       // ['1', '0', '2']
roundUp(1.999099);                       // '2' — global helper
```

### Round down (floor, toward zero)

```php
Math::roundDown(1.1);                    // '1'
Math::roundDown(-1.1);                   // '-2'
Math::roundDown(1.1e-8);                 // '0'

Math::roundDown([0.01, -0.01, 1.1]);     // ['0', '-1', '1']
roundDown(1.999099);                     // '1' — global helper
```

### Round close (configurable mode)

Full control via `RoundingMode` enum (PHP 8.4+):

```php
Math::roundClose(1.5, precision: 0, mode: RoundingMode::HalfAwayFromZero); // '2'
Math::roundClose(-1.5, precision: 0, mode: RoundingMode::HalfAwayFromZero); // '-2'

Math::roundClose(1.005, precision: 2, mode: RoundingMode::HalfAwayFromZero); // '1.01'
Math::roundClose(1.004, precision: 2, mode: RoundingMode::HalfAwayFromZero); // '1'

Math::roundClose([1.1, 1.5, 0.499], precision: 0, mode: RoundingMode::HalfAwayFromZero);
// ['1', '2', '0']

roundClose(1.5, precision: 0, mode: RoundingMode::HalfEven);
// '2' — global helper
```

### Rounding modes reference

| Mode | `1.5` | `2.5` | `-1.5` | `-2.5` | Description |
|---|---|---|---|---|---|
| `HalfAwayFromZero` | `2` | `3` | `-2` | `-3` | Half rounds away from zero (school rounding) |
| `HalfTowardsZero` | `1` | `2` | `-1` | `-2` | Half rounds toward zero |
| `HalfEven` | `2` | `2` | `-2` | `-2` | Half rounds to nearest even (bankers' rounding) |
| `HalfOdd` | `1` | `3` | `-1` | `-3` | Half rounds to nearest odd |
| `TowardsZero` | `1` | `2` | `-1` | `-2` | Always truncate toward zero |
| `AwayFromZero` | `2` | `3` | `-2` | `-3` | Any fraction rounds away |
| `NegativeInfinity` | `1` | `2` | `-2` | `-3` | Round down (floor) |
| `PositiveInfinity` | `2` | `3` | `-1` | `-2` | Round up (ceil) |

All rounding modes support array input → array output, and precision down to the configured `BC_SCALE` (12).

### Integer-boundary carry

All modes correctly handle carry-over at integer boundaries:

```php
Math::roundClose(999.995, precision: 0, mode: RoundingMode::HalfAwayFromZero); // '1000'
Math::roundClose(999.995, precision: 0, mode: RoundingMode::TowardsZero);      // '999'
Math::roundClose(999.995, precision: 0, mode: RoundingMode::HalfEven);         // '1000'
Math::roundClose(998.5,   precision: 0, mode: RoundingMode::HalfEven);         // '998'
```

## Number Formatting

### `number` — truncate, clean, and format

Truncates one or more numbers to the configured precision, removes trailing zeros, and returns clean numeric strings:

```php
Math::number(0.001);                           // '0.001'
Math::number(11.000001000);                    // '11.000001'
Math::number(1.101e-5);                        // '0.00001101'
Math::number(1.1E-20);                         // '0'

Math::setPrecision(precision: 4)->number(0.44015042);
// '0.4401'

Math::number(1, 11.000001000, 0);              // ['1', '11.000001', '0']
Math::number([1, 11.000001000, 0]);            // ['1', '11.000001', '0']

number(0.001);                                  // '0.001' — global helper
number([1, 0.001]);                             // ['1', '0.001']
```

Throws `MathCalculationException` (code `1102`) when empty, and (code `1103`) for non-numeric values:

```php
number();                  // throws MathCalculationException (code 1102)
number('test');            // throws MathCalculationException (code 1103)
```

### `numberFormat` — thousands separators

Formats a number with thousands separators and precision truncation:

```php
Math::numberFormat(5000000);                         // '5,000,000'
Math::numberFormat(5000000.50);                      // '5,000,000.5'
Math::numberFormat(5000000.0150100);                 // '5,000,000.01501'

Math::numberFormat(5000000.0150100, thousandsSeparator: '|');
// '5|000|000.01501'

Math::numberFormat('5,000,000.0150100', thousandsSeparator: ' ');
// '5 000 000.01501'

// Negative numbers with same separator as thousands:
Math::numberFormat('-5-000-000.0150100', thousandsSeparator: '-');
// '-5-000-000.01501'

numberFormat(5000000.50);                            // '5,000,000.5' — global helper
```

Handles leading signs and removes existing separators before formatting:

```php
Math::numberFormat('+5,000,000.0150100', thousandsSeparator: ' ');
// '5 000 000.01501'
```

## Comparison

All comparison methods use `bccomp` with `BC_SCALE` for accurate decimal comparison:

```php
Math::greaterThan(1.112, 1.11);           // true
Math::greaterThan(1.11, 1.112);           // false

Math::greaterThanOrEqual(1.112, 1.112);   // true
Math::greaterThanOrEqual(1.11, 1.112);    // false

Math::lessThan(1.11, 1.112);              // true
Math::lessThan(1.112, 1.11);              // false

Math::lessThanOrEqual(1.11, 1.112);       // true
Math::lessThanOrEqual(1.112, 1.112);      // true

Math::equal(1.112, 1.112);               // true
Math::equal(1.11, 1.112);                // false

Math::notEqual(1.11, 1.112);             // true
Math::notEqual(1.112, 1.112);            // false
```

Global helpers:

```php
greaterThan(1.112, 1.11);                 // true
greaterThanOrEqual(1.112, 1.112);         // true
lessThan(1.11, 1.112);                    // true
lessThanOrEqual(1.112, 1.112);            // true
equal(1.112, 1.112);                      // true
notEqual(1.11, 1.112);                    // true
```

Non-numeric operands throw `MathCalculationException` (code `1103`):

```php
greaterThan(1, 'test');                   // throws MathCalculationException
```

## Utilities

### Convert scientific notation

Expands numbers like `1.5E+4` into their full decimal string representation:

```php
Math::convertScientificNumber(1.1e+8);     // '110000000'
Math::convertScientificNumber('1.1e-8');   // '0.000000011'
Math::convertScientificNumber(1e8);        // '100000000'
Math::convertScientificNumber(312.12E-2);  // '3.1212'

// Non-scientific strings pass through unchanged:
Math::convertScientificNumber('foobar');   // 'foobar'
Math::convertScientificNumber('');         // ''
```

Very large exponents (`abs(exponent) > 99`) throw `MathCalculationException` (code `1105`):

```php
Math::convertScientificNumber('1.1E+9999');  // throws MathCalculationException
Math::convertScientificNumber('1.1E-324');   // throws MathCalculationException
```

INF and -INF also throw (code `1105`).

### Trim trailing zeros

Removes trailing zeros after the decimal point:

```php
Math::trimTrailingZeros(11.001100);           // '11.0011'
Math::trimTrailingZeros('1100.001100');       // '1100.0011'
Math::trimTrailingZeros(1100.);               // '1100'
Math::trimTrailingZeros(-11.000001000);       // '-11.000001'

// Non-numeric strings pass through:
Math::trimTrailingZeros('test');              // 'test'
```

### Count decimal places

```php
Math::countDecimalPlaces(11);                // 0
Math::countDecimalPlaces(11.01);             // 2
Math::countDecimalPlaces(0.000000000100);    // 10
Math::countDecimalPlaces(1.1e-8);            // 9
Math::countDecimalPlaces('test');            // 0 — non-numeric → 0
```

## Error Handling

All math exceptions extend `FooinoException` with fluent `with()` context for logging:

```php
try {

    Math::sum(1, 'test');

} catch (MathCalculationException $e) {

    $e->getMessage();    // 'msg.mathCalculationExceptionInvalidArgumentType'
    $e->getCode();       // 1103
    $e->getLevel();      // 'error'
    $e->getWith();       // ['method' => 'bcadd', 'operand' => [1, 'test'], 'args' => []]
}
```

### Error code reference

| Code | Level | Method | Description |
|---|---|---|---|
| `1101` | `critical` | constructor | Precision out of valid range (`0` – `12`) |
| `1102` | `error` | `number`, `sum`, `subtract`, `multiply`, `divide`, `remainder`, `power`, `sqrt`, `roundUp`, `roundDown`, `roundClose` | Insufficient operands |
| `1103` | `error` | all methods that accept numeric input | Non-numeric operand provided |
| `1104` | `critical` | `divide`, `remainder`, `power` | Division / modulo by zero |
| `1105` | `critical` | `convertScientificNumber`, `sqrt` | Value out of allowed range (INF, exponent > 99, negative sqrt) |
| `1106` | `critical` | internal | Unrecognised bcmath function (should not occur in normal use) |

### Exception context

Every exception carries `method`, `operand`, and `args` in `getWith()` for debugging:

```php
try {

    Math::divide(1, 0);

} catch (MathCalculationException $e) {

    $e->getWith();
    // ['method' => 'bcdiv', 'operand' => [1, 0], 'args' => []]
}
```
