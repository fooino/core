# ApiResourceable

A trait for Laravel API resources that standardizes every `JsonResource` and `ResourceCollection` response behind the same envelope structure used by `Json::respond()`: `status`, `success`, `message`, `errors`, `data`, and `additional`.

## Envelope Structure

Every response built with the trait follows the same shape:

```json
{
    "status": 200,
    "success": true,
    "message": "ok",
    "errors": [],
    "data": {},
    "additional": []
}
```

- `status` — the HTTP status the envelope reports (default `200`)
- `success` — automatically derived from `status`: `true` for any 2xx status, `false` otherwise
- `message` — a human-readable outcome (default `ok`)
- `errors` — validation or domain errors (default `[]`)
- `data` — the resource payload produced by `toArray()`
- `additional` — extra metadata passed through `additional()` (default `[]`)

The envelope template comes from `Json::responseTemplate()`, so resources, collections, and `Json::respond()` always agree on the standard structure.

## Usage

### Resource

Use the trait on any `JsonResource`:

```php
use Fooino\Core\Concerns\ApiResourceable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use ApiResourceable;

    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
        ];
    }
}
```

```php
return UserResource::make($user)
    ->setStatus(status: 201)
    ->setMessage(message: 'created')
    ->setErrors(errors: ['foo' => 'bar'])
    ->additional(data: ['foo' => 'bar']);
```

Response:

```json
{
    "data": {
        "id": 1,
        "name": "fooino"
    },
    "status": 201,
    "success": true,
    "message": "created",
    "errors": {
        "foo": "bar"
    },
    "additional": {
        "foo": "bar"
    }
}
```

### Collection

Use the same trait on `ResourceCollection` subclasses:

```php
use Fooino\Core\Concerns\ApiResourceable;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    use ApiResourceable;
}
```

```php
return UserCollection::make(UserResource::collection($users))
    ->setStatus(status: 201)
    ->setMessage(message: 'created');
```

Response:

```json
{
    "data": [
        {
            "id": 1,
            "name": "fooino"
        },
        {
            "id": 2,
            "name": "foo"
        }
    ],
    "status": 201,
    "success": true,
    "message": "created",
    "errors": [],
    "additional": []
}
```

## Methods

All setters are fluent and return `static` for chaining.

### setStatus(int $status): static

Sets the envelope `status` field. The actual HTTP status code of the response is synchronized with it automatically (via the `withResponse` hook), so `setStatus(404)` produces both a `status: 404` envelope and a real HTTP 404.

### getStatus(): int

Returns the status the envelope currently reports (default `200`).

### setMessage(string $message): static

Attaches a human-readable message that explains the outcome of the request.

### getMessage(): string

Returns the message the envelope currently carries (default `ok`).

### setErrors(array $errors): static

Attaches error details, typically validation or domain failures, to the envelope.

### getErrors(): array

Returns the errors the envelope currently carries (default `[]`).

### additional(array $data): static

Attaches extra metadata under the top-level `additional` key, mirroring the `Json::respond()` structure.

## Success Derivation

The `success` field is derived from the status code: `true` for any 2xx status (200–299), `false` otherwise. This matches the HTTP standard response classes (RFC 9110) and stays consistent with `Json::respond()`:

- `2xx` → `true` (successful)
- `3xx`, `4xx`, `5xx` → `false` (redirection, client error, server error)
