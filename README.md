# Fooino core package

## ⬇️ Installation

You can install the package


1. With composer:

```bash
composer require fooino/core
```

2. With Docker(for running and modify the package)
```bash
git clone https://github.com/fooino/core.git
git clone https://github.com/fooino/laravel-fooino-packages-docker.git
cd ./laravel-fooino-packages-docker
docker-compose -p fooino up -d --build
docker exec -it fooino_php bash
cd ../core
composer update
./vendor/bin/pest
exit
```

## 📝 Documentation

```bash
# the result will be built at docs/core
./vendor/bin/phpdoc # or composer phpdoc
```

**Facades**
1. Json
    + Interface `Fooino\Core\Interfaces\Jsonable`
    + Concrete  `Fooino\Core\Concretes\JsonManager`
    + Unit test `Fooino\Core\Tests\JsonFacadeUnitTest`
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

2. Date
    + Interface `Fooino\Core\Interfaces\Dateable`
    + Concrete  `Fooino\Core\Concretes\DateManager`
    + Unit test `Fooino\Core\Tests\DateFacadeUnitTest`
    + Basic Usage
    ```php
        use Fooino\Core\Facades\Date;

        // return DateTimeZone::listIdentifiers() list that contains ['Asia/Tehran', 'America/New_York', 'UTC', ...]
        Date::getTimezones();

        // validate timezone base on Date::getTimezones() list
        Date::validateTimezone('Asia/Tehran'); // true
        Date::validateTimezone('Asia/Fooino'); // false

        // convert date
        Date::convert(date: '2022-12-24 19:27:00', format: 'Y-m-d H:i:s', to: 'Asia/Tehran'); // 1401-10-03 22:57:00
        Date::convert(date: '2022-12-24 07:27:00 PM', format: 'Y-m-d h:i:s A', to: 'Asia/Tehran'); // 1401-10-03 10:57:00 بعد از ظهر


    ```
3. Math
    + Interface `Fooino\Core\Interfaces\Mathable`
    + Concrete  `Fooino\Core\Concretes\MathManager`
    + Unit test `Fooino\Core\Tests\MathFacadeUnitTest`
    + Basic Usage
    ```php
        use Fooino\Core\Facades\Math;

        Math::convertScientificNumber('1.1e+8'); //110000000.000000000000
        Math::trimTrailingZeros('110000000.000000000000'); // 110000000
        Math::countDecimalPlaces(1.1e-8); // 9

        Math::setPrecision(precision: 4)->number(0.44015042); //0.4401 - or use number()
        Math::number(1.1e+8); // 110000000
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