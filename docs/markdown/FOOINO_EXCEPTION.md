# FooinoException

Consistent exception structure with fluent setters for message, code, severity level, HTTP status code, contextual data, and log reporting — enabling Laravel's exception handler to process all fooino exceptions uniformly without `if`/`switch` branching.

## Usage

Custom exceptions extend `FooinoException` and define default property values:

```php
class UserNotFoundException extends FooinoException
{
    protected $message = 'User not found';
    protected $code = 1404;
    protected string $level = 'warning';
    protected int $httpStatusCode = 404;
}
```

Throw with fluent overrides:

```php
app(UserNotFoundException::class)
    ->setHttpStatusCode(404)
    ->with(['user_id' => $id])
    ->warning()
    ->throw();
```

The constructor follows the standard exception signature — `$message`, `$code`, and `$previous` are honoured, and each falls back to the value declared on the class when omitted:

```php
new UserNotFoundException();                              // 'User not found' / 1404
new UserNotFoundException('Custom message');               // 'Custom message' / 1404
new UserNotFoundException('Custom message', 1500);         // 'Custom message' / 1500
new UserNotFoundException('Custom message', 1500, $prev);  // with previous exception chained
```

## Fluent Setters

| Setter | Type | Default | Description |
|---|---|---|---|
| `setMessage(string)` | `string` | `''` | Override the exception message |
| `setCode(int)` | `int` | `0` | Set the unique error code |
| `setLevel(string)` | `string` | `'error'` | Set the severity level |
| `setHttpStatusCode(int)` | `int` | `500` | Set the HTTP status code for the response |
| `with(array)` | `array` | `[]` | Attach contextual data for debugging and logging |
| `setPlaceholders(array)` / `getPlaceholders()` | `array` | `[]` | Translation replacements for `__()` (e.g. `':EXCEPTION_CODE'`) |
| `setReport(bool)` / `shouldReport()` / `dontReport()` | `bool` | `true` | Control whether the exception is logged |
| `cause(?Exception)` / `getCause()` | `?Exception` | `null` | Attach/retrieve the original exception that was wrapped |
| `context()` | `array` | `[]` | Context Laravel's report pipeline logs automatically (returns `with()`) |

## Wrapping Non-Fooino Exceptions

When a `try...catch` block catches a generic exception (e.g., Laravel's `ModelNotFoundException`), it wraps it into a `FooinoException` so your handler only needs one `instanceof` check:

```php
try {
    // some operation
} catch (ModelNotFoundException $e) {

     app(FooinoException::class)
        ->setHttpStatusCode(404)
        ->with(['id' => $id])
        ->warning()
        ->cause($e)   // preserve the original exception
        ->throw();
}
```

### Wrapping with `from()`

When the caught exception is already a `FooinoException` and you want to add context while keeping the original error data, use `from()`. The wrapper stays a thin **envelope**: it keeps its own message, code, level, and status, while `from()` attaches the original as the cause and merges the extra context into that cause:

```php
try {
    // some operation
} catch (FooinoException $e) {

    app(CanNotConvertDateException::class)
        ->from(e: $e, with: ['input' => $input])
        ->throw();
}
```

Handlers should unwrap the envelope to the root cause when the cause is itself a `FooinoException`; otherwise the wrapper carries the error data.

## Laravel Exception Handler

A single handler covers every fooino exception:

```php
    public function report(Throwable $e)
    {
        $e = $this->resolveException($e);

        if (
            $e instanceof FooinoException &&
            $e->reportable() === false
        ) {
            return;
        }

        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        $e = $this->resolveException($e);

        if ($request->expectsJson()) {

            if ($e instanceof FooinoException) {

                return jsonRespond(
                    status: $e->getHttpStatusCode(),
                    message: __(
                        key: $e->getMessage(),
                        replace: array_merge(
                            [
                                'EXCEPTION_CODE' => $e->getCode()
                            ],
                            $e->getPlaceholders()
                        )
                    ),
                );
            }
        }

        return parent::render($request, $e);
    }

    protected function resolveException(Throwable $e): Throwable
    {
        if (
            $e instanceof FooinoException &&
            $e->getCause() !== null
        ) {
            return $e->getCause();
        }

        return $e;
    }
```

## Severity Level Shorthands

| Method | Level |
|---|---|
| `emergency()` | `'emergency'` |
| `alert()` | `'alert'` |
| `critical()` | `'critical'` |
| `error()` | `'error'` |
| `warning()` | `'warning'` |
| `notice()` | `'notice'` |
| `info()` | `'info'` |
| `debug()` | `'debug'` |

## Log Format

The `log()` method serializes the exception into a pipe-delimited line for structured logging:

```
Fooino\Core\Exceptions\ExampleException|message text|100|500|error|{"key":"value"}
```

Include a stack trace with `log(trace: true)` (default).
