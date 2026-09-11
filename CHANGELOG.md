# Changelog

All notable changes to `fooino/core` will be documented in this file

## 1.4.0 - 2026-09-11

- Upgrade the pest from version `4.7` to the `5.0`
- Added the `ApiResourceable` trait for building resources and collections on the standard API response envelope (`status`, `success`, `message`, `errors`, `data`, `additional`)
- Added `includeHTMLTags` support to `Sanitizer::normalizeInput()` and the `normalizeInput()` helper for preserving extra HTML tags
- Added `SingletonableTask::flush()` to clear every cached singleton instance across all subclasses
- Added `convertScientificNumber()` and `trimTrailingZeros()` global helpers
- Added public `MIN_PASSWORD_LENGTH` and `MIN_STRONG_PASSWORD_LENGTH` constants on `TokenGenerator`
- Extracted the Sanitizer data sources into cached tasks: `GetAllowedHTMLTagsTask`, `GetForbiddenCharactersTask`, and `GetForbiddenFilesTask`
- Refactored the Sanitizer recursion guard: input depth is validated once with `validateValue()` instead of counting per-method recursion, so wide arrays are no longer affected and values nested deeper than 25 levels throw `InfiniteLoopException` (code 252)
- Refactored the `NormalizesInputs` wildcards: every wildcard rule on the same parent now applies, specific rules win over wildcard rules for the same field regardless of rule order, and unruled nested siblings are preserved when merging results
- `removeComma()`, `removeWhitespace()`, and `replaceSlashWithDash()` now process nested arrays recursively
- `FooinoException::__construct()` now honours the given message, code, and previous exception, falling back to the class defaults when they are omitted
- `FooinoException::from()` now keeps the wrapper as an envelope and merges extra context into the wrapped `FooinoException` cause
- `weakPassword` now delegates to the numeric generator, so it shares one implementation and accepts any length like `numeric`
- Renamed `TokenGenerator::pipeline()` to `addPipeline()`, and `SingletonableTask::setData()` to `memoize()`
- Renamed the exception message keys from `fooinoRunTimeException*` to `fooinoRuntimeException*`
- Renamed the user timezone config key from `user-timezone` to `fooino.user_timezone`
- `Json::encodePretty()` now accepts scalar values (`int`, `float`, `bool`, `null`) in addition to strings and arrays
- Token uniqueness checking now requires both `model` and `field` to be set (code 1205)
- Math now rejects `NaN` operands with `MathCalculationException` (code 1105) instead of failing with a raw error
- Fixed `convertScientificNumber()` returning a trailing decimal point for zero-heavy mantissas (e.g. `1.00E+0`, `1.00100000E+5`)
- Fixed `unitSizeFormat()` resolving the wrong `msg.invalid` translation key, and `unitNumberFormat()` now resolves plural unit labels through `trans_choice()` so pipe-based translations work
- Added `API_RESOURCEABLE.md` and refreshed the facade, helper, Sanitizer, and exception documentation

## 1.3.0 - 2026-07-14

- Added `setPlaceholders()` / `getPlaceholders()` on `FooinoException` for passing translation replacement parameters to Laravel's `__()` helper

## 1.2.0 - 2026-07-12

- Added `context()` method on `FooinoException` for Laravel's exception reporting pipeline
- Refactored `from()` to properly handle wrapping of existing `FooinoException` instances

## 1.1.0 - 2026-07-12

- Renamed `report()` to `setReport()` at `FooinoException` to avoid conflict with Laravel's exception handler which calls `$exception->report()` during reporting

## 1.0.0 - 2026-07-01

- initial release
