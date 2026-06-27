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
        $true = true;
        $false = false;
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

        expect(Json::is($true))
            ->toBeFalse()
            ->and(isJson($true))->toBeFalse()
            ->and($true)->not->toBeJson();

        expect(Json::is($false))
            ->toBeFalse()
            ->and(isJson($false))->toBeFalse()
            ->and($false)->not->toBeJson();

        expect(Json::is($array))
            ->toBeFalse()
            ->and(isJson($array))->toBeFalse()
            ->and($array)->not->toBeJson();

        expect(Json::is($object))
            ->toBeFalse()
            ->and(isJson($object))->toBeFalse()
            ->and($object)->not->toBeJson();

        expect(Json::is(''))
            ->toBeFalse()
            ->and(isJson(''))->toBeFalse()
            ->and('')->not->toBeJson();
    });

    test('is method returns true', function () {

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

        expect(Json::is($true))
            ->toBeTrue()
            ->and(isJson($true))->toBeTrue()
            ->and($true)->toBeJson();

        expect(Json::is($false))
            ->toBeTrue()
            ->and(isJson($false))->toBeTrue()
            ->and($false)->toBeJson();

        expect(Json::is($array))
            ->toBeTrue()
            ->and(isJson($array))->toBeTrue()
            ->and($array)->toBeJson();

        expect(Json::is($object))
            ->toBeTrue()
            ->and(isJson($object))->toBeTrue()
            ->and($object)->toBeJson();

        expect(Json::is('0'))
            ->toBeTrue()
            ->and(isJson('0'))->toBeTrue()
            ->and('0')->toBeJson();

        expect(Json::is('5'))
            ->toBeTrue()
            ->and(isJson('5'))->toBeTrue()
            ->and('5')->toBeJson();

        expect(Json::is('5.5'))
            ->toBeTrue()
            ->and(isJson('5.5'))->toBeTrue()
            ->and('5.5')->toBeJson();

        expect(Json::is('true'))
            ->toBeTrue()
            ->and(isJson('true'))->toBeTrue()
            ->and('true')->toBeJson();

        expect(Json::is('false'))
            ->toBeTrue()
            ->and(isJson('false'))->toBeTrue()
            ->and('false')->toBeJson();

        expect(Json::is('null'))
            ->toBeTrue()
            ->and(isJson('null'))->toBeTrue()
            ->and('null')->toBeJson();

        expect(Json::is('[]'))
            ->toBeTrue()
            ->and(isJson('[]'))->toBeTrue()
            ->and('[]')->toBeJson();

        expect(Json::is('{}'))
            ->toBeTrue()
            ->and(isJson('{}'))->toBeTrue()
            ->and('{}')->toBeJson();
    });

    test('encode method returns json', function () {

        $int = 5;
        $float = 5.5;
        $string = 'foo bar';
        $null = null;
        $true = true;
        $false = false;
        $array = ['foo' => 'bar'];
        $object = new stdClass;
        $object->foo = 'bar';

        expect(Json::encode($int))
            ->toBe(json_encode($int))
            ->and(jsonEncode($int))->toBeJson();

        expect(Json::encode($float))
            ->toBe(json_encode($float))
            ->and(jsonEncode($float))->toBeJson();

        expect(Json::encode($string))
            ->toBe(json_encode($string))
            ->and(jsonEncode($string))->toBeJson();

        expect(Json::encode($null))
            ->toBe(json_encode($null))
            ->and(jsonEncode($null))->toBeJson();

        expect(Json::encode($true))
            ->toBe(json_encode($true))
            ->and(jsonEncode($true))->toBeJson();

        expect(Json::encode($false))
            ->toBe(json_encode($false))
            ->and(jsonEncode($false))->toBeJson();

        expect(Json::encode($array))
            ->toBe(json_encode($array))
            ->and(jsonEncode($array))->toBeJson();

        expect(Json::encode($object))
            ->toBe(json_encode($object))
            ->and(jsonEncode($object))->toBeJson();
    });

    test('encode method does not re-encode valid JSON', function () {

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

        expect(Json::encode($int))->toBe($int);
        expect(jsonEncode($int))->toBe($int);

        expect(Json::encode($float))->toBe($float);
        expect(jsonEncode($float))->toBe($float);

        expect(Json::encode($string))->toBe($string);
        expect(jsonEncode($string))->toBe($string);

        expect(Json::encode($null))->toBe($null);
        expect(jsonEncode($null))->toBe($null);

        expect(Json::encode($true))->toBe($true);
        expect(jsonEncode($true))->toBe($true);

        expect(Json::encode($false))->toBe($false);
        expect(jsonEncode($false))->toBe($false);

        expect(Json::encode($array))->toBe($array);
        expect(jsonEncode($array))->toBe($array);

        expect(Json::encode($object))->toBe($object);
        expect(jsonEncode($object))->toBe($object);

        $array = ['foo' => 'bar'];
        expect(jsonEncode(Json::encode(Json::encode($array))))
            ->toBe(json_encode($array));
    });

    test('encode value in pretty format for showing purpose', function () {

        expect(Json::encodePretty(value: ''))
            ->toBe('')
            ->and(jsonEncodePretty(value: ''))->toBe('');

        expect(Json::encodePretty(value: 'null'))
            ->toBe('')
            ->and(jsonEncodePretty(value: 'NaN'))->toBe('');

        expect(Json::encodePretty(value: '     '))
            ->toBe('')
            ->and(jsonEncodePretty(value: '    '))->toBe('');

        expect(Json::encodePretty(value: []))
            ->toBe('')
            ->and(jsonEncodePretty(value: []))->toBe('');

        expect(removeWhitespace(value: Json::encodePretty(value: ['foo' => 'bar'])))->toBe('{&quot;foo&quot;:&quot;bar&quot;}');

        expect(removeWhitespace(value: Json::encodePretty(value: json_encode(['foo' => 'bar']))))->toBe('{&quot;foo&quot;:&quot;bar&quot;}');

        expect(removeWhitespace(value: jsonEncodePretty(value: '5')))->toBe('[5]');
        expect(removeWhitespace(value: jsonEncodePretty(value: 5)))->toBe('[5]');

        expect(removeWhitespace(value: jsonEncodePretty(value: '5.5')))->toBe('[5.5]');
        expect(removeWhitespace(value: jsonEncodePretty(value: 5.5)))->toBe('[5.5]');

        expect(removeWhitespace(value: jsonEncodePretty(value: '-5.5')))->toBe('[-5.5]');
        expect(removeWhitespace(value: jsonEncodePretty(value: -5.5)))->toBe('[-5.5]');

        expect(removeWhitespace(value: jsonEncodePretty(value: ['5'])))->toBe('[&quot;5&quot;]');
        expect(removeWhitespace(value: jsonEncodePretty(value: [5])))->toBe('[5]');

        expect(removeWhitespace(value: jsonEncodePretty(value: ['5.5'])))->toBe('[&quot;5.5&quot;]');
        expect(removeWhitespace(value: jsonEncodePretty(value: [5.5])))->toBe('[5.5]');

        expect(removeWhitespace(value: jsonEncodePretty(value: ['-5.5'])))->toBe('[&quot;-5.5&quot;]');
        expect(removeWhitespace(value: jsonEncodePretty(value: [-5.5])))->toBe('[-5.5]');

        expect(removeWhitespace(value: jsonEncodePretty(value: 'foobar')))->toBe('&quot;foobar&quot;');
        expect(removeWhitespace(value: jsonEncodePretty(value: ["foobar"])))->toBe('[&quot;foobar&quot;]');
    });

    test('decode method returns well-formatted value', function () {

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

        expect(Json::decode($int))
            ->toBe(json_decode($int))
            ->and(jsonDecode($int))->toBe(5);

        expect(Json::decode($float))
            ->toBe(json_decode($float))
            ->and(jsonDecode($float))->toBe(5.5);

        expect(Json::decode($string))
            ->toBe(json_decode($string))
            ->and(jsonDecode($string))->toBe('foo bar');



        expect(Json::decode($null))
            ->toBe(json_decode($null))
            ->and(jsonDecode($null))->toBe(null);

        expect(Json::decode($true))
            ->toBe(json_decode($true))
            ->and(jsonDecode($true))->toBe(true);

        expect(Json::decode($false))
            ->toBe(json_decode($false))
            ->and(jsonDecode($false))->toBe(false);

        expect(Json::decode($array))
            ->toEqual(json_decode($array))
            ->and(jsonDecode($array, true))->toBe(['foo' => 'bar']);

        expect(Json::decode($object))
            ->toEqual(json_decode($object))
            ->and(jsonDecode($object)->foo)->toBe('bar');


        expect(Json::decode('0'))
            ->toBe(json_decode('0'))
            ->and(jsonDecode('0'))->toBe(0);

        expect(Json::decode('5'))
            ->toBe(json_decode('5'))
            ->and(jsonDecode('5'))->toBe(5);

        expect(Json::decode('5.5'))
            ->toBe(json_decode('5.5'))
            ->and(jsonDecode('5.5'))->toBe(5.5);

        expect(Json::decode('false'))
            ->toBe(json_decode('false'))
            ->and(jsonDecode('false'))->toBe(false);

        expect(Json::decode('true'))
            ->toBe(json_decode('true'))
            ->and(jsonDecode('true'))->toBe(true);

        expect(Json::decode('null'))
            ->toBe(json_decode('null'))
            ->and(jsonDecode('null'))->toBe(null);

        expect(Json::decode('[]'))
            ->toBe(json_decode('[]'))
            ->and(jsonDecode('[]'))->toBe([]);

        expect(Json::decode('{}'))
            ->toEqual(json_decode('{}'))
            ->and(jsonDecode('{}'))->toEqual(new stdClass);
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


        expect(Json::decodeToArray('0'))
            ->toBe([0])
            ->and(jsonDecodeToArray('0'))->toBe([0]);

        expect(Json::decodeToArray('5'))
            ->toBe([5])
            ->and(jsonDecodeToArray('5'))->toBe([5]);

        expect(Json::decodeToArray('5.5'))
            ->toBe([5.5])
            ->and(jsonDecodeToArray('5.5'))->toBe([5.5]);

        expect(Json::decodeToArray('null'))
            ->toBe([]) // casting null to array will be empty array
            ->and(jsonDecodeToArray('null'))->toBe([]);

        expect(Json::decodeToArray('true'))
            ->toBe([true])
            ->and(jsonDecodeToArray('true'))->toBe([true]);

        expect(Json::decodeToArray('false'))
            ->toBe([false])
            ->and(jsonDecodeToArray('false'))->toBe([false]);

        expect(Json::decodeToArray('[]'))
            ->toBe([])
            ->and(jsonDecodeToArray('[]'))->toBe([]);

        expect(Json::decodeToArray('{}'))
            ->toBe([]) // casting empty object to array will be empty array
            ->and(jsonDecodeToArray('{}'))->toBe([]);
    });

    test('decode json to array returns identical value in array format when the value is not json', function () {

        expect(Json::decodeToArray(-5))->toBe([-5]);
        expect(Json::decodeToArray(5))->toBe([5]);
        expect(Json::decodeToArray(0))->toBe([0]);
        expect(Json::decodeToArray(5.5))->toBe([5.5]);

        expect(Json::decodeToArray('foo bar'))->toBe(['foo bar']);
        expect(Json::decodeToArray(''))->toBe(['']);
        expect(Json::decodeToArray('  '))->toBe(['  ']);

        expect(Json::decodeToArray(null))->toBe([]);
        expect(Json::decodeToArray(true))->toBe([true]);
        expect(Json::decodeToArray(false))->toBe([false]);

        expect(Json::decodeToArray(['foo' => 'bar']))->toBe(['foo' => 'bar']);
        expect(Json::decodeToArray([0]))->toBe([0]);
        expect(Json::decodeToArray([null]))->toBe([null]);
        expect(Json::decodeToArray([true]))->toBe([true]);
        expect(Json::decodeToArray([false]))->toBe([false]);
        expect(Json::decodeToArray([]))->toBe([]);

        expect(Json::decodeToArray(new stdClass))->toBe([]);

        $object = new stdClass;
        $object->foo = 'bar';

        expect(Json::decodeToArray($object)['foo'])->toBe('bar');
    });

    test('json response return a http response with standarad structure', function () {

        expect(Json::respond())->toBeInstanceOf(JsonResponse::class);
        expect(jsonRespond())->toBeInstanceOf(JsonResponse::class);

        $facade = Json::respond(
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
                'accept-language'    => 'fa'
            ]
        );

        $helper = jsonRespond(
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
                'accept-language'    => 'fa'
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
                    'accept-language'    => 'fa'
                ]
            );

        expect($facade->getStatusCode())->toBe($expected->getStatusCode());
        expect($helper->getStatusCode())->toBe($expected->getStatusCode());

        expect($facade->getData(true))->toBe($expected->getData(true));
        expect($helper->getData(true))->toBe($expected->getData(true));

        expect($facade->headers->all())->toBe($expected->headers->all());
        expect($helper->headers->all())->toBe($expected->headers->all());

        expect($facade->getStatusCode())->toBe(429);
        expect($facade->headers->all()['accept-language'][0])->toBe('fa');

        expect($facade->getData(true)['status'])->toBe(429);
        expect($facade->getData(true)['success'])->toBe(false);
        expect($facade->getData(true)['message'])->toBe('too many request');
        expect($facade->getData(true)['errors'])->toBe(['foo' => 'bar']);
        expect($facade->getData(true)['data'])->toBe(['foo' => 'bar']);
        expect($facade->getData(true)['additional'])->toBe(['bypass' => false]);

        $withOptions = Json::respond(
            status: 200,
            data: ['foo' => 'bar', 'number' => '123'],
            options: JSON_NUMERIC_CHECK
        );

        expect($withOptions->getData(true)['data'])->toBe(['foo' => 'bar', 'number' => 123]); // the '123' casted to number
    });

    test('check json response template', function () {

        expect(Json::responseTemplate())->toBe([
            'status'        => 200,
            'success'       => true,
            'message'       => '',
            'errors'        => [],
            'data'          => [],
            'additional'    => []
        ]);
    });
});
