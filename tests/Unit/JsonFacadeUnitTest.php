<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Facades\Json;
use Illuminate\Http\JsonResponse;
use stdClass;

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

    test('encode value in pretty format for showing purpose', function () {

        expect(Json::encodePrettified(''))
            ->toEqual('')
            ->and(jsonEncodePrettified(''))->toEqual('');

        expect(Json::encodePrettified('null'))
            ->toEqual('')
            ->and(jsonEncodePrettified('NaN'))->toEqual('');

        expect(Json::encodePrettified('     '))
            ->toEqual('')
            ->and(jsonEncodePrettified('    '))->toEqual('');

        expect(Json::encodePrettified([]))
            ->toEqual('')
            ->and(jsonEncodePrettified([]))->toEqual('');


        expect(str_replace(["\n", " "], "", Json::encodePrettified(['foo' => 'bar'])) == '{&quot;foo&quot;:&quot;bar&quot;}')->toBeTrue();

        expect(str_replace(["\n", " "], "", Json::encodePrettified(json_encode(['foo' => 'bar']))) == '{&quot;foo&quot;:&quot;bar&quot;}')->toBeTrue();
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
            ->and(jsonDecode($int))->toBe(5);

        expect(Json::decode($float))
            ->toEqual(json_decode($float))
            ->and(jsonDecode($float))->toBe(5.5);

        expect(Json::decode($string))
            ->toEqual(json_decode($string))
            ->and(jsonDecode($string))->toBe('foo bar');

        expect(Json::decode('0'))
            ->toEqual(json_decode('0'))
            ->and(jsonDecode('0'))->toBe(0);

        expect(Json::decode('false'))
            ->toEqual(json_decode('false'))
            ->and(jsonDecode('false'))->toBe(false);

        expect(Json::decode('true'))
            ->toEqual(json_decode('true'))
            ->and(jsonDecode('true'))->toBe(true);

        expect(Json::decode('null'))
            ->toEqual(json_decode('null'))
            ->and(jsonDecode('null'))->toBe(null);

        expect(Json::decode('[]'))
            ->toEqual(json_decode('[]'))
            ->and(jsonDecode('[]'))->toBe([]);

        expect(Json::decode($null))
            ->toEqual(json_decode($null))
            ->and(jsonDecode($null))->toBe(null);

        expect(Json::decode($boolean))
            ->toEqual(json_decode($boolean))
            ->and(jsonDecode($boolean))->toBe(true);

        expect(Json::decode($array))
            ->toEqual(json_decode($array))
            ->and(jsonDecode($array, true))->toBe(['foo' => 'bar']);

        expect(Json::decode($object))
            ->toEqual(json_decode($object))
            ->and(jsonDecode($object)->foo)->toBe('bar');
    });

    test('decode method returns identical value when the value is not json', function () {

        expect(Json::decode(-5))->toBe(-5);
        expect(Json::decode(5))->toBe(5);
        expect(Json::decode(0))->toBe(0);
        expect(Json::decode(5.5))->toBe(5.5);
        expect(Json::decode(5.21E-6))->toBe(5.21E-6);

        expect(Json::decode('foo bar'))->toBe('foo bar');
        expect(Json::decode(''))->toBe('');
        expect(Json::decode('  '))->toBe('  ');

        expect(Json::decode(null))->toBe(null);
        expect(Json::decode(true))->toBe(true);
        expect(Json::decode(false))->toBe(false);

        expect(Json::decode(['foo' => 'bar']))->toBe(['foo' => 'bar']);
        expect(Json::decode([0]))->toBe([0]);
        expect(Json::decode([null]))->toBe([null]);
        expect(Json::decode([false]))->toBe([false]);
        expect(Json::decode([]))->toBe([]);

        $object = new stdClass;
        $object->foo = 'bar';

        expect(Json::decode($object)->foo)->toBe('bar');
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
            ->toBe([5])
            ->and(jsonDecodeToArray($int))->toBe([5]);

        expect(Json::decodeToArray($float))
            ->toBe([5.5])
            ->and(jsonDecodeToArray($float))->toBe([5.5]);

        expect(Json::decodeToArray($string))
            ->toBe(['foo bar'])
            ->and(jsonDecodeToArray($string))->toBe(['foo bar']);

        expect(Json::decodeToArray('0'))
            ->toBe([0])
            ->and(jsonDecodeToArray('0'))->toBe([0]);

        expect(Json::decodeToArray('null'))
            ->toBe([])
            ->and(jsonDecodeToArray('null'))->toBe([]);

        expect(Json::decodeToArray('true'))
            ->toBe([true])
            ->and(jsonDecodeToArray('true'))->toBe([true]);

        expect(Json::decodeToArray('false'))
            ->toBe([false])
            ->and(jsonDecodeToArray('false'))->toBe([false]);

        expect(Json::decodeToArray($null))
            ->toBe([])
            ->and(jsonDecodeToArray($null))->toBe([]);

        expect(Json::decodeToArray($true))
            ->toBe([true])
            ->and(jsonDecodeToArray($true))->toBe([true]);

        expect(Json::decodeToArray($false))
            ->toBe([false])
            ->and(jsonDecodeToArray($false))->toBe([false]);

        expect(Json::decodeToArray($array))
            ->toBe(['foo' => 'bar'])
            ->and(jsonDecodeToArray($array))->toBe(['foo' => 'bar']);

        expect(Json::decodeToArray($object))
            ->toBe(['foo' => 'bar'])
            ->and(jsonDecodeToArray($object))->toBe(['foo' => 'bar']);
    });

    test('decode json to array returns identical value in array format when the value is not json', function () {

        expect(Json::decodeToArray(-5))->toEqual([-5]);
        expect(Json::decodeToArray(5))->toEqual([5]);
        expect(Json::decodeToArray(0))->toEqual([0]);
        expect(Json::decodeToArray(5.5))->toEqual([5.5]);

        expect(Json::decodeToArray('foo bar'))->toEqual(['foo bar']);
        expect(Json::decodeToArray(''))->toEqual(['']);
        expect(Json::decodeToArray('  '))->toEqual(['  ']);

        expect(Json::decodeToArray(null))->toEqual([]);
        expect(Json::decodeToArray(true))->toEqual([true]);
        expect(Json::decodeToArray(false))->toEqual([false]);

        expect(Json::decodeToArray(['foo' => 'bar']))->toEqual(['foo' => 'bar']);
        expect(Json::decodeToArray([0]))->toEqual([0]);
        expect(Json::decodeToArray([null]))->toBe([null]);
        expect(Json::decodeToArray([true]))->toBe([true]);
        expect(Json::decodeToArray([false]))->toBe([false]);
        expect(Json::decodeToArray([]))->toEqual([]);

        $object = new stdClass;
        $object->foo = 'bar';

        expect(Json::decodeToArray($object)['foo'])->toEqual('bar');
    });

    test('json response return a http response with standarad structure', function () {

        expect(Json::response())->toBeInstanceOf(JsonResponse::class);
        expect(jsonResponse())->toBeInstanceOf(JsonResponse::class);

        $facade = Json::response(
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
        );

        $helper = jsonResponse(
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
        );

        $expected = response()
            ->json(
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
            );

        expect($facade->getStatusCode())->toEqual($expected->getStatusCode());
        expect($helper->getStatusCode())->toEqual($expected->getStatusCode());

        expect($facade->getData(true))->toEqual($expected->getData(true));
        expect($helper->getData(true))->toEqual($expected->getData(true));

        expect($facade->headers->all())->toEqual($expected->headers->all());
        expect($helper->headers->all())->toEqual($expected->headers->all());
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
