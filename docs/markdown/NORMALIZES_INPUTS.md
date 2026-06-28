# NormalizesInputs

A trait for Laravel form requests that automatically prepares user input before validation. It scans the form request's `rules()`, normalizes each input through a configurable pipeline, and merges the results back into the request.

## Pipeline

Each input goes through three stages in order:

```
normalizeInput → nullIfBlank / nullIfBlankOrZero → custom pipes
```

## Usage

```php
use Fooino\Core\Concerns\NormalizesInputs;

class StoreArticleRequest extends FormRequest
{
    use NormalizesInputs;

    protected function inputConfigs(): array
    {
        return [
            'title' => ['default' => 'Untitled'],
            'slug'  => ['pipe' => fn($v) => sanitizeSlug($v)],
            'count' => ['nullOnZero' => true],
            'phone' => ['pipe' => [
                fn($v) => removeComma($v),
                fn($v) => removeWhitespace($v),
                fn($v) => sanitizeNumber($v),
            ]],
            'raw'   => ['skipNormalize' => true, 'keepBlank' => true],
        ];
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug'  => 'nullable|string',
            'count' => 'nullable|integer',
            'phone' => 'nullable|string',
            'raw'   => 'nullable|string',
            'bio'   => 'nullable|string',
        ];
    }
}
```

Inputs not listed in `inputConfigs()` (like `bio` above) get the default treatment: both `normalizeInput` and `nullIfBlank`.

---

## Dot Notation

Rule keys using dot notation (`user.name`) are resolved to nested arrays. The result is merged back as a proper nested structure.

```php
protected function inputConfigs(): array
{
    return [
        'user.name' => ['default' => 'Guest'],
    ];
}

public function rules(): array
{
    return [
        'user.name' => 'required|string|max:255',
    ];
}
```

Input `['user' => ['name' => 'عليك']]` becomes `['user' => ['name' => 'علیک']]` after normalization.

## Wildcards

Rule keys with `*` wildcards (`users.*.name`) apply the same config to every matching item in an array. Each item's field is normalized independently.

```php
protected function inputConfigs(): array
{
    return [
        'users.*.name' => ['default' => 'Guest'],
    ];
}

public function rules(): array
{
    return [
        'users.*.name' => 'required|string|max:255',
    ];
}
```

Input `['users' => [['name' => 'عليك'], ['name' => '']]]` becomes `['users' => [['name' => 'علیک'], ['name' => 'Guest']]]`.

When the parent value is not an array, the wildcard is safely skipped and no merge occurs for that key.

---

## Config Options

### skipNormalize

Skip the `normalizeInput` step. Use this for inputs that must keep their raw value (e.g., markdown, code snippets).

```php
'raw' => ['skipNormalize' => true]
```

### keepBlank

Keep blank values as-is instead of converting them to `null`. Skips both `nullIfBlank` and `nullIfBlankOrZero`.

```php
'bio' => ['keepBlank' => true]  // '' stays ''
```

### default

Fallback value used when the input is blank or null. Works with both `nullIfBlank` and `nullIfBlankOrZero`.

```php
'title' => ['default' => 'Untitled']       // '' → 'Untitled'
'count' => ['nullOnZero' => true, 'default' => 0]  // '0' → 0
```

### nullOnZero

Use `nullIfBlankOrZero` instead of `nullIfBlank`. This also converts numeric zero (`0`, `0.0`, `'0'`) to null. A `default` can be combined to provide a fallback instead.

```php
'count' => ['nullOnZero' => true]          // '' → null, '0' → null
'count' => ['nullOnZero' => true, 'default' => 0]  // '0' → 0 (fallback)
```

### pipe

One or more custom transformations applied after `nullIfBlank`. Each callable receives `($value, $request)` and returns the transformed value.

Pass a single callable:

```php
'slug' => ['pipe' => fn($v) => sanitizeSlug($v)]
```

Or an array for multiple transformations in sequence:

```php
'phone' => ['pipe' => [
    fn($v) => removeComma($v),
    fn($v) => removeWhitespace($v),
    fn($v) => sanitizeNumber($v),
]]
```

Pipes run after `nullIfBlank`, so if the value was blank it arrives as `null`. Your pipe can check for this:

```php
'slug' => ['pipe' => fn($v) => $v === null ? null : sanitizeSlug($v)]
```

The second parameter gives access to the request, useful for generating values from other inputs:

```php
'slug' => ['pipe' => fn($v, $req) => sanitizeSlug($req->input('title'))]
```

---

## Default Behavior

Any input in `rules()` that is **not** listed in `inputConfigs()` still gets both `normalizeInput` and `nullIfBlank`. This means:

- Persian/Arabic digits are converted to English
- Arabic letters `ي` and `ك` are replaced with Persian `ی` and `ک`
- Zero-width characters (ZWNJ, ZWJ, BOM) are removed
- Non-allowed HTML tags are stripped
- Whitespace is trimmed
- Blank values become `null`

## Type Preservation

`normalizeInput` preserves the original PHP type of each value — integers stay integers, floats stay floats, booleans stay booleans, null stays null. Only strings are processed.

## Resolving in Tests

Use the `resolveRequest` helper to test form requests that use this trait:

```php
$request = resolveRequest(
    request: MyFormRequest::class,
    data: ['title' => 'Hello'],
);

expect($request->validated()['title'])->toBe('Hello');
```

`resolveRequest` creates the form request instance, merges the data, bootstraps the container, runs `prepareForValidation` (which triggers the trait), and validates the result.
