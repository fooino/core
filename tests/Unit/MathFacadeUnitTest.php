<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Facades\Math;

describe('Math facade using FooinoMathHandler', function () {

    test('precision getter and setter', function () {

        expect(Math::getPrecision())->toBe(12);
        expect(Math::setPrecision(precision: 5)->getPrecision())->toBe(5);

        expect(math()->getPrecision())->toBe(12);
        expect(math(precision: 5)->getPrecision())->toBe(5);

        expect(bcscale())->toBe(12);
    });

    test('handle exceptions', function () {

        // ============ Invalid Precision ============

        expect(fn() => math(20))->toThrow('msg.mathCalculationExceptionInvalidPrecision');

        try {

            Math::setPrecision(precision: 20);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidPrecision');
            expect($e->getCode())->toBe(10101);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'precision' => 20,
                'bc_scale'  => 12
            ]);
        }

        expect(fn() => math(-1))->toThrow('msg.mathCalculationExceptionInvalidPrecision');
        
        try {

            Math::setPrecision(precision: -1);

            // 
        } catch (FooinoException $e) {

            expect($e->getMessage())->toBe('msg.mathCalculationExceptionInvalidPrecision');
            expect($e->getCode())->toBe(10101);
            expect($e->getLevel())->toBe('critical');
            expect($e->reportable())->toBeTrue();
            expect($e->getWith())->toBe([
                'precision' => -1,
                'bc_scale'  => 12
            ]);
        }
        // ============ Invalid Precision ============
    });
});
