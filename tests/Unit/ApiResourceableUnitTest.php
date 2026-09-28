<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Concerns\ApiResourceable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use stdClass;

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

class UserCollection extends ResourceCollection
{
    use ApiResourceable;
}

describe('ApiResourceable trait', function () {

    test('status setter and getter', function () {

        $resource = UserResource::make(new stdClass);

        expect($resource->getStatus())->toBe(200);

        expect($resource->setStatus(status: 201))->toBe($resource)
            ->and($resource->getStatus())->toBe(201);
    });

    test('message setter and getter', function () {

        $resource = UserResource::make(new stdClass);

        expect($resource->getMessage())->toBe('ok');

        expect($resource->setMessage(message: 'created'))->toBe($resource)
            ->and($resource->getMessage())->toBe('created');
    });

    test('errors setter and getter', function () {

        $resource = UserResource::make(new stdClass);

        expect($resource->getErrors())->toBe([]);

        $errors = [
            'name'      => ['The name field is required.'],
        ];

        expect($resource->setErrors(errors: $errors))->toBe($resource)
            ->and($resource->getErrors())->toBe($errors);
    });

    test('additional setter and getter', function () {

        $resource = UserResource::make(new stdClass);

        expect($resource->additional(data: ['foo' => 'bar']))->toBe($resource)
            ->and($resource->additional)->toBe(['additional' => ['foo' => 'bar']]);
    });

    test('with method returns the standard envelope filled with the current values', function () {

        $with = UserResource::make(new stdClass)
            ->setStatus(status: 422)
            ->setMessage(message: 'validation failed')
            ->setErrors(errors: ['name' => ['The name field is required.']])
            ->with(request());

        expect($with)->toBe([
            'status'        => 422,
            'success'       => false,
            'message'       => 'validation failed',
            'errors'        => ['name' => ['The name field is required.']],
            'data'          => [],
            'additional'    => [],
        ]);
    });

    test('with method returns the envelope defaults when nothing is customized', function () {

        expect(UserResource::make(new stdClass)->with(request()))->toBe([
            'status'        => 200,
            'success'       => true,
            'message'       => 'ok',
            'errors'        => [],
            'data'          => [],
            'additional'    => [],
        ]);
    });

    test('user resource response follows the standard envelope structure', function () {

        $user = new stdClass;
        $user->id = 1;
        $user->name = 'fooino';

        $response = UserResource::make($user)
            ->setStatus(status: 201)
            ->setMessage(message: 'created')
            ->setErrors(errors: ['foo' => 'bar'])
            ->additional(data: ['foo' => 'bar'])
            ->toResponse(request());

        expect($response->getStatusCode())->toBe(201)
            ->and($response->getData(true))->toBe([
                'data'          => [
                    'id'    => 1,
                    'name'  => 'fooino',
                ],
                'status'        => 201,
                'success'       => true,
                'message'       => 'created',
                'errors'        => ['foo' => 'bar'],
                'additional'    => ['foo' => 'bar'],
            ]);
    });

    test('user resource response marks success as false for error statuses', function () {

        $user = new stdClass;
        $user->id = 1;
        $user->name = 'fooino';

        $response = UserResource::make($user)
            ->setStatus(status: 404)
            ->setMessage(message: 'not found')
            ->toResponse(request());

        $data = $response->getData(true);

        expect($response->getStatusCode())->toBe(404)
            ->and($data['status'])->toBe(404)
            ->and($data['success'])->toBeFalse()
            ->and($data['message'])->toBe('not found');
    });

    test('user collection response follows the standard envelope structure', function () {

        $first = new stdClass;
        $first->id = 1;
        $first->name = 'fooino';

        $second = new stdClass;
        $second->id = 2;
        $second->name = 'foo';

        $response = UserCollection::make(collect([$first, $second]))
            ->setStatus(status: 201)
            ->setMessage(message: 'created')
            ->setErrors(errors: ['foo' => 'bar'])
            ->additional(data: ['foo' => 'bar'])
            ->toResponse(request());

        expect($response->getStatusCode())->toBe(201)
            ->and($response->getData(true))->toBe([
                'data'          => [
                    [
                        'id'    => 1,
                        'name'  => 'fooino',
                    ],
                    [
                        'id'    => 2,
                        'name'  => 'foo',
                    ],
                ],
                'status'        => 201,
                'success'       => true,
                'message'       => 'created',
                'errors'        => ['foo' => 'bar'],
                'additional'    => ['foo' => 'bar'],
            ]);
    });
});
