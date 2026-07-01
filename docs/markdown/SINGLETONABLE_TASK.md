# SingletonableTask

An abstract base class that implements the **singleton + memoization** pattern for one-shot tasks. Each subclass is automatically a singleton — `instance()` always returns the same object — and `run()` lazily computes the result exactly once, caching it for all subsequent calls until `reset()` clears it.

## When to use

Use `SingletonableTask` when you have a unit of work that:

- Should be computed **at most once per request/cycle**
- Returns the **same result** every time it's called within a cycle
- Needs a simple **reset mechanism** to force re-computation

Common examples: loading configuration, fetching a remote resource, computing a derived value, building a lookup map.

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
