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


1. [Json Facade](./docs/markdown/JSON_FACADE.md)

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
    + Concrete  `Fooino\Core\Concretes\Math\MathManager` (default driver: `FooinoMathHandler`)
    + Unit test `Fooino\Core\Tests\Unit\MathFacadeUnitTest`
    + Available helpers: `math()`, `number()`, `numberFormat()`, `sum()`, `subtract()`, `multiply()`, `divide()`, `remainder()`, `roundUp()`, `roundDown()`, `roundClose()`, `greaterThan()`, `greaterThanOrEqual()`, `lessThan()`, `lessThanOrEqual()`, `equal()`, `notEqual()`
    + Basic Usage (all methods are also available as helper functions, e.g. `math()` instead of `Math::`):
    ```php
        use Fooino\Core\Facades\Math;

        // --- Precision management ---
        Math::getPrecision();                          // 12 (default)
        Math::setPrecision(precision: 4)->getPrecision(); // 4 (returns fresh instance)

        // --- Number string handling ---
        Math::convertScientificNumber('1.1e+8');       // "110000000.000000000000"
        Math::trimTrailingZeros('110000000.000000000000'); // "110000000"
        Math::countDecimalPlaces(1.1e-8);              // 9

        // --- Precision‑truncated number ---
        Math::number(1.1e+8);                          // "110000000"
        Math::setPrecision(precision: 4)->number(0.44015042); // "0.4401"
        Math::number(1.123456789, 2.345678901);        // ["1.123456789", "2.345678901"] (array)

        // --- Locale‑friendly number format ---
        Math::numberFormat('2000000.12');                          // "2,000,000.12"
        Math::numberFormat(number: '2000000.12', thousandsSeparator: ' '); // "2 000 000.12"

        // --- Arithmetic (variadic / array) ---
        Math::sum(1, 2, 3);                             // "6"
        Math::subtract(10, 3, 2);                       // "5"
        Math::multiply(2, 3, 4);                        // "24"
        Math::divide(100, 4, 5);                        // "5"
        Math::remainder(10, 3);                         // "1"

        // --- Power & square root ---
        Math::power(2, 3);                              // "8"
        Math::sqrt(144);                                // "12"

        // --- Rounding ---
        Math::roundUp(2.1);                             // "3"
        Math::roundDown(2.9);                           // "2"
        Math::roundClose(2.5678, precision: 2);         // "2.57" (default mode: HalfAwayFromZero)

        // --- Comparison ---
        Math::greaterThan(5, 3);                        // true
        Math::greaterThanOrEqual(5, 5);                 // true
        Math::lessThan(3, 5);                           // true
        Math::lessThanOrEqual(3, 3);                    // true
        Math::equal(10, 10);                            // true
        Math::notEqual(10, 5);                          // true
    ```

4. [Helpers](./docs/markdown/HELPERS.md)


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