<?php

namespace Fooino\Core\Concerns;

use Fooino\Core\Facades\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait ApiResourceable
{
    private int $status = 200;

    private string $message = 'ok';

    private array $errors = [];

    /**
     * Override the reported HTTP status so the envelope and the response status code reflect the actual outcome of the request
     */
    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the HTTP status the envelope currently reports
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Attach a human-readable message so the envelope explains the outcome of the request
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the message the envelope currently carries
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Attach error details so the envelope can surface validation or domain failures
     */
    public function setErrors(array $errors): static
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get the errors the envelope currently carries
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Define the shared response envelope so every resource and collection exposes the same status, success, message, and errors keys
     */
    public function with(Request $request): array
    {
        $status = $this->getStatus();

        return array_merge(
            Json::responseTemplate(),
            [
                'status'        => $status,
                'success'       => $status >= 200 && $status <= 299,
                'message'       => $this->getMessage(),
                'errors'        => $this->getErrors(),
            ]
        );
    }

    /**
     * Store extra metadata under the standard additional key so the envelope mirrors the Json::respond structure
     */
    public function additional(array $data): static
    {
        $this->additional = ['additional' => $data];

        return $this;
    }

    /**
     * Synchronize the actual HTTP status code with the envelope status after the response is built
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(code: $this->getStatus());
    }
}
