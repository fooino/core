<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Support\SingletonableTask;
use RuntimeException;
use Error;

describe('SingletonableTask', function () {

    test('getInstance returns the same instance', function () {

        $instance1 = TestSingletonableTaskForTesting::getInstance();
        $instance2 = TestSingletonableTaskForTesting::getInstance();

        expect($instance1)->toBe($instance2);
    });

    test('caches data and calls getData only once per cycle', function () {

        $task = TestSingletonableTaskForTesting::getInstance();
        $task->calledCount = 0;
        $task->reset();

        expect($task->run())->toBe(['foobar']);
        expect($task->calledCount)->toBe(1);

        $task->run();
        expect($task->calledCount)->toBe(1);
    });

    test('reset clears cached data so getData is called again', function () {

        $task = TestSingletonableTaskForTesting::getInstance();
        $task->calledCount = 0;
        $task->reset();

        $task->run();
        expect($task->calledCount)->toBe(1);

        $task->reset();
        $task->run();
        expect($task->calledCount)->toBe(2);
    });

    test('beforeReset and afterReset hooks are called on reset', function () {

        $task = CacheAwareTaskForTesting::getInstance();
        $task->resetCount = 0;
        $task->reset();

        expect($task->beforeResetCalled)->toBeTrue();
        expect($task->afterResetCalled)->toBeTrue();
        expect($task->resetCount)->toBe(1);
    });

    test('beforeReset hook can invalidate external cache', function () {

        $task = CacheAwareTaskForTesting::getInstance();
        $task->resetCount = 0;
        // Simulate a cache hit that will be invalidated on reset
        $task->cacheInvalidated = false;

        expect($task->run())->toBe(['cached']);
        expect($task->cacheInvalidated)->toBeFalse();

        $task->reset();

        expect($task->cacheInvalidated)->toBeTrue();
        // After reset, getData is called again
        expect($task->run())->toBe(['cached']);
    });

    test('cannot be cloned', function () {

        $task = TestSingletonableTaskForTesting::getInstance();

        clone $task;
    })->throws(Error::class);

    test('cannot be unserialized', function () {

        $task = TestSingletonableTaskForTesting::getInstance();

        unserialize(serialize($task));
    })->throws(RuntimeException::class, 'Cannot unserialize a singleton.');
});

class TestSingletonableTaskForTesting extends SingletonableTask
{
    public int $calledCount = 0;

    public function getData(): mixed
    {
        $this->calledCount++;

        return ['foobar'];
    }
}

class CacheAwareTaskForTesting extends SingletonableTask
{
    public bool $beforeResetCalled = false;
    public bool $afterResetCalled = false;
    public int $resetCount = 0;
    public bool $cacheInvalidated = false;

    public function getData(): mixed
    {
        return ['cached'];
    }

    protected function beforeReset(): void
    {
        $this->beforeResetCalled = true;
        $this->cacheInvalidated = true;
    }

    protected function afterReset(): void
    {
        $this->afterResetCalled = true;
        $this->resetCount++;
    }
}
