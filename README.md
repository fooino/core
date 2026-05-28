# Fooino core package

## ⬇️ Installation

You can install the package via composer:

```bash
composer require fooino/core
```

## 📝 Documentation

```bash
# the result will be built at docs/core
./vendor/bin/phpdoc # or composer phpdoc
```

**Facades**
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

**Helpers**
1. `nullIfBlank()` Returns a fallback value when the input is considered `blank` or a `null-like string` which usually produce by js.
2. `nullIfBlankOrZero()` Convert value to null when the value is ZERO or blank base on `nullIfBlank()`
3. `removeComma()` Remove comma between letters when the value is string or array
4. `removeSpace()` Remove space between letters when the value is string or array
5. `sanitizeNumber()` Remove space and comma from value
6. `replaceSlashToDash()` Replace slashes to dashes when the value is string or array
7. `setDefaultLocale()` and `getDefaultLocale()` are getter and setter for `app.locale` config
8. `currentDate()` and `currentDateTime()` returns date and time in `Y-m-d` and `Y-m-d H:i:s` format
9. `callMethodIfExists()` Safely call a method on an object or class if it exists, otherwise return a fallback value.


## 🚀 Change log

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes.

## ✅ Testing

```bash
./vendor/bin/pest # or composer pest
```

## 👨‍💻 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details to how contribute.

## 🐞 Security

If you've found a bug regarding security please mail [sajadsholidev@gmail.com](mailto:sajadsholidev@gmail.com)

## 🔥 Credits

-   [Sajad Sholi](mailto:sajadsholidev@gmail.com)
-   [All Contributors](../../contributors)

## ⚖️ License

PRIVATE CODE. Please see [License File](LICENSE.md) for more information.