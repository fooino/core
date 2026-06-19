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

## Fluent Setters

| Setter | Type | Default | Description |
|---|---|---|---|
| `setMessage(string)` | `string` | `''` | Override the exception message |
| `setCode(int)` | `int` | `0` | Set the unique error code |
| `setLevel(string)` | `string` | `'error'` | Set the severity level |
| `setHttpStatusCode(int)` | `int` | `500` | Set the HTTP status code for the response |
| `with(array)` | `array` | `[]` | Attach contextual data for debugging |
| `report(bool)` / `shouldReport()` / `dontReport()` | `bool` | `true` | Control whether the exception is logged |
| `cause(?Exception)` / `getCause()` | `?Exception` | `null` | Attach/retrieve the original non-fooino exception that was wrapped |

## Wrapping Non-Fooino Exceptions

When a helper like `dbTransaction` catches a generic exception (e.g., Laravel's `ModelNotFoundException`), it wraps it into a `FooinoException` so your handler only needs one `instanceof` check:

```php
try {
    // some operation
} catch (ModelNotFoundException $e) {
    throw app(FooinoException::class)
        ->setHttpStatusCode(404)
        ->with(['id' => $id])
        ->warning()
        ->cause($e)   // preserve the original exception
        ->throw();
}
```

In the Laravel exception handler:

```php
public function report(Throwable $e): void
{
    if ($e instanceof FooinoException) {
        
        if($e->getCause() === null || $e->getCause() instanceof FooinoException){
            /**
             *  the caues root is FooinoException
             *  so you can easily call log(), getLevel(), getHttpStatusCode()... to handle better
             *  exception reporting and responsing
             */
        }else{
            // Reassign $e with original exception
            $e = $e->getCause();
        }
    }

    // handle the laravel or non-fooino exceptions here
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
