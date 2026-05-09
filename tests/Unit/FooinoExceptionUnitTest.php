<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;

class EmptyException extends FooinoException {};

class CustomException extends FooinoException
{
    protected $message = 'fooino';

    protected $code = 10;

    protected string $level = 'fooino';

    protected int $httpStatusCode = 422;

    protected array $with = ['foo' => 'ino'];

    protected bool $report = false;
}


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

    expect($e->reportable())->toBeFalse();
    expect($e->shouldReport()->reportable())->toBeTrue();
    expect($e->dontReport()->reportable())->toBeFalse();
});

test('check throw and log methods', function () {

    $e = app(CustomException::class);

    expect($e->log(false))->toEqual('Fooino\Core\Tests\Unit\CustomException|fooino|10|422|fooino|{"foo":"ino"}');

    $e = app(CustomException::class)->setMessage('nasty error')->setCode(100)->alert()->setHttpStatusCode(503)->with(['timestamp' => '123123123'])->shouldReport();

    expect($e->log(false))->toEqual('Fooino\Core\Tests\Unit\CustomException|nasty error|100|503|alert|{"timestamp":"123123123"}');

    try {

        $e->throw();

        // 
    } catch (FooinoException $e) {

        expect($e->getMessage())->toEqual('nasty error');
        expect($e->getCode())->toEqual(100);
        expect($e->getLevel())->toEqual('alert');
        expect($e->getHttpStatusCode())->toEqual(503);
        expect(jsonDecodeToArray($e->getWith()))->toEqual(['timestamp' => '123123123']);
        expect($e->reportable())->toBeTrue();
    }

    $e = app(EmptyException::class);

    expect($e->log(false))->toEqual("Fooino\Core\Tests\Unit\EmptyException|empty message|0|500|error|[]");

    try {

        $e->throw();

        // 
    } catch (FooinoException $e) {

        expect($e->getMessage())->toEqual('');
        expect($e->getCode())->toEqual(0);
        expect($e->getLevel())->toEqual('error');
        expect($e->getHttpStatusCode())->toEqual(500);
        expect(jsonDecodeToArray($e->getWith()))->toEqual([]);
        expect($e->reportable())->toBeTrue();
    }
});
