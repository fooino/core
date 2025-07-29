<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Abstracts\SingletonableTask;
use Fooino\Core\Tests\TestCase;

class SingletonableTaskUnitTest extends TestCase
{
    public function test_singletonable_task()
    {
        $task = new class extends SingletonableTask {


            public int $calledCount = 0;

            public function getData(): mixed
            {
                $this->calledCount++;
                return ['foobar'];
            }
        };

        $this->assertEquals($task->calledCount, 0);
        $this->assertEquals($task->run(), ['foobar']);
        $this->assertEquals($task->calledCount, 1);
        $task->run();
        $this->assertEquals($task->calledCount, 1);

        $task->reset();
        $task->run();
        $this->assertEquals($task->calledCount, 2);
    }
}
