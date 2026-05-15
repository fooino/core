<?php

namespace Fooino\Core\Exceptions;

use Exception;

class FooinoException extends Exception
{
    protected string $level = 'error';

    protected int $httpStatusCode = 500;

    protected array $with = [];

    protected bool $report = true;

    public function __construct(...$args)
    {
        parent::__construct(message: $this->message, code: $this->code);
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setHttpStatusCode(int $httpStatusCode): static
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function with(array $with): static
    {
        $this->with = $with;

        return $this;
    }

    public function getWith(): array
    {
        return $this->with;
    }

    public function report(bool $report): static
    {
        $this->report = $report;

        return $this;
    }

    public function reportable(): bool
    {
        return $this->report;
    }

    public function dontReport(): static
    {
        return $this->report(false);
    }

    public function shouldReport(): static
    {
        return $this->report(true);
    }

    public function instance(): FooinoException
    {
        return (new static())
            ->setMessage($this->message)
            ->setCode($this->code)
            ->setLevel($this->level)
            ->setHttpStatusCode($this->httpStatusCode)
            ->with($this->with)
            ->report($this->report);
    }

    public function throw(): never
    {
        throw $this->instance();
    }

    public function throwIf(bool $condition)
    {
        if ($condition) $this->throw();
    }

    public function log(bool $trace = true): string
    {
        $e = $this->instance();

        $log = implode('|', [get_class($e), nullIfBlank($e->getMessage(), 'empty message'), $e->getCode(), $e->getHttpStatusCode(), $e->getLevel(), jsonEncode($e->getWith())]);

        if ($trace) {
            $log .= "\n[stacktrace]\n" . $e->getTraceAsString();
        }

        return $log;
    }

    /**
     * System is unusable or inaccessible
     */
    public function emergency(): static
    {
        return $this->setLevel('emergency');
    }

    /**
     * Action must be taken immediately. the system still partially operational
     */
    public function alert(): static
    {
        return $this->setLevel('alert');
    }

    /**
     * Application component unavailable
     */
    public function critical(): static
    {
        return $this->setLevel('critical');
    }

    /**
     * Runtime errors that do not require immediate action
     */
    public function error(): static
    {
        return $this->setLevel('error');
    }

    /**
     * Exceptional occurrences that are not errors. like using deprecated API
     */
    public function warning(): static
    {
        return $this->setLevel('warning');
    }

    /**
     * Normal but significant events. like job starts
     */
    public function notice(): static
    {
        return $this->setLevel('notice');
    }

    /**
     * Interesting events like a background task done
     */
    public function info(): static
    {
        return $this->setLevel('info');
    }

    /**
     * Detailed debug information
     */
    public function debug(): static
    {
        return $this->setLevel('debug');
    }
}
