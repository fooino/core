<h1>Fooino core package</h1>

+ **Facades**
    1. Json
        + Interface ```Fooino\Core\Interfaces\Jsonable```
        + Concrete  ```Fooino\Core\Concretes\Json```
        + Unit test ```Fooino\Core\Tests\JsonFacadeUnitTest```
        + Basic Usage
            ```php
                use Fooino\Core\Facades\Json;
            
                // To validate a value is json or not | or use isJson() helper
                Json::is(5); // false
                Json::is(json_encode(['foo' => 'bar'])); // true

                // To encode a value to json format | see jsonEncode(), Json::encodePrettified() and jsonEncodePrettified()
                Json::encode(['foo' => 'bar']); // "{"foo":"bar"}"

                // To decode a json to the original format | see jsonDecode(), Json::decodeToArray() and jsonDecodeToArray()
                Json::decode('{"foo":"bar"}' , true); // ['foo' => 'bar']

                // To return response to user in json format and standard structure | or use jsonResponse() helper
                Json::response(status: 200, message: 'ok', errors: ['foo' => 'the foo is required'], data: ['foo' => 'bar'], additional: ['foo' => 'ino'], headers: ['language' => 'fa']) // it returns \Illuminate\Http\JsonResponse
            ```

+ **Helpers**
    1. ```nullIfBlank()``` convert value to null when the value is ```blank``` or ```'null'```,```"null"``` otherwise it returns the value or fallback value
    2. 