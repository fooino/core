# Json Facade

The JSON facade provides a consistent interface for JSON serialization, deserialization, validation, and API response building.

## Architecture

```
Jsonable (Interface — Fooino\Core\Interfaces\Jsonable)
  └── FooinoJsonHandler (Concrete — Fooino\Core\Concretes\Json\FooinoJsonHandler)
      └── JsonManager (Laravel driver manager — Fooino\Core\Concretes\Json\JsonManager)
          └── Json (Facade — Fooino\Core\Facades\Json)
```

## Usage

### Validate JSON

```php
Json::is(value: '{"foo":"bar"}'); // true
Json::is(value: 'foo bar');       // false
isJson(value: '{"foo":"bar"}');   // true — global helper
```

### Encode to JSON

```php
Json::encode(value: ['foo' => 'bar']);       // '{"foo":"bar"}'
jsonEncode(value: ['foo' => 'bar']);         // '{"foo":"bar"}' — global helper
```

Values that are already valid JSON strings pass through without double-encoding.

### Pretty-print for display

```php
Json::encodePretty(value: ['foo' => 'bar']);  // HTML-safe pretty-printed JSON
jsonEncodePretty(value: ['foo' => 'bar']);    // global helper
```

Returns the value formatted with `JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE` and HTML-escaped via `htmlspecialchars` with `ENT_QUOTES`.

### Decode from JSON

```php
Json::decode(json: '{"foo":"bar"}');                      // stdClass { foo: 'bar' }
Json::decode(json: '{"foo":"bar"}', associative: true);   // ['foo' => 'bar']
Json::decode(json: 'foo bar');                            // 'foo bar' — non-JSON passes through

jsonDecode(json: '{"foo":"bar"}');                        // global helper
```

### Decode to array

```php
Json::decodeToArray(json: '{"foo":"bar"}');  // ['foo' => 'bar']
Json::decodeToArray(json: 'null');           // []
Json::decodeToArray(json: 5);                // [5] — non-JSON wrapped in array

jsonDecodeToArray(json: '{"foo":"bar"}');    // global helper
```

### Build an API response

```php
Json::respond(
    status: 200,
    message: 'ok',
    errors: ['field' => 'validation error'],
    data: ['user' => $user],
    additional: ['meta' => 'data'],
    headers: ['X-Custom' => 'value'],
    options: JSON_UNESCAPED_UNICODE
);

jsonRespond(status: 200, message: 'ok'); // global helper
```

Response structure:

```json
{
    "status": 200,
    "success": true,
    "message": "ok",
    "errors": [],
    "data": [],
    "additional": []
}
```

The `success` field is automatically derived from the status code (`true` for 2xx, `false` otherwise).

### Response template

```php
Json::responseTemplate();
// ['status' => 200, 'success' => true, 'message' => '', 'errors' => [], 'data' => [], 'additional' => []]
```
