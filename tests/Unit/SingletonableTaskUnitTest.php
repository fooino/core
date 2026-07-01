<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Support\SingletonableTask;
use Fooino\Core\Exceptions\FooinoRuntimeException;

class TestSingletonableTask extends SingletonableTask
{
    public int $calledCount = 0;

    protected function getData(): mixed
    {
        $this->calledCount++;

        return ['foobar'];
    }
}

class NullReturnTaskForTesting extends SingletonableTask
{
    public int $calledCount = 0;

    protected function getData(): mixed
    {
        $this->calledCount++;

        return null;
    }
}

class TaskAlphaForTesting extends SingletonableTask
{
    public int $calledCount = 0;

    protected function getData(): mixed
    {
        $this->calledCount++;

        return ['alpha'];
    }
}

class TaskBetaForTesting extends SingletonableTask
{
    public int $calledCount = 0;

    protected function getData(): mixed
    {
        $this->calledCount++;

        return ['beta'];
    }
}

class CacheAwareTaskForTesting extends SingletonableTask
{
    public bool $beforeResetCalled = false;
    public bool $afterResetCalled = false;
    public int $resetCount = 0;
    public bool $cacheInvalidated = false;

    protected function getData(): mixed
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

describe('SingletonableTask', function () {

    test('instance returns the same instance', function () {

        $instance1 = TestSingletonableTask::instance();
        $instance2 = TestSingletonableTask::instance();

        expect($instance1)->toBe($instance2);
    });

    test('caches data and calls getData only once per cycle', function () {

        $task = TestSingletonableTask::instance();
        $task->calledCount = 0;
        $task->reset();

        expect($task->run())->toBe(['foobar']);
        expect($task->calledCount)->toBe(1);

        $task->run();
        expect($task->calledCount)->toBe(1);

        $task->run();
        expect($task->calledCount)->toBe(1);
    });

    test('reset clears cached data so getData is called again', function () {

        $task = TestSingletonableTask::instance();
        $task->calledCount = 0;
        $task->reset();

        $task->run();
        expect($task->calledCount)->toBe(1);

        $task->reset();
        $task->run();
        expect($task->calledCount)->toBe(2);

        $task->run();
        expect($task->calledCount)->toBe(2);

        $task->reset();
        $task->run();
        expect($task->calledCount)->toBe(3);
    });

    test('beforeReset and afterReset hooks are called on reset', function () {

        $task = CacheAwareTaskForTesting::instance();
        $task->resetCount = 0;
        $task->reset();

        expect($task->beforeResetCalled)->toBeTrue();
        expect($task->afterResetCalled)->toBeTrue();
        expect($task->resetCount)->toBe(1);
    });

    test('beforeReset hook can invalidate external cache', function () {

        $task = CacheAwareTaskForTesting::instance();
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

    test('getData returning null is cached and does not run on every call', function () {

        $task = NullReturnTaskForTesting::instance();
        $task->calledCount = 0;
        $task->reset();

        expect($task->run())->toBe(null);
        expect($task->calledCount)->toBe(1);

        $task->run();
        expect($task->calledCount)->toBe(1);

        $task->run();
        expect($task->calledCount)->toBe(1);
    });

    test('independent subclasses each have their own singleton', function () {

        $alpha = TaskAlphaForTesting::instance();
        $beta = TaskBetaForTesting::instance();

        expect($alpha)->not->toBe($beta);

        $alpha->reset();
        $beta->reset();

        $alpha->calledCount = 0;
        $beta->calledCount = 0;

        $alpha->run();
        expect($alpha->calledCount)->toBe(1);
        expect($alpha->run())->toBe(['alpha']);

        $beta->run();
        expect($beta->calledCount)->toBe(1);
        expect($beta->run())->toBe(['beta']);

        $alpha->reset();

        expect($alpha->calledCount)->toBe(1);
        expect($beta->calledCount)->toBe(1);

        $alpha->run();
        expect($alpha->calledCount)->toBe(2);
        expect($beta->calledCount)->toBe(1);

        $alpha->run();
        expect($alpha->calledCount)->toBe(2);
    });

    describe('handle exceptions', function () {

        test('cannot be unserialized', function () {

            expect(fn() => unserialize(serialize(TestSingletonableTask::instance())))->toThrow(FooinoRuntimeException::class, 'msg.fooinoRunTimeExceptionCannotUnserializeSingleton');

            try {

                unserialize(serialize(TestSingletonableTask::instance()));

                //
            } catch (FooinoRuntimeException $e) {

                expect($e->getMessage())->toBe('msg.fooinoRunTimeExceptionCannotUnserializeSingleton');
                expect($e->getCode())->toBe(4);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([]);
            }
        });

        test('cannot be cloned', function () {

            $task = TestSingletonableTask::instance();

            expect(fn() => clone $task)->toThrow(FooinoRuntimeException::class, 'msg.fooinoRunTimeExceptionCannotCloneSingleton');

            try {

                clone $task;

                //
            } catch (FooinoRuntimeException $e) {

                expect($e->getMessage())->toBe('msg.fooinoRunTimeExceptionCannotCloneSingleton');
                expect($e->getCode())->toBe(5);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([]);
            }
        });
    });
});
