<?php

namespace Fooino\Core\Support;

use RuntimeException;

abstract class SingletonableTask
{
    private static array $instances = [];

    protected mixed $data;

    /**
     * Prevent direct instantiation.
     */
    protected function __construct() {}

    /**
     * Prevent cloning.
     */
    protected function __clone() {}

    /**
     * Prevent unserialization.
     */
    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize a singleton.');
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
    public function setData(): mixed
    {
        return $this->data ??= $this->getData();
    }

    /** 
     * Clear the cached data so the next run refreshes it.
     */
    public function reset(): static
    {
        $this->beforeReset();
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
