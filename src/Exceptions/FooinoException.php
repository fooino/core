<?php

namespace Fooino\Core\Exceptions;

use Exception;

class FooinoException extends Exception
{
    use FooinoExceptionList;

    protected Exception|null $cause = null;

    protected string $level = 'error';

    protected int $httpStatusCode = 500;

    protected array $with = [];

    protected bool $report = true;

    /**
     * Use current state of message and code(the initial value in class property or already setted by setMessage, setCode methods)
     */
    public function __construct(...$args)
    {
        parent::__construct(message: $this->message, code: $this->code);
    }

    /**
     * Attach the original exception that triggered this wrapper, preserving its context for the handler
     */
    public function cause(Exception|null $cause): static
    {
        $this->cause = $cause;

        return $this;
    }

    /**
     * Get the original exception that was wrapped by this FooinoException
     */
    public function getCause(): Exception|null
    {
        return $this->cause;
    }

    /**
     * Override the exception message for customized error output
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the unique error code for this exception type
     */
    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set the severity level for log handlers to categorize the error
     */
    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get the severity level for log handlers
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * Set the HTTP status code that should be returned with the error response
     */
    public function setHttpStatusCode(int $httpStatusCode): static
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }

    /**
     * Get the HTTP status code for the error response
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Attach contextual data to the exception for debugging and log enrichment
     */
    public function with(array $with): static
    {
        $this->with = $with;

        return $this;
    }

    /**
     * Get the contextual data attached to the exception
     */
    public function getWith(): array
    {
        return $this->with;
    }

    /**
     * Set whether this exception should be written to the error log
     */
    public function report(bool $report): static
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Check whether this exception should be written to the error log
     */
    public function reportable(): bool
    {
        return $this->report;
    }

    /**
     * Suppress this exception from being logged
     */
    public function dontReport(): static
    {
        return $this->report(report: false);
    }

    /**
     * Ensure this exception is written to the error log
     */
    public function shouldReport(): static
    {
        return $this->report(report: true);
    }

    /**
     * Apply all current properties and throw the exception
     */
    public function throw(): never
    {
        $this
            ->cause($this->getCause())
            ->setMessage($this->getMessage())
            ->setCode($this->getCode())
            ->setLevel($this->getLevel())
            ->setHttpStatusCode($this->getHttpStatusCode())
            ->with($this->getWith())
            ->report($this->reportable());

        throw $this;
    }

    /**
     * Serialize the exception into a pipe-delimited log line, optionally with a stack trace
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

    public function from(Exception $e, array $with = []): static
    {
        return $this
            ->cause($e)
            ->setMessage($e->getMessage())
            ->setCode($e->getCode())
            ->setLevel(callMethodIfExists(object: $e, method: 'getLevel', fallback: 'error'))
            ->setHttpStatusCode(callMethodIfExists(object: $e, method: 'getHttpStatusCode', fallback: 500))
            ->report(callMethodIfExists(object: $e, method: 'reportable', fallback: true))
            ->with(array_merge(
                callMethodIfExists(object: $e, method: 'getWith', fallback: []),
                $with
            ));
    }

    /**
     * Set the exception level to the highest severity: system is unusable
     */
    public function emergency(): static
    {
        return $this->setLevel(level: 'emergency');
    }

    /**
     * Set the exception level to alert: action must be taken immediately
     */
    public function alert(): static
    {
        return $this->setLevel(level: 'alert');
    }

    /**
     * Set the exception level to critical: application component unavailable
     */
    public function critical(): static
    {
        return $this->setLevel(level: 'critical');
    }

    /**
     * Set the exception level to error: runtime errors that do not require immediate action
     */
    public function error(): static
    {
        return $this->setLevel(level: 'error');
    }

    /**
     * Set the exception level to warning: exceptional occurrences that are not errors
     */
    public function warning(): static
    {
        return $this->setLevel(level: 'warning');
    }

    /**
     * Set the exception level to notice: normal but significant events
     */
    public function notice(): static
    {
        return $this->setLevel(level: 'notice');
    }

    /**
     * Set the exception level to info: interesting events like background task completion
     */
    public function info(): static
    {
        return $this->setLevel(level: 'info');
    }

    /**
     * Set the exception level to debug: detailed diagnostic information
     */
    public function debug(): static
    {
        return $this->setLevel(level: 'debug');
    }
}
