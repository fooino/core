<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Concretes\Date\DateManager;
use Fooino\Core\Concretes\Json\JsonManager;
use Fooino\Core\Concretes\Math\MathManager;
use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Interfaces\Dateable;
use Fooino\Core\Interfaces\Jsonable;
use Fooino\Core\Interfaces\Mathable;
use Fooino\Core\Support\SingletonableTask;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Manager;
use Illuminate\Support\ServiceProvider;

describe('Arch tests', function () {

    // arch('Actions')
    //     ->expect('Fooino\Core\*\Actions')
    //     ->toHaveSuffix('Action');

    // arch('Tasks')
    //     ->expect('Fooino\Core\*\Tasks')
    //     ->toHaveSuffix('Task');

    arch('Enums')
        ->expect('Fooino\Core\Enums')
        ->toBeEnums();

    arch('Interfaces')
        ->expect('Fooino\Core\Interfaces')
        ->toBeInterface();

    arch('Concerns')
        ->expect('Fooino\Core\Concerns')
        ->toBeTraits();

    arch('Facades')
        ->expect('Fooino\Core\Facades')
        ->toExtend(Facade::class);

    arch('Exceptions')
        ->expect('Fooino\Core\Exceptions')
        ->toExtend(FooinoException::class)
        ->ignoring('Fooino\Core\Exceptions\FooinoException');

    arch('Service Provider')
        ->expect('Fooino\Core\Providers\CoreServiceProvider')
        ->toExtend(ServiceProvider::class);

    arch('no debug calls')
        ->expect(['dd', 'dump', 'var_dump', 'ray'])
        ->not->toBeUsed();

    // arch('Documented')
    //     ->expect('Fooino\Core')
    //     ->toHaveMethodsDocumented();

    arch('Abstract')
        ->expect([
            SingletonableTask::class,
        ])
        ->toBeAbstract();

    arch('Managers')
        ->expect([
            MathManager::class,
            DateManager::class,
            JsonManager::class,
        ])
        ->toExtend(Manager::class);

    arch('Math Handler')
        ->expect('Fooino\Core\Concretes\Math\FooinoMathHandler')
        ->toImplement(Mathable::class);

    arch('Date Handler')
        ->expect('Fooino\Core\Concretes\Date\FooinoDateHandler')
        ->toExtend('Fooino\Core\Concretes\Date\DateHandler')
        ->toImplement(Dateable::class);

    arch('Json Handler')
        ->expect('Fooino\Core\Concretes\Json\FooinoJsonHandler')
        ->toImplement(Jsonable::class);
});
