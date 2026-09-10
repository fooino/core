# Sanitizer

The `Sanitizer` class provides a fluent interface for cleaning and normalizing user input. Each method returns `$this` so operations can be chained. Call `value()` at the end to retrieve the result.

```php
sanitizer($input)->normalizeInput()->lowercase()->value();
```

---

## normalizeInput

Normalize the input by converting Persian/Arabic digits and letters, removing zero-width non-joiners, stripping XSS vectors, and trimming whitespace.

This is the primary sanitization method — designed for `prepareForValidation` in Laravel form requests to normalize all user inputs before validation.

**For strings:**
- Converts Persian digits (`۰۱۲۳۴۵۶۷۸۹`) to English (`0123456789`)
- Converts Arabic digits (`٠١٢٣٤٥٦٧٨٩`) to English (`0123456789`)
- Replaces Arabic letters `ي` and `ك` with Persian `ی` and `ک`
- Removes Zero-Width Non-Joiner (U+200C), Zero-Width Joiner (U+200D), and BOM (U+FEFF)
- Strips HTML tags not in the allowed list (see [Allowed HTML Tags](#allowed-html-tags) below) for XSS reduction
- Trims leading/trailing whitespace

**For arrays:** Recursively normalizes all string values at every nesting level.

**For JSON strings:** Decodes to array, normalizes all string values, then re-encodes. Numeric strings (`'5'`, `'5.5'`), `'true'`, `'false'`, `'null'`, and `'{}'` are treated as plain strings, not JSON.

**Type preservation:** Integers, floats, booleans, null, and objects are returned unchanged.

```php
// Digit conversion
sanitizer('۰۱۲۳۴۵۶۷۸۹')->normalizeInput()->value();           // '0123456789'
sanitizer('٠١٢٣٤٥٦٧٨٩')->normalizeInput()->value();           // '0123456789'

// Arabic to Persian letters
sanitizer('عليك سلام')->normalizeInput()->value();             // 'علیک سلام'

// ZWNJ/ZWJ/BOM removal
sanitizer('foo' . json_decode('"\u200C"') . 'bar')->normalizeInput()->value(); // 'foobar'

// HTML stripping (script tag removed, content preserved)
sanitizer('<script>alert("XSS")</script>')->normalizeInput()->value(); // 'alert("XSS")'

// Allowed tags are preserved
sanitizer('<b>bold</b> <i>italic</i>')->normalizeInput()->value(); // '<b>bold</b> <i>italic</i>'

// Trimming
sanitizer('  hello world  ')->normalizeInput()->value();       // 'hello world'

// Type preservation
sanitizer(123)->normalizeInput()->value();                     // 123 (int)

// Arrays (recursive)
sanitizer(['foo' => '۰۱۲۳', 'bar' => ['baz' => 'عليك']])->normalizeInput()->value();
// ['foo' => '0123', 'bar' => ['baz' => 'علیک']]

// JSON strings
sanitizer('{"name":"۰۱۲۳"}')->normalizeInput()->value();       // '{"name":"0123"}'

// Numeric strings are NOT treated as JSON
sanitizer('5')->normalizeInput()->value();                     // '5'
sanitizer('true')->normalizeInput()->value();                  // 'true'
sanitizer('null')->normalizeInput()->value();                  // 'null'
sanitizer('{}')->normalizeInput()->value();                    // '{}'
```

### Allowed HTML Tags

These tags are preserved by `normalizeInput`. All other tags are stripped (their inner text content is kept):

```
<b> <strong> <em> <i> <u> <s> <sub> <sup> <p> <br> <hr> <pre> <code>
<img> <button> <div> <span> <h1> <h2> <h3> <h4> <h5> <h6>
<table> <caption> <col> <colgroup> <td> <tr> <th> <thead> <tbody>
<ul> <ol> <li> <dl> <dt> <dd> <blockquote> <q> <figure> <figcaption>
<mark> <small> <del> <ins> <abbr> <cite> <a> <picture>
```

> **Note:** `strip_tags` does not remove event handler attributes (e.g., `onclick`, `onerror`) inside allowed tags. This method reduces XSS vector surface but is not a complete XSS sanitizer.

#### `includeHTMLTags` parameter

`normalizeInput(array $includeHTMLTags = [])` merges extra tags into the default list for that single call, preserving them on top of the built-in set:

```php
sanitizer('<my-tag>hi</my-tag>')->normalizeInput()->value();
// 'hi'  → <my-tag> is stripped by default

sanitizer('<my-tag>hi</my-tag>')->normalizeInput(includeHTMLTags: ['<my-tag>'])->value();
// '<my-tag>hi</my-tag>'  → preserved for this call only
```

---

## replaceForbiddenCharacters

Remove or replace characters considered forbidden or harmful. Operates on strings and arrays recursively.

```php
$input = 'hello! foo@bar.com';

// Remove all forbidden characters
sanitizer($input)->replaceForbiddenCharacters()->value();            // 'hellofoobarcom'

// Replace with a different character
sanitizer($input)->replaceForbiddenCharacters(replaceWith: '-')->value(); // 'hello--foo-bar-com'

// Exclude specific characters from removal
sanitizer($input)->replaceForbiddenCharacters(excludes: ['@', '.'])->value(); // 'hellofoobar.com'
//                                                                              @ is kept, . is kept
```

### Forbidden Characters

| Characters |
|---|
| ` ` `-` `.` `!` `@` `#` `$` `%` `^` `&` `*` `(` `)` `=` `+` `{` `}` `:` `;` `"` `'` `?` `؟` `<` `>` `,` <code>&#124;</code> `` ` `` `/` `\` `[` `]` `~` `°` `../` `_` |

Multi-character patterns like `../` are matched before their individual characters.

---

## replaceForbiddenFiles

Remove or replace known sensitive file names and extensions. Operates on strings and arrays recursively.

```php
sanitizer('config/database.php')->replaceForbiddenFiles()->value();       // 'config/database'
sanitizer('.env')->replaceForbiddenFiles()->value();                       // ''

// Keep specific extensions
sanitizer('config/database.php')->replaceForbiddenFiles(excludes: ['.php'])->value();
// 'config/database.php'

// Custom replacement
sanitizer('.env')->replaceForbiddenFiles(replaceWith: '[REDACTED]')->value();
// '[REDACTED]'
```

Includes common filenames (`.env`, `composer.json`, `index.php`, `artisan`), config files, certificate/key extensions, log files, database dumps, compiled assets, deployment artifacts, and CI/CD configurations.

---

## replaceEmoji

Remove or replace emoji characters from the value. Operates on strings and arrays recursively.

Uses a comprehensive Unicode range covering emoticons, pictographs, transport symbols, flags, skin tone modifiers, ZWJ sequences, variation selectors, keycap combining characters, subdivision flags, and more.

```php
sanitizer('Hello 😎 World')->replaceEmoji()->value();                // 'Hello  World'
sanitizer('😊😎👍')->replaceEmoji()->value();                          // ''
sanitizer('🇩🇪')->replaceEmoji()->value();                             // ''

// Custom replacement text
sanitizer('😀')->replaceEmoji(replaceWith: '[emoji]')->value();      // '[emoji]'
sanitizer('Hello 😎 World')->replaceEmoji(replaceWith: '-')->value(); // 'Hello - World'

// Arrays
sanitizer(['hello 😊 world', ['foo 😎 bar']])->replaceEmoji()->value();
// ['hello  world', ['foo  bar']]
```

---

## lowercase

Convert all string values to lowercase. Operates on strings and arrays recursively. Uses `mb_strtolower` for proper Unicode handling.

```php
sanitizer('Hello World')->lowercase()->value();                     // 'hello world'
sanitizer('ÜBER')->lowercase()->value();                            // 'über'
sanitizer('ТЕКСТ')->lowercase()->value();                           // 'текст'
sanitizer(['FOO', 'BAR', ['BAZ']])->lowercase()->value();          // ['foo', 'bar', ['baz']]
```

Non-string values in arrays (integers, floats, booleans, null) are passed through unchanged.

---

## uppercase

Convert all string values to uppercase. Operates on strings and arrays recursively. Uses `mb_strtoupper` for proper Unicode handling.

```php
sanitizer('Hello World')->uppercase()->value();                     // 'HELLO WORLD'
sanitizer('über')->uppercase()->value();                            // 'ÜBER'
sanitizer('straße')->uppercase()->value();                          // 'STRASSE'
sanitizer(['foo', 'bar', ['baz']])->uppercase()->value();          // ['FOO', 'BAR', ['BAZ']]
```

---

## collapse

Collapse consecutive occurrences of a character into a single occurrence. Operates on strings and arrays recursively.

```php
sanitizer('foo---bar')->collapse(char: '-')->value();               // 'foo-bar'
sanitizer('foo.......bar')->collapse(char: '.')->value();            // 'foo.bar'
sanitizer('foo___bar')->collapse(char: '_')->value();               // 'foo_bar'
sanitizer('---')->collapse(char: '-')->value();                     // '-'

// Arrays
sanitizer(['foo---bar', ['baz___qux']])->collapse(char: '-')->value();
// ['foo-bar', ['baz___qux']]    (only ---- is collapsed, not underscore)
```

Passing an empty string as `$char` returns the value unchanged.

---

## trim

Remove characters from the beginning and end of the value. Operates on strings and arrays recursively. Uses `mb_trim` for proper Unicode handling.

Default character mask removes standard whitespace (space, newline, carriage return, tab, vertical tab, null byte).

```php
// Default whitespace trim
sanitizer('  hello  ')->trim()->value();                           // 'hello'
sanitizer("\nhello\n")->trim()->value();                            // 'hello'

// Custom character
sanitizer('--hello--')->trim(char: '-')->value();                   // 'hello'
sanitizer('---foo---bar---')->trim(char: '-')->value();             // 'foo---bar'
sanitizer('.foo.bar.')->trim(char: '.')->value();                   // 'foo.bar'

// Multi-byte characters
sanitizer('üüfooü')->trim(char: 'ü')->value();                      // 'foo'

// Arrays
sanitizer(['-foo-', ['-bar-']])->trim(char: '-')->value();
// ['foo', ['bar']]
```

---

## Helper Function

The global `normalizeInput` helper provides a shorthand for creating a Sanitizer, calling `normalizeInput`, and retrieving the value:

```php
normalizeInput('۰۱۲۳');              // '0123'
normalizeInput('<script>XSS</script>'); // 'XSS'
normalizeInput(['foo' => 'عليك']);    // ['foo' => 'علیک']
```

---

## Recursion Safety

Every pipeline method walks nested arrays recursively, so the value's nesting depth is validated **once up front**: the constructor throws an `InfiniteLoopException` (code 252) when an array value is nested deeper than 25 levels. The same check runs inside `normalizeInput` on JSON strings after decoding, since a deep JSON payload can expand into a deep array.

Only the nesting **depth** is limited — wide arrays with many items or many shallow sibling arrays are processed normally.

```php
// Depth 26 → throws InfiniteLoopException (252) at construction
sanitizer([[[ ... 26 levels of nesting ... ['x']]]]);

// Wide but shallow → fine
sanitizer(array_map(fn ($i) => ["item-$i"], range(1, 1000)));
```
