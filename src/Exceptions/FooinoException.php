<?php

namespace Fooino\Core\Exceptions;

use Exception;
use Throwable;

class FooinoException extends Exception
{
    protected FooinoException|Exception|null $cause = null;

    protected string $level = 'error';

    protected int $httpStatusCode = 500;

    protected array $with = [];

    protected array $placeholders = [];

    protected bool $report = true;

    /**
     * Use current state of message and code if they are not set at initialization
     */
    public function __construct(string $message = '', int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct(
            message: nullIfBlank(value: $message, fallback: $this->message),
            code: nullIfBlankOrZero(value: $code, fallback: $this->code),
            previous: $previous
        );
    }

    /**
     * Attach the original exception that triggered this wrapper, preserving its context for the handler
     */
    public function cause(FooinoException|Exception|null $cause): static
    {
        $this->cause = $cause;

        return $this;
    }

    /**
     * Get the original exception that was wrapped by this FooinoException
     */
    public function getCause(): FooinoException|Exception|null
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
     * Set placeholder values for translation strings passed to Laravel's __() helper
     */
    public function setPlaceholders(array $placeholders): static
    {
        $this->placeholders = $placeholders;

        return $this;
    }

    /**
     * Get the placeholder values for translation strings
     */
    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }

    /**
     * Set whether this exception should be written to the error log
     */
    public function setReport(bool $report): static
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
        return $this->setReport(report: false);
    }

    /**
     * Ensure this exception is written to the error log
     */
    public function shouldReport(): static
    {
        return $this->setReport(report: true);
    }

    /**
     * Throw this exception with its current configuration, halting execution at the throw site
     */
    public function throw(): never
    {
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

    /**
     * Attach an exception as the cause of this envelope so handlers can unwrap to the root, merging extra context into FooinoException causes
     */
    public function from(FooinoException|Exception $e, array $with = []): static
    {
        if ($e instanceof FooinoException) {

            $e
                ->with(
                    array_merge(
                        $e->getWith(),
                        $with
                    )
                );
        }

        return $this->cause($e);
    }

    /**
     * Return contextual data for Laravel's exception reporting pipeline to include in logs
     */
    public function context(): array
    {
        return $this->getWith();
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
