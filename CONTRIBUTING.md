# Contributing to Fooino Core

Thanks for contributing! Here's how to get started.

## Setup

1. Fork and clone the repo
2. Run `composer install`
3. Create a branch: `feature/your-feature` or `fix/issue-description`

## Development Workflow

- Write a failing test first (TDD)
- Implement the minimum code to make it pass
- Run `./vendor/bin/pest` to verify all tests pass
- Check coverage: `./vendor/bin/pest --coverage` (target: >90%)

## Pull Requests

1. Push your branch to your fork
2. Open a PR against `main` with a clear description
3. Reference any related issues

## Guidelines

- Follow SOLID & KISS — small methods, single responsibility
- Every method needs a phpDoc describing **why**, not how
- Explicit type declarations on all parameters and return types
- Always use named arguments when calling methods
- Use the existing error code ranges for new exceptions
- No `dd`, `dump`, `var_dump`, or `ray` calls in committed code
