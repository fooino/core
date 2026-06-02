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

    test('convertScientificNumber method', function () {

        expect(Math::convertScientificNumber(11.000000))->toBe('11');
        expect(Math::convertScientificNumber(11))->toBe('11');
        expect(Math::convertScientificNumber(-11))->toBe('-11');
        expect(Math::convertScientificNumber(11.11))->toBe('11.11');

        expect(Math::convertScientificNumber('1.1e+8'))->toBe('110000000.0000000000');
        expect(Math::convertScientificNumber(1.1e+8))->toBe('110000000');
        expect(Math::convertScientificNumber('1.1e+20'))->toBe('110000000000000000000.0000000000');
        expect(Math::convertScientificNumber('1.1E-8'))->toBe('0.0000000110');
        expect(Math::convertScientificNumber('1.1e-8'))->toBe('0.0000000110');
        expect(Math::convertScientificNumber('1.1e-11'))->toBe('0.0000000000'); // Max scale: 10
        expect(Math::convertScientificNumber('-1.1e-11'))->toBe('-0.0000000000'); // Max scale: 10
        expect(Math::convertScientificNumber('20.1e+20'))->toBe('2010000000000000000000.0000000000');

        expect(Math::convertScientificNumber(null))->toBe('0');
        expect(Math::convertScientificNumber('abc1E+3xyz'))->toBe('abc1E+3xyz'); // contains 1E+3 which is valid Scientific Number but the method must not convert it
        expect(Math::convertScientificNumber('test'))->toBe('test');
    });
});
