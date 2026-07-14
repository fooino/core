# Changelog

All notable changes to `fooino/core` will be documented in this file

## 1.3.0 - 2026-07-14

- Added `setPlaceholders()` / `getPlaceholders()` on `FooinoException` for passing translation replacement parameters to Laravel's `__()` helper

## 1.2.0 - 2026-07-12

- Added `context()` method on `FooinoException` for Laravel's exception reporting pipeline
- Refactored `from()` to properly handle wrapping of existing `FooinoException` instances

## 1.1.0 - 2026-07-12

- Renamed `report()` to `setReport()` at `FooinoException` to avoid conflict with Laravel's exception handler which calls `$exception->report()` during reporting

## 1.0.0 - 2026-07-01

- initial release
