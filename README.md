# Fooino core package

## ⬇️ Installation

You can install the package via composer:

```bash
composer require fooino/core
```

## ☕ If this package makes your life easier, Buy me a coffee… but make it ₿itcoin

```
bc1q5g0kxxwwcn5h8vnv8rhch2c9x6uxy9ay9k5ch2
```

## 📝 Documentation

```bash
./vendor/bin/phpdoc # the result will be build at docs/api
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
1. `nullIfBlank()` convert value to null when the value is `blank` or `'null'`,`"null"` otherwise it returns the value or fallback value
2. `nullIfBlankOrZero()` convert value to null when the value is ZERO or blank base on `nullIfBlank()`
3. `removeComma()` remove comma between letters when the value is string or array
4. `removeSpace()` remove space between letters when the value is string or array
5. `sanitizeNumber()` remove space and comma from value
6. `replaceSlashToDash()` replace dashes to slash when the value is string or array
7. `setDefaultLocale()` and `getDefaultLocale()` are getter and setter for `app.locale` config
8. `currentDate()` and `currentDateTime()` returns date and time in `Y-m-d` and `Y-m-d H:i:s` format


## 🚀 Change log

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes.

## ✅ Testing

```bash
./vendor/bin/pest
```

## 👨‍💻 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details to how contribute.

## 🐞 Security

If you've found a bug regarding security please mail [sajadsholidev@gmail.com](mailto:sajadsholidev@gmail.com) instead of using the issue tracker.

## 🔥 Credits

-   [Sajad Sholi](mailto:sajadsholidev@gmail.com)
-   [All Contributors](../../contributors)

## ⚖️ License

PRIVATE CODE. Please see [License File](LICENSE.md) for more information.