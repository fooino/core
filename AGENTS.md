# Fooino Core

Core foundational library for the Fooino ecosystem — provides date conversion, arbitrary-precision math, JSON handling, sanitization, token generation, and global helpers.

**Critical foundation package.** All other fooino packages and projects depend on this library. Every function, class, and helper must be written with extreme care — edge cases, input validation, type safety, and predictability are non-negotiable. A bug here cascades everywhere.

---

## Project Architecture

Source code lives under `src/`. File placement is determined by content, not by guessing:

| Directory | When to place a file here |
|---|---|
| `src/Interfaces/` | Defining a contract/abstraction. Name must end with `able` (e.g., `Jsonable`, `Mathable`). |
| `src/Concretes/` | Implementing an interface or providing a concrete service. Group by domain in subdirectories (e.g., `Concretes/Math/`). |
| `src/Facades/` | Creating a Laravel facade. Must extend `Illuminate\Support\Facades\Facade`. |
| `src/Exceptions/` | Creating an exception. Must extend `FooinoException`. |
| `src/Providers/` | Service providers. Must extend `Illuminate\Support\ServiceProvider`. |
| `src/Support/` | Utility/helper classes that don't implement a domain interface (e.g., `Sanitizer`, `TokenGenerator`). |
| `src/Enums/` | PHP native enums only. |
| `src/Concerns/` | Traits only. |
| `tests/Unit/` | Pest tests. One test file per source class. |

Global functions go in `src/helpers.php`, each wrapped in `if (!function_exists(...))`.

**Architecture is enforced** by `tests/Unit/ArchitectureUnitTest.php` — any new file must pass these structural assertions.

---

## Coding Standards & Design Patterns

### Philosophy
Follow **SOLID** and **KISS** principles. Methods must have a **single responsibility** and be small. Write ninja code — concise but never obscure. Maintainability and readability are paramount.

### Method & Variable Naming
- Methods use `camelCase`. Variables use `camelCase`.
- Private/protected properties use `camelCase`.
- Constants use `SCREAMING_SNAKE_CASE`.
- Boolean methods imply a question: `isJson()`, `greaterThan()`, `notEqual()`.
- Getter/setter convention: `getPrecision()` / `setPrecision()`.

### Type Declarations
- **Every** method parameter and return type must be explicitly typed (PHP 8.x union types used extensively).
- Never use `mixed` as a lazy escape — only when types genuinely vary.
- Interface methods carry full `@param`/`@return` phpDocs; concrete implementations may omit `@param`/`@return` if the signature is self-documenting.

### phpDoc
- **Every method must have a phpDoc description.** The description explains **why**, not how. Keep it one line.
- Example: `/** Retrieve the current precision value used for truncating output numbers */` — not "Returns the precision property".

### Method Size & Responsibility
- Each method does one thing. If a method needs a multi-paragraph phpDoc to explain what it does, it does too much. Split it.
- Private helper methods are encouraged to keep public methods lean.

### Fluent Interfaces
- Setters and config methods return `static` to enable chaining.
- The exception system uses fluent method chaining: `app(Exception::class)->_CODE()->with([])->throw()`.

### Named Arguments
- Always use named arguments when calling methods: `Math::sum(operand: [1, 2])`, not `Math::sum([1, 2])`.

### Laravel Manager Driver Pattern
- Each domain (Json, Date, Math) uses: `*able` interface → `*Handler` concrete → `*Manager` (Laravel driver) → `Facade`.
- Managers extend `Illuminate\Support\Manager` and are registered as singletons in `CoreServiceProvider`.
- Facade `@method` docblocks mirror the interface exactly.

---

## Error Code Ranges

All errors use the fluent exception system built on `FooinoException`. The trait `FooinoExceptionList` defines error codes in these ranges:

| Range | Domain |
|---|---|
| `1001` | General invalid period |
| `10050–10099` | Date conversion (`CanNotConvertDateException`) |
| `10100–10199` | Math calculation (`MathCalculationException`) |
| `10200–10300` | Infinite loop detection (`InfiniteLoopException`) |
| `10400–10499` | Token generation (`TokenGeneratorException`) |

When adding a new error, pick the next unused code in the appropriate range, add an `_XXXX()` method to `FooinoExceptionList`, and follow the existing pattern: set message key, code, level, and reportable flag via fluent chain.

---

## Testing Conventions

- **Framework**: Pest PHP with Orchestra Testbench.
- **Architecture tests**: `tests/Unit/ArchitectureUnitTest.php` enforces structural rules (directories contain correct types, no debug calls, managers extend correct base class).
- **Data providers**: Static methods in `tests/Data/Datasets.php`, composable via `Datasetable` trait.
- **Naming**: Test methods use descriptive lowercase strings: `test('precision getter and setter', function () { ... })`.
- **Coverage requirements**: Code coverage AND type coverage must both stay above **90%**. Every new or changed source line must have a corresponding test. Do not ship untested code.
- **Run tests with**: `composer pest` or `./vendor/bin/pest`.
