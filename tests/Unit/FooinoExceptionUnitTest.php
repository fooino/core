<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmptyException extends FooinoException {};

class CustomException extends FooinoException
{
    protected $message = 'fooino';

    protected $code = 10;

    protected string $level = 'fooino';

    protected int $httpStatusCode = 422;

    protected array $with = ['foo' => 'ino'];

    protected array $placeholders = ['name' => 'ino'];

    protected bool $report = false;
}

describe('FooinoException for better error handling', function () {

    test('check the getter and setter', function () {

        $levels = [
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
            'debug'
        ];

        $e = app(CustomException::class);

        expect($e->getCause())->toBe(null);
        expect($e->cause(new ModelNotFoundException('Row not found'))->getCause())->toBeInstanceOf(ModelNotFoundException::class);

        expect($e->getMessage())->toEqual('fooino');
        expect($e->setMessage('error message')->getMessage())->toEqual('error message');

        expect($e->getCode())->toEqual(10);
        expect($e->setCode(100)->getCode())->toEqual(100);

        expect($e->getLevel())->toEqual('fooino');

        foreach ($levels as $level) {
            expect($e->{$level}()->getLevel())->toEqual($level);
        }

        expect($e->getHttpStatusCode())->toEqual(422);
        expect($e->setHttpStatusCode(401)->getHttpStatusCode())->toEqual(401);

        expect($e->getWith())->toEqual(['foo' => 'ino']);
        expect($e->with(['foo' => 'bar'])->getWith())->toEqual(['foo' => 'bar']);

        expect($e->getPlaceholders())->toEqual(['name' => 'ino']);
        expect($e->setPlaceholders(['name' => 'fooino'])->getPlaceholders())->toEqual(['name' => 'fooino']);

        expect($e->reportable())->toBeFalse();
        expect($e->shouldReport()->reportable())->toBeTrue();
        expect($e->dontReport()->reportable())->toBeFalse();
    });

    test('check throw and log methods', function () {

        $e = app(CustomException::class);

        expect($e->log())->toContain('[stacktrace]');

        expect($e->log(trace: false))->toEqual('Fooino\Core\Tests\Unit\CustomException|fooino|10|422|fooino|{"foo":"ino"}');

        $e = app(CustomException::class)->setMessage('nasty error')->setCode(100)->alert()->setHttpStatusCode(503)->with(['timestamp' => '123123123'])->setPlaceholders(['name' => 'John'])->shouldReport();

        expect($e->log(trace: false))->toEqual('Fooino\Core\Tests\Unit\CustomException|nasty error|100|503|alert|{"timestamp":"123123123"}');

        expect(fn() => $e->throw())->toThrow(CustomException::class);

        try {

            $e->throw();

            // 
        } catch (FooinoException $e) {

            expect($e->getCause())->toBe(null);
            expect($e->getMessage())->toEqual('nasty error');
            expect($e->getCode())->toEqual(100);
            expect($e->getLevel())->toEqual('alert');
            expect($e->getHttpStatusCode())->toEqual(503);
            expect(jsonDecodeToArray($e->getWith()))->toEqual(['timestamp' => '123123123']);
            expect($e->getPlaceholders())->toEqual(['name' => 'John']);
            expect($e->reportable())->toBeTrue();
        }

        $e = app(EmptyException::class);

        expect($e->log(false))->toEqual("Fooino\Core\Tests\Unit\EmptyException|empty message|0|500|error|[]");

        expect(fn() => $e->throw())->toThrow(EmptyException::class);

        try {

            $e->throw();

            // 
        } catch (FooinoException $e) {

            expect($e->getCause())->toBe(null);
            expect($e->getMessage())->toEqual('');
            expect($e->getCode())->toEqual(0);
            expect($e->getLevel())->toEqual('error');
            expect($e->getHttpStatusCode())->toEqual(500);
            expect(jsonDecodeToArray($e->getWith()))->toEqual([]);
            expect($e->getPlaceholders())->toEqual([]);
            expect($e->reportable())->toBeTrue();
        }

        try {

            app(EmptyException::class)
                ->setMessage('custom message')
                ->setCode(42)
                ->critical()
                ->setHttpStatusCode(403)
                ->with(['key' => 'value'])
                ->setPlaceholders(['foo' => 'bar'])
                ->shouldReport()
                ->throw();

            // 
        } catch (FooinoException $e) {

            expect($e->getCause())->toBe(null);
            expect($e->getMessage())->toEqual('custom message');
            expect($e->getCode())->toEqual(42);
            expect($e->getLevel())->toEqual('critical');
            expect($e->getHttpStatusCode())->toEqual(403);
            expect($e->getWith())->toEqual(['key' => 'value']);
            expect($e->getPlaceholders())->toEqual(['foo' => 'bar']);
            expect($e->reportable())->toBeTrue();
            expect($e->log(trace: false))->toEqual('Fooino\Core\Tests\Unit\EmptyException|custom message|42|403|critical|{"key":"value"}');
        }
    });

    test('cause wraps a non-fooino exception and preserves it through nested try-catch', function () {

        try {

            try {

                throw new ModelNotFoundException('Row not found');

                //
            } catch (ModelNotFoundException $e) {

                throw app(FooinoException::class)
                    ->setHttpStatusCode(404)
                    ->setLevel('warning')
                    ->cause($e)
                    ->throw();
            }

            //
        } catch (FooinoException $e) {

            expect($e->getHttpStatusCode())->toBe(404);
            expect($e->getLevel())->toBe('warning');

            $cause = $e->getCause();

            expect($cause)->toBeInstanceOf(ModelNotFoundException::class);
            expect($cause->getMessage())->toBe('Row not found');
        }
    });
});
