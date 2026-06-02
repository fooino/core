<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Facades\Math;

describe('Math facade using FooinoMathHandler', function () {

    test('precision getter and setter', function () {

        expect(Math::getPrecision())->toBe(10);
        expect(Math::setPrecision(precision: 5)->getPrecision())->toBe(5);

        expect(math()->getPrecision())->toBe(10);
        expect(math(precision: 5)->getPrecision())->toBe(5);
    });
});
