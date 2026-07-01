# Exception Reference

All exceptions extend `FooinoException` which extends PHP's `Exception`. The base class provides a fluent builder API for consistent error handling across the ecosystem. For more information, see the [FooinoException documentation](./FOOINO_EXCEPTION.md).


## Error Code Ranges

| Range | Exception Class | Domain |
|---|---|---|
| 1–249 | `FooinoRuntimeException` | General runtime errors |
| 250–499 | `InfiniteLoopException` | Infinite loop / recursion protection |
| 1000–1099 | `CanNotConvertDateException` | Date conversion |
| 1100–1199 | `MathCalculationException` | Math calculation |
| 1200–1299 | `TokenGeneratorException` | Token generation |

---

## `FooinoRuntimeException` (codes 1–249)

Default message key: `msg.fooinoRunTimeException`

| Code | Method | Message Key | Level | HTTP Status | Logged |
|---|---|---|---|---|---|
| 1 | Default | `msg.fooinoRunTimeException` | `warning` | 500 | Yes |
| 2 | `_2()` | `msg.fooinoRunTimeExceptionInvalidPeriodForDatesBetween` | `warning` | 500 | Yes |
| 3 | `_3()` | `msg.fooinoRunTimeExceptionInvalidDateString` | `error` | 500 | Yes |
| 4 | `_4()` | `msg.fooinoRunTimeExceptionCannotUnserializeSingleton` | `critical` | 500 | Yes |
| 5 | `_5()` | `msg.fooinoRunTimeExceptionCannotCloneSingleton` | `critical` | 500 | Yes |

**Usage:**

- **Code 2** — Thrown by `DateHandler::throwInvalidPeriodForDatesBetweenException()` when the `to` date is before the `from` date in `datesBetween()`.
- **Code 3** — Thrown by `strToDate()` and `strToDateTime()` helpers when `strtotime()` fails to parse the input string.
- **Code 4** — Thrown by `SingletonableTask::__wakeup()` when code attempts to unserialize a singleton instance.
- **Code 5** — Thrown by `SingletonableTask::__clone()` when code attempts to clone a singleton instance.

---

## `InfiniteLoopException` (codes 250–499)

Default message key: `msg.infiniteLoopException`

| Code | Method | Message Key | Level | HTTP Status | Logged |
|---|---|---|---|---|---|
| 250 | Default | `msg.infiniteLoopException` | `critical` | 500 | Yes |
| 251 | `_251()` | `msg.infiniteLoopExceptionInvalidIntervalForDatesBetween` | `critical` | 500 | Yes |
| 252 | `_252()` | `msg.infiniteLoopExceptionSanitizerRecursionLimit` | `critical` | 500 | Yes |
| 253 | `_253()` | `msg.infiniteLoopExceptionInTokenGenerator` | `critical` | 500 | Yes |

**Usage:**

- **Code 251** — Thrown by `DateHandler::throwInvalidIntervalForDatesBetweenException()` when the `DateInterval` has all zero properties (would iterate infinitely).
- **Code 252** — Thrown by `Sanitizer::assertRecursionLimit()` when a recursive sanitizer operation exceeds the maximum nesting depth of 25.
- **Code 253** — Thrown by `TokenGenerator::throwInfiniteLoopException()` when the uniqueness retry limit of 100 is exhausted.

---

## `CanNotConvertDateException` (codes 1000–1099)

Default message key: `msg.canNotConvertDateException`

| Code | Method | Message Key | Level | HTTP Status | Logged |
|---|---|---|---|---|---|
| 1000 | Default | `msg.canNotConvertDateException` | `warning` | 500 | Yes |
| 1001 | `_1001()` | `msg.canNotConvertDateExceptionInvalidTimezone` | `error` | 500 | Yes |
| 1002 | `_1002()` | `msg.canNotConvertDateExceptionDateIsEmpty` | `warning` | 500 | **No** |
| 1003 | `_1003()` | `msg.canNotConvertDateExceptionInvalidDate` | `error` | 500 | Yes |
| 1004 | `_1004()` | `msg.canNotConvertDateExceptionInvalidDefaultTimezone` | `emergency` | 500 | Yes |

**Usage:**

- **Code 1001** — Thrown by `DateHandler::throwInvalidTimezoneException()` when a user-supplied timezone string is not a valid PHP timezone identifier.
- **Code 1002** — Thrown by `DateHandler::throwDateIsEmptyException()` when an empty date is provided and `throwException` is `true`. This error is **not logged** by default.
- **Code 1003** — Thrown by `DateHandler::throwInvalidDateException()` when no component of the date string could be parsed into a valid value.
- **Code 1004** — Thrown by `DateHandler::throwInvalidDefaultTimezoneException()` when the system default timezone is not UTC. This is the most severe date error (`emergency` level).

---

## `MathCalculationException` (codes 1100–1199)

Default message key: `msg.mathCalculationException`

| Code | Method | Message Key | Level | HTTP Status | Logged |
|---|---|---|---|---|---|
| 1100 | Default | `msg.mathCalculationException` | `error` | 500 | Yes |
| 1101 | `_1101()` | `msg.mathCalculationExceptionInvalidPrecision` | `critical` | 500 | Yes |
| 1102 | `_1102()` | `msg.mathCalculationExceptionInvalidArgumentsCount` | `error` | 500 | Yes |
| 1103 | `_1103()` | `msg.mathCalculationExceptionInvalidArgumentType` | `error` | 500 | Yes |
| 1104 | `_1104()` | `msg.mathCalculationExceptionDivisionByZero` | `critical` | 500 | Yes |
| 1105 | `_1105()` | `msg.mathCalculationExceptionInvalidValueError` | `critical` | 500 | Yes |
| 1106 | `_1106()` | `msg.mathCalculationExceptionUnsupportedFunction` | `critical` | 500 | Yes |

**Usage:**

- **Code 1101** — Thrown by `FooinoMathHandler::throwInvalidPrecisionException()` when precision is outside the allowed range (0–12).
- **Code 1102** — Thrown by `FooinoMathHandler::throwInvalidArgumentsCountException()` when an insufficient number of operands is provided for the operation.
- **Code 1103** — Thrown by `FooinoMathHandler::throwInvalidArgumentTypeException()` when a non-numeric operand is passed to an arithmetic operation.
- **Code 1104** — Thrown by `FooinoMathHandler::throwDivisionByZeroException()` when division or modulo by zero is attempted.
- **Code 1105** — Thrown by `FooinoMathHandler::throwInvalidValueErrorException()` when an operand value is invalid (e.g., `INF`, exponent overflow in scientific notation).
- **Code 1106** — Thrown by `FooinoMathHandler::throwUnsupportedFunctionException()` when an unrecognised bcmath function name is provided.

---

## `TokenGeneratorException` (codes 1200–1299)

Default message key: `msg.tokenGeneratorException`

| Code | Method | Message Key | Level | HTTP Status | Logged |
|---|---|---|---|---|---|
| 1200 | Default | `msg.tokenGeneratorException` | `error` | 500 | Yes |
| 1201 | `_1201()` | `msg.tokenGeneratorExceptionLengthMustBePositive` | `error` | 500 | Yes |
| 1202 | `_1202()` | `msg.tokenGeneratorExceptionBigLengthNumber` | `error` | 500 | Yes |
| 1203 | `_1203()` | `msg.tokenGeneratorExceptionSmallLengthNumberForStrongPassword` | `error` | 500 | Yes |
| 1204 | `_1204()` | `msg.tokenGeneratorExceptionSmallLengthNumberForPassword` | `error` | 500 | Yes |
| 1205 | `_1205()` | `msg.tokenGeneratorExceptionFieldIsRequired` | `error` | 500 | Yes |
| 1206 | `_1206()` | `msg.tokenGeneratorExceptionSmallLengthNumberForMemorable` | `error` | 500 | Yes |

**Usage:**

- **Code 1201** — Thrown by `TokenGenerator::throwLengthMustBePositiveException()` when length is zero or negative.
- **Code 1202** — Thrown by `TokenGenerator::throwBigLengthNumberException()` when length exceeds the maximum of 255.
- **Code 1203** — Thrown when `strongPassword` format is used with length less than 12.
- **Code 1204** — Thrown when `password` format is used with length less than 8.
- **Code 1205** — Thrown when a model is configured for uniqueness checking but no field name was provided.
- **Code 1206** — Thrown when `memorableOtp` format is used with length less than 2.
