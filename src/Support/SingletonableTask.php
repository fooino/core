<?php

namespace Fooino\Core\Support;

use Fooino\Core\Exceptions\FooinoRuntimeException;

abstract class SingletonableTask
{
    private static array $instances = [];

    private bool $dataLoaded = false;

    protected mixed $data;

    /**
     * Prevent direct instantiation.
     */
    protected function __construct() {}

    /**
     * Prevent unserialization.
     */
    public function __wakeup(): never
    {
        app(FooinoRuntimeException::class)->_4()->throw();
    }

    /**
     * Prevent cloning.
     */
    public function __clone(): never
    {
        app(FooinoRuntimeException::class)->_5()->throw();
    }

    /**
     * Return the singleton instance.
     */
    public static function getInstance(): static
    {
        return self::$instances[static::class] ??= new static();
    }

    /**
     * Execute the task and return the cached result.
     */
    public function run(): mixed
    {
        $this->setData();

        return $this->data;
    }

    /**
     * Lazily load and cache data via getData.
     */
    protected function setData(): mixed
    {
        if (!$this->dataLoaded) {

            $this->data = $this->getData();

            $this->dataLoaded = true;
        }

        return $this->data;
    }

    /**
     * Clear the cached data so the next run refreshes it.
     */
    public function reset(): static
    {
        $this->beforeReset();

        $this->dataLoaded = false;
        $this->data = null;

        $this->afterReset();

        return $this;
    }

    /**
     * Hook called before the cached data is cleared.
     */
    protected function beforeReset(): void {}

    /**
     * Hook called after the cached data is cleared.
     */
    protected function afterReset(): void {}

    /**
     * Compute the task data. Must be implemented by subclasses.
     */
    abstract public function getData(): mixed;
}
