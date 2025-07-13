<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tasks\Tag\AddNewTagTask;
use Fooino\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddNewTagTaskUnitTest extends TestCase
{
    use DatabaseMigrations;

    public function test_the_task()
    {
        app(AddNewTagTask::class)->run([
            'laravel',
            'php'
        ]);

        $this->assertDatabaseHas('tags', [
            'name'  => 'laravel',
        ]);
        $this->assertDatabaseHas('tags', [
            'name'  => 'php',
        ]);
        app(AddNewTagTask::class)->run([
            'laravel',
            'php',
            'js'
        ]);

        $this->assertDatabaseHas('tags', [
            'name'  => 'laravel',
        ]);
        $this->assertDatabaseHas('tags', [
            'name'  => 'php',
        ]);
        $this->assertDatabaseHas('tags', [
            'name'  => 'js',
        ]);
    }
}
