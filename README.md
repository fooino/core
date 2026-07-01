# Fooino core package

## ⬇️ Installation

You can install the package


1. With composer:

```bash
composer require fooino/core
```

2. With Docker(for running and modify the package)
```bash
mkdir fooino && cd fooino && mkdir packages

cd fooino/packages && git clone https://github.com/fooino/core.git

cd fooino/ && git clone https://github.com/fooino/laravel-fooino-packages-docker.git

cd ./fooino/laravel-fooino-packages-docker && docker-compose -p fooino up -d --build

docker exec -it fooino-php bash

cd ./packages/core

composer update

./vendor/bin/pest

exit
```

## 📝 Documentation

1. [Json Facade](./docs/markdown/JSON_FACADE.md)

2. [Date Facade](./docs/markdown/DATE_FACADE.md)

3. [Math Facade](./docs/markdown/MATH_FACADE.md)

4. [Helpers](./docs/markdown/HELPERS.md)

5. [Sanitizer](./docs/markdown/SANITIZER.MD)

6. [NormalizesInputs trait](./docs/markdown/NORMALIZES_INPUTS.md)

7. [TokenGenerator](./docs/markdown/TOKEN_GENERATOR.md)

8. [SingletonableTask](./docs/markdown/SINGLETONABLE_TASK.md)

9. [FooinoException](./docs/markdown/FOOINO_EXCEPTION.md)

10. [Exceptions](./docs/markdown/EXCEPTIONS.md)

11. [Configuration](./docs/markdown/CONFIGURATION.md)


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