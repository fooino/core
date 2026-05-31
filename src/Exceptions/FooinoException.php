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
        // use current state of message and code(the initial value in class property or already setted by setMessage, setCode methods)
        parent::__construct(message: $this->message, code: $this->code);
    }

    /**
     * Message Setter
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Code Setter
     */
    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Level Setter
     */
    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Level Getter
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * Http status code Setter
     */
    public function setHttpStatusCode(int $httpStatusCode): static
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }

    /**
     * Http status code Getter
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * The attached data to exception Setter
     */
    public function with(array $with): static
    {
        $this->with = $with;

        return $this;
    }

    /**
     * The attached data to exception Getter
     */
    public function getWith(): array
    {
        return $this->with;
    }

    /**
     * Report or skip the exception log
     */
    public function report(bool $report): static
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Exception log is reportable or not
     */
    public function reportable(): bool
    {
        return $this->report;
    }

    /**
     * Do not Report the exception log
     */
    public function dontReport(): static
    {
        return $this->report(false);
    }

    /**
     * Report the exception log
     */
    public function shouldReport(): static
    {
        return $this->report(true);
    }

    /**
     * Throw the exception
     */
    public function throw(): never
    {
        $this
            ->setMessage($this->getMessage())
            ->setCode($this->getCode())
            ->setLevel($this->getLevel())
            ->setHttpStatusCode($this->getHttpStatusCode())
            ->with($this->getWith())
            ->report($this->reportable());

        throw $this;
    }

    /**
     * Log message with trace
     */
    public function log(bool $trace = true): string
    {
        $log = implode(
            '|',
            [
                get_class($this),
                nullIfBlank(value: $this->getMessage(), fallback: 'empty message'),
                $this->getCode(),
                $this->getHttpStatusCode(),
                $this->getLevel(),
                jsonEncode($this->getWith())
            ]
        );

        if ($trace) {
            $log .= "\n[stacktrace]\n" . $this->getTraceAsString();
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
