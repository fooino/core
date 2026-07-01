# SingletonableTask

An abstract base class for singleton tasks that cache their computed data. Useful when you need to compute expensive data once per request cycle and serve the cached result across multiple consumers.

---

## Basic Usage

Extend SingletonableTask and implement getData():

```php
use Fooino\Core\Support\SingletonableTask;

class UserPermissionsTask extends SingletonableTask
{
    public function getData(): mixed
    {
        return auth()->user()->getAllPermissions()->pluck('name');
    }
}
```

Then use the cached result anywhere in your application:

```php
$permissions = UserPermissionsTask::getInstance()->run();
$permissions = UserPermissionsTask::getInstance()->run(); // same result as above
```

---


## How It Works

- run() calls setData() which calls getData() once and stores the result in $this->data.
- A $dataLoaded flag tracks whether data has been fetched, so null return values from getData() are also correctly cached.
- Subsequent run() calls return the cached data without re-invoking getData().
- Call reset() to clear the cache and force a fresh getData() call on the next run().

---

## Methods

### run(): mixed

Execute the task and return the cached result. On the first call it triggers getData(), on subsequent calls it returns the cached value.

```php
$task->run();
$task->run();
```

### setData(): mixed

Lazily load and cache data via getData(). The result is cached even when getData() returns null.

```php
$task->setData();
$task->setData();
```

### reset(): static

Clear the cached data so the next run() refreshes it. Calls beforeReset() and afterReset() hooks.

```php
$task->reset();
$task->run();
```

### getInstance(): static

Return the singleton instance maintained by the class itself. Uses a static array keyed by the concrete class name.

```php
$task = UserPermissionsTask::getInstance();
```

---

## Hooks

### beforeReset(): void

Called at the start of reset(), before the cached data is cleared. Override to invalidate external caches.

### afterReset(): void

Called at the end of reset(), after the cached data is cleared. Override to perform post-reset cleanup.

```php
class CachedSettingsTask extends SingletonableTask
{
    private array $cache = [];

    public function getData(): mixed
    {
        return cache()->remember('app.settings', 3600, fn() => Setting::all());
    }

    protected function beforeReset(): void
    {
        cache()->forget('app.settings');
    }

    protected function afterReset(): void
    {
        logger('Settings cache invalidated');
    }
}
```

---

## Singleton Enforcement

The class prevents multiple instances through three mechanisms:

| Mechanism | How it works |
|---|---|
| protected __construct() | Prevents new SingletonableTask() from outside the class hierarchy |
| public __wakeup() | Prevents unserialize() -- throws FooinoRuntimeException (code 4) |
| public __clone() | Prevents clone $task -- PHP throws an FooinoRuntimeException (code 5) |

---

## Exception Reference

| Code | Exception | Condition |
|---|---|---|
| 4 | FooinoRuntimeException | Attempted unserialization via __wakeup() |
| 5 | FooinoRuntimeException | Attempted clone via __clone() |

---

## Caching Behaviour

| getData() returns | First run() | Second run() | After reset() |
|---|---|---|---|
| ['key' => 'value'] | Returns data | Returns cached data (no re-call) | Clears cache, re-calls getData() |
| null | Returns null | Returns cached null (no re-call) | Clears cache, re-calls getData() |

The $dataLoaded flag ensures that null is a valid cached value -- getData() is not called again until reset().
