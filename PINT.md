# Pint Configuration

Base: the `laravel` preset, with `exclude` skipping `public/` (the stock front controller stays untouched).

| Rule | Configuration | Why |
|---|---|---|
| `binary_operator_spaces` | `default: single_space`, `operators: { "=>": "align_single_space_minimal" }` | All binary operators keep a single space, and multi-line array arrows (`=>`) are automatically aligned to the longest key of each consecutive block (minimal padding). Blank lines end an alignment block; inline and single-element arrays keep a single space. |
| `concat_space` | `spacing: one` | Enforce spaces around concatenation: `$a . $b` instead of `$a.$b`. |
| `fully_qualified_strict_types` | `false` | Keep fully qualified class names where written (e.g. `bootstrap/providers.php`) instead of adding imports. |
| `function_declaration` | `closure_fn_spacing: none` | Keep arrow functions without a space: `fn() => ...`, not `fn () => ...`. Regular closures keep the space: `function () { ... }`. |
| `new_with_parentheses` | `false` | Keep `new Foo` without parentheses when no arguments are passed; Pint will not add or remove them. |
| `no_extra_blank_lines` | `false` | Keep deliberate blank lines inside blocks (for example the blank line after an opening brace) exactly as written. |
| `not_operator_with_successor_space` | `false` | Keep negation without a space: `!$value`, not `! $value`. |
| `nullable_type_declaration` | `false` | Leave nullable types as written: `string|null` stays `string|null` and is not rewritten to `?string`. Switch to `{"syntax": "union"}` to actively enforce the union style. |
| `ordered_imports` | `false` | Keep the manual `use` statement order and grouping. |
| `single_line_empty_body` | `true` | Allow truly empty method bodies to stay on one line (`public function run(): void {}`); bodies containing comments are untouched. |
| `trailing_comma_in_multiline` | `false` | Never add trailing commas to multi-line arrays, calls, or parameters. |

## Working with the config

```bash
./vendor/bin/pint --test        # list files and fixers that would change (no writes)
./vendor/bin/pint --test -v     # show the diffs
./vendor/bin/pint --dirty       # format only uncommitted files
./vendor/bin/pint --no-config   # run the raw Laravel preset, for comparison
```

When `--test` reports a fixer that fights the project style, either accept the change or add that fixer to `pint.json`. The goal is `./vendor/bin/pint --test` passing without modifying the codebase.
