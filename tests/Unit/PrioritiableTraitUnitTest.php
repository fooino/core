<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Prioritiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrioritiableTraitUnitTest extends TestCase
{
    public function test_the_data_prioritized_base_on_priority_field()
    {
        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('priority');
            $table->timestamps();
        });

        $model = new class extends Model
        {
            use Prioritiable;

            protected $guarded = ['id'];

            protected $table = 'users_table';
        };

        $model->insert([
            [
                'id'         => 1,
                'name'       => 'first',
                'priority'   => 300
            ],
            [
                'id'         => 2,
                'name'       => 'second',
                'priority'   => 100
            ],
            [
                'id'         => 3,
                'name'       => 'third',
                'priority'   => 200
            ],
            [
                'id'         => 4,
                'name'       => 'fourth',
                'priority'   => 300
            ],
        ]);

        $models = $model->get();

        $this->assertEquals($models[0]->name, 'second');
        $this->assertEquals($models[1]->name, 'third');
        $this->assertEquals($models[2]->name, 'fourth');
        $this->assertEquals($models[3]->name, 'first');


        $model = new class extends Model
        {
            use Prioritiable;

            protected $guarded = ['id'];

            protected $table = 'users_table';

            public function getPrioritySort(): string
            {
                return 'DESC';
            }
            public function getIdSort(): string
            {
                return 'ASC';
            }
        };

        $models = $model->get();

        $this->assertEquals($models[0]->name, 'first');
        $this->assertEquals($models[1]->name, 'fourth');
        $this->assertEquals($models[2]->name, 'third');
        $this->assertEquals($models[3]->name, 'second');
    }
}
