# SingletonableTask

An abstract base class that implements the **singleton + memoization** pattern for one-shot tasks. Each subclass is automatically a singleton — `instance()` always returns the same object — and `run()` lazily computes the result exactly once, caching it for all subsequent calls until `reset()` clears it.

## When to use

Use `SingletonableTask` when you have a unit of work that:

- Should be computed **at most once per request/cycle**
- Returns the **same result** every time it's called within a cycle
- Needs a simple **reset mechanism** to force re-computation

Common examples: loading configuration, fetching a remote resource, computing a derived value, building a lookup map.

> **Note:** cached instances live for the whole PHP process. Under standard PHP-FPM that matches one request, but under long-running workers (e.g., Octane or queue workers) the data stays cached across requests — call `flush()` (e.g., in a request lifecycle hook) to force a fresh cycle per request.

## How it works

```
┌──────────┐    run()     ┌──────────┐    getData()    ┌──────────────┐
│ Consumer │─────────────▶│   Task   │────────────────▶│  Computation │
└──────────┘              └──────────┘                 └──────────────┘
                               │
                               │  (2nd+ calls skip getData)
                               ▼
                          Return cached $data
```

- `instance()` returns the singleton.
- `run()` calls `getData()` on the **first** invocation only. Subsequent calls return the cached result.
- `reset()` clears the cached data so the next `run()` re-executes `getData()`.
- `flush()` clears **all** cached instances so the next `instance()` call rebuilds them.
- `beforeReset()` / `afterReset()` are lifecycle hooks that run before and after data is cleared.

## Usage example

```php
class AppConfig extends SingletonableTask
{
    protected function getData(): mixed
    {
        return json_decode(
            file_get_contents(base_path('config.json')),
            associative: true
        );
    }
}

// First call — reads and caches the file
$config = AppConfig::instance()->run();

// Subsequent calls — return cached result immediately
$same = AppConfig::instance()->run();

// Force re-read on next run
AppConfig::instance()->reset();
```

## API

### `instance(): static`

Returns the singleton instance. The constructor is `protected` — the only way to obtain an instance is through this method.

```php
$task = MyTask::instance();
```

### `flush(): void`

Clears every cached singleton instance across **all** subclasses. The next `instance()` call rebuilds a fresh instance with an empty cache. Useful for long-running processes where a per-request lifecycle applies (e.g., Octane request hooks) and in tests to reset state between cases.

```php
MyTask::flush();

$task = MyTask::instance(); // fresh instance with an empty cache
```

### `run(): mixed`

Executes `getData()` on the first call and caches the result. All subsequent calls return the cached value without re-executing `getData()`.

```php
$result = $task->run(); // calls getData()
$result = $task->run(); // returns cached value
```

### `reset(): static`

Clears the cached data so the next `run()` calls `getData()` again. Fires `beforeReset()` and `afterReset()` hooks. Returns the instance for fluent chaining.

```php
$task->reset()->run();
```

### `beforeReset(): void`

Hook called before cached data is cleared. Override to invalidate external caches, release resources, etc.

### `afterReset(): void`

Hook called after cached data is cleared. Override to perform post-cleanup tasks.

### `getData(): mixed`

Override this to provide the actual computation. This is where the expensive work happens.

## Reset hooks example

```php
class UserPermissions extends SingletonableTask
{
    private array $permissionCache = [];

    protected function getData(): mixed
    {
        return DB::table('permissions')->get();
    }

    protected function beforeReset(): void
    {
        $this->permissionCache = [];
    }

    protected function afterReset(): void
    {
        logger('Permission cache has been cleared');
    }
}
```

## Exception safety

- `__wakeup()` throws `FooinoRuntimeException` (code `4`) to prevent unserialization.
- `__clone()` throws `FooinoRuntimeException` (code `5`) to prevent cloning.
- If `getData()` throws, the instance remains in a retryable state — the next `run()` call re-executes `getData()` rather than returning stale data.
