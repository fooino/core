<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Facades\Json;
use Fooino\Core\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use stdClass;

class JsonFacadeUnitTest extends TestCase
{

    public function test_is_json_method_works_correctly()
    {
        $int = 5;
        $array = ['fo' => 'bar'];
        $string = 'fo bar';
        $object = new stdClass;
        $object->fo = 'bar';
        $null = null;
        $boolean = true;
        $callable = fn() => 'foobar';

        $this->assertFalse(Json::is($int) || isJson($int));
        $this->assertFalse(Json::is($array) || isJson($array));
        $this->assertFalse(Json::is($string) || isJson($string));
        $this->assertFalse(Json::is($object) || isJson($object));
        $this->assertFalse(Json::is($null) || isJson($null));
        $this->assertFalse(Json::is($boolean) || isJson($boolean));
        $this->assertFalse(Json::is($callable) || isJson($callable));

        $this->assertTrue(Json::is(json_encode($int)) && isJson(json_encode($int)));
        $this->assertTrue(Json::is(json_encode($array)) && isJson(json_encode($array)));
        $this->assertTrue(Json::is(json_encode($string)) && isJson(json_encode($string)));
        $this->assertTrue(Json::is(json_encode($object)) && isJson(json_encode($object)));
        $this->assertTrue(Json::is(json_encode($null)) && isJson(json_encode($null)));
        $this->assertTrue(Json::is(json_encode($boolean)) && isJson(json_encode($boolean)));
        $this->assertTrue(Json::is(json_encode($callable)) && isJson(json_encode($callable)));
    }

    public function test_encode_method()
    {
        $int = 5;
        $array = ['fo' => 'bar'];
        $string = 'fo bar';
        $object = new stdClass;
        $object->fo = 'bar';
        $null = null;
        $boolean = true;

        $this->assertEquals(Json::encode($int), json_encode($int));
        $this->assertEquals(Json::encode($array), json_encode($array));
        $this->assertEquals(Json::encode($string), json_encode($string));
        $this->assertEquals(Json::encode($object), json_encode($object));
        $this->assertEquals(Json::encode($null), json_encode($null));
        $this->assertEquals(Json::encode($boolean), json_encode($boolean));

        $this->assertEquals(Json::encode(Json::encode($array)), json_encode($array));

        $this->assertEquals(jsonEncode($int), json_encode($int));
        $this->assertEquals(jsonEncode($array), json_encode($array));
        $this->assertEquals(jsonEncode($string), json_encode($string));
        $this->assertEquals(jsonEncode($object), json_encode($object));
        $this->assertEquals(jsonEncode($null), json_encode($null));
        $this->assertEquals(jsonEncode($boolean), json_encode($boolean));

        $this->assertEquals(jsonEncode(jsonEncode($array)), json_encode($array));
    }

    public function test_decode_method()
    {
        $int = json_encode(5);
        $array = json_encode(['fo' => 'bar']);
        $string = json_encode('fo bar');
        $object = new stdClass;
        $object->fo = 'bar';
        $object = json_encode($object);
        $null = json_encode(null);
        $boolean = json_encode(true);

        $this->assertEquals(Json::decode($int), json_decode($int));
        $this->assertEquals(Json::decode($array), json_decode($array));
        $this->assertEquals(Json::decode($string), json_decode($string));
        $this->assertEquals(Json::decode($object), json_decode($object));
        $this->assertEquals(Json::decode($null), json_decode($null));
        $this->assertEquals(Json::decode($boolean), json_decode($boolean));

        $this->assertEquals(jsonDecode($int), json_decode($int));
        $this->assertEquals(jsonDecode($array), json_decode($array));
        $this->assertEquals(jsonDecode($string), json_decode($string));
        $this->assertEquals(jsonDecode($object), json_decode($object));
        $this->assertEquals(jsonDecode($null), json_decode($null));
        $this->assertEquals(jsonDecode($boolean), json_decode($boolean));

        $object = new stdClass;
        $this->assertEquals(Json::decode(['fo' => 'bar']), ['fo' => 'bar']);
        $this->assertEquals(Json::decode([]), []);
        $this->assertEquals(Json::decode($object), $object);
        $this->assertEquals(Json::decode('fo bar'), 'fo bar');
        $this->assertEquals(Json::decode(5), 5);
        $this->assertEquals(Json::decode(0), 0);
        $this->assertEquals(Json::decode(5.5), 5.5);
        $this->assertEquals(Json::decode(null), null);
        $this->assertEquals(Json::decode(true), true);
        $this->assertEquals(Json::decode(false), false);

        $this->assertEquals(jsonDecode(['fo' => 'bar']), ['fo' => 'bar']);
        $this->assertEquals(jsonDecode([]), []);
        $this->assertEquals(jsonDecode($object), $object);
        $this->assertEquals(jsonDecode('fo bar'), 'fo bar');
        $this->assertEquals(jsonDecode(5), 5);
        $this->assertEquals(jsonDecode(0), 0);
        $this->assertEquals(jsonDecode(5.5), 5.5);
        $this->assertEquals(jsonDecode(null), null);
        $this->assertEquals(jsonDecode(true), true);
        $this->assertEquals(jsonDecode(false), false);
    }

    public function test_json_decode_to_array_method()
    {
        $this->assertEquals(Json::decodeToArray(json_encode([123])), [123]);
        $this->assertEquals(Json::decodeToArray(json_encode(123)), [123]);
        $this->assertEquals(Json::decodeToArray(json_encode(123.123)), [123.123]);
        $this->assertEquals(Json::decodeToArray(json_encode(null)), []);
        $this->assertEquals(Json::decodeToArray(json_encode(true)), [true]);
        $this->assertEquals(Json::decodeToArray(json_encode(false)), [false]);
        $this->assertEquals(Json::decodeToArray(json_encode('foobar')), ['foobar']);
        $this->assertEquals(Json::decodeToArray(json_encode('')), ['']);

        $this->assertEquals(jsonDecodeToArray(json_encode([123])), [123]);
        $this->assertEquals(jsonDecodeToArray(json_encode(123)), [123]);
        $this->assertEquals(jsonDecodeToArray(json_encode(123.123)), [123.123]);
        $this->assertEquals(jsonDecodeToArray(json_encode(null)), []);
        $this->assertEquals(jsonDecodeToArray(json_encode(true)), [true]);
        $this->assertEquals(jsonDecodeToArray(json_encode(false)), [false]);
        $this->assertEquals(jsonDecodeToArray(json_encode('foobar')), ['foobar']);
        $this->assertEquals(jsonDecodeToArray(json_encode('')), ['']);
    }

    public function test_json_response_method()
    {
        $this->assertInstanceOf(JsonResponse::class, Json::response());
        $this->assertInstanceOf(JsonResponse::class, jsonResponse());


        $this->assertEquals(
            Json::response(
                status: 429,
                message: 'too many request',
                data: [
                    'foo' => 'bar'
                ],
                errors: [
                    'foo'   => 'bar'
                ],
                headers: [
                    'accept'    => 'application/json'
                ]
            ),
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
                ],
                status: 429,
                headers: [
                    'accept'    => 'application/json'
                ]
            )
        );

        $this->assertEquals(
            jsonResponse(
                status: 429,
                message: 'too many request',
                data: [
                    'foo' => 'bar'
                ],
                errors: [
                    'foo'   => 'bar'
                ],
                headers: [
                    'accept'    => 'application/json'
                ]
            ),
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
                ],
                status: 429,
                headers: [
                    'accept'    => 'application/json'
                ]
            )
        );
    }

    public function test_template_method()
    {
        $this->assertEquals(gettype(Json::template()), 'array');
        $this->assertEquals(
            Json::template(),
            [
                'status'    => 200,
                'success'   => true,
                'message'   => '',
                'errors'    => [],
                'data'      => [],
            ]
        );
    }
}
