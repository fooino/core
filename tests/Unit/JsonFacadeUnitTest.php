<?php

use Fooino\Core\Facades\Json;
use Illuminate\Http\JsonResponse;

describe('Json facade using FooinoJsonHandler', function () {

    test('is method returns false', function () {

        $int = 5;
        $float = 5.5;
        $string = 'foo bar';
        $null = null;
        $boolean = true;
        $array = ['foo' => 'bar'];
        $object = new stdClass;
        $object->foo = 'bar';

        expect(Json::is($int))
            ->toBeFalse()
            ->and(isJson($int))->toBeFalse()
            ->and($int)->not->toBeJson();

        expect(Json::is($float))
            ->toBeFalse()
            ->and(isJson($float))->toBeFalse()
            ->and($float)->not->toBeJson();

        expect(Json::is($string))
            ->toBeFalse()
            ->and(isJson($string))->toBeFalse()
            ->and($string)->not->toBeJson();

        expect(Json::is($null))
            ->toBeFalse()
            ->and(isJson($null))->toBeFalse()
            ->and($null)->not->toBeJson();

        expect(Json::is($boolean))
            ->toBeFalse()
            ->and(isJson($boolean))->toBeFalse()
            ->and($boolean)->not->toBeJson();

        expect(Json::is($array))
            ->toBeFalse()
            ->and(isJson($array))->toBeFalse()
            ->and($array)->not->toBeJson();

        expect(Json::is($object))
            ->toBeFalse()
            ->and(isJson($object))->toBeFalse()
            ->and($object)->not->toBeJson();
    });

    test('is method returns true', function () {

        $int = json_encode(5);
        $float = json_encode(5.5);
        $string = json_encode('foo bar');
        $null = json_encode(null);
        $boolean = json_encode(false);
        $array = json_encode(['foo' => 'bar']);
        $object = new stdClass;
        $object->foo = 'bar';
        $object = json_encode($object);

        expect(Json::is($int))
            ->toBeTrue()
            ->and(isJson($int))->toBeTrue()
            ->and($int)->toBeJson();

        expect(Json::is($float))
            ->toBeTrue()
            ->and(isJson($float))->toBeTrue()
            ->and($float)->toBeJson();

        expect(Json::is($string))
            ->toBeTrue()
            ->and(isJson($string))->toBeTrue()
            ->and($string)->toBeJson();

        expect(Json::is($null))
            ->toBeTrue()
            ->and(isJson($null))->toBeTrue()
            ->and($null)->toBeJson();

        expect(Json::is($boolean))
            ->toBeTrue()
            ->and(isJson($boolean))->toBeTrue()
            ->and($boolean)->toBeJson();

        expect(Json::is($array))
            ->toBeTrue()
            ->and(isJson($array))->toBeTrue()
            ->and($array)->toBeJson();

        expect(Json::is($object))
            ->toBeTrue()
            ->and(isJson($object))->toBeTrue()
            ->and($object)->toBeJson();
    });

    test('encode method returns json', function () {

        $int = 5;
        $float = 5.5;
        $string = 'foo bar';
        $null = null;
        $boolean = true;
        $array = ['foo' => 'bar'];
        $object = new stdClass;
        $object->foo = 'bar';

        expect(Json::encode($int))
            ->toEqual(json_encode($int))
            ->and(jsonEncode($int))->toBeJson();

        expect(Json::encode($float))
            ->toEqual(json_encode($float))
            ->and(jsonEncode($float))->toBeJson();

        expect(Json::encode($string))
            ->toEqual(json_encode($string))
            ->and(jsonEncode($string))->toBeJson();

        expect(Json::encode($null))
            ->toEqual(json_encode($null))
            ->and(jsonEncode($null))->toBeJson();

        expect(Json::encode($boolean))
            ->toEqual(json_encode($boolean))
            ->and(jsonEncode($boolean))->toBeJson();

        expect(Json::encode($array))
            ->toEqual(json_encode($array))
            ->and(jsonEncode($array))->toBeJson();

        expect(jsonEncode(Json::encode(Json::encode($array))))
            ->toEqual(json_encode($array));

        expect(Json::encode($object))
            ->toEqual(json_encode($object))
            ->and(jsonEncode($object))->toBeJson();
    });

    test('decode method returns well-formatted value', function () {

        $int = json_encode(5);
        $float = json_encode(5.5);
        $string = json_encode('foo bar');
        $null = json_encode(null);
        $boolean = json_encode(true);
        $array = json_encode(['foo' => 'bar']);
        $object = new stdClass;
        $object->foo = 'bar';
        $object = json_encode($object);

        expect(Json::decode($int))
            ->toEqual(json_decode($int))
            ->and(jsonDecode($int))->toEqual(5);

        expect(Json::decode($float))
            ->toEqual(json_decode($float))
            ->and(jsonDecode($float))->toEqual(5.5);

        expect(Json::decode($string))
            ->toEqual(json_decode($string))
            ->and(jsonDecode($string))->toEqual('foo bar');

        expect(Json::decode($null))
            ->toEqual(json_decode($null))
            ->and(jsonDecode($null))->toEqual(null);

        expect(Json::decode($boolean))
            ->toEqual(json_decode($boolean))
            ->and(jsonDecode($boolean))->toEqual(true);

        expect(Json::decode($array))
            ->toEqual(json_decode($array))
            ->and(jsonDecode($array, true))->toEqual(['foo' => 'bar']);

        expect(Json::decode($object))
            ->toEqual(json_decode($object))
            ->and(jsonDecode($object)->foo)->toEqual('bar');
    });

    test('decode json to array format', function () {

        $int = json_encode(5);
        $float = json_encode(5.5);
        $string = json_encode('foo bar');
        $null = json_encode(null);
        $true = json_encode(true);
        $false = json_encode(false);
        $array = json_encode(['foo' => 'bar']);
        $object = new stdClass;
        $object->foo = 'bar';
        $object = json_encode($object);

        expect(Json::decodeToArray($int))
            ->toEqual([5])
            ->and(jsonDecodeToArray($int))->toEqual([5]);

        expect(Json::decodeToArray($float))
            ->toEqual([5.5])
            ->and(jsonDecodeToArray($float))->toEqual([5.5]);

        expect(Json::decodeToArray($string))
            ->toEqual(['foo bar'])
            ->and(jsonDecodeToArray($string))->toEqual(['foo bar']);

        expect(Json::decodeToArray($null))
            ->toEqual([])
            ->and(jsonDecodeToArray($null))->toEqual([]);

        expect(Json::decodeToArray($true))
            ->toEqual([true])
            ->and(jsonDecodeToArray($true))->toEqual([true]);

        expect(Json::decodeToArray($false))
            ->toEqual([false])
            ->and(jsonDecodeToArray($false))->toEqual([false]);

        expect(Json::decodeToArray($array))
            ->toEqual(['foo' => 'bar'])
            ->and(jsonDecodeToArray($array))->toEqual(['foo' => 'bar']);

        expect(Json::decodeToArray($object))
            ->toEqual(['foo' => 'bar'])
            ->and(jsonDecodeToArray($object))->toEqual(['foo' => 'bar']);
    });

    test('json response return a http response with standarad structure', function () {

        expect(Json::response() instanceof JsonResponse)
            ->and(jsonResponse() instanceof JsonResponse)
            ->toBeTrue();

        expect(
            Json::response(
                status: 429,
                message: 'too many request',
                errors: [
                    'foo'       => 'bar'
                ],
                data: [
                    'foo'       => 'bar'
                ],
                additional: [
                    'bypass'    => false
                ],
                headers: [
                    'accept'    => 'application/json'
                ]
            ),
        )
            ->and(
                jsonResponse(
                    status: 429,
                    message: 'too many request',
                    errors: [
                        'foo'       => 'bar'
                    ],
                    data: [
                        'foo'       => 'bar'
                    ],
                    additional: [
                        'bypass'    => false
                    ],
                    headers: [
                        'accept'    => 'application/json'
                    ]
                ),
            )
            ->toEqual(
                response()->json(
                    data: [
                        'status'    => 429,
                        'success'   => false,
                        'message'   => 'too many request',
                        'errors'    => [
                            'foo'   => 'bar'
                        ],
                        'data'      => [
                            'foo' => 'bar'
                        ],
                        'additional'    => [
                            'bypass'    => false
                        ],
                    ],
                    status: 429,
                    headers: [
                        'accept'    => 'application/json'
                    ]
                )
            );
    });

    test('check json response template', function () {

        expect(Json::responseTemplate())->toEqual([
            'status'        => 200,
            'success'       => true,
            'message'       => '',
            'errors'        => [],
            'data'          => [],
            'additional'    => []
        ]);
    });
});
