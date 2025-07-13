<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SearchableUnitTest extends TestCase
{

    public $user;

    public function setUp(): void
    {
        parent::setUp();


        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('state', ['DEFAULT', 'UNDEFAULT']);
            $table->enum('status', ['ACTIVE', 'INACTIVE']);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });


        $this->user = new class extends Model
        {
            use Searchable;

            protected $guarded = ['id'];

            protected $table = 'users_table';
        };

        $this->user->insert([
            [
                'name'      => 'first',
                'state'     => 'UNDEFAULT',
                'status'    => 'INACTIVE',
                'priority'  => 300
            ],
            [
                'name'      => 'second',
                'state'     => 'DEFAULT',
                'status'    => 'ACTIVE',
                'priority'  => 300
            ],
            [
                'name'      => 'third',
                'state'     => 'UNDEFAULT',
                'status'    => 'ACTIVE',
                'priority'  => 300
            ],
            [
                'name'      => 'fourth',
                'state'     => 'UNDEFAULT',
                'status'    => 'ACTIVE',
                'priority'  => 100
            ],
        ]);
    }



    public function test_sort_by_state_and_status_scope() {

        $users = $this->user->sortByStateAndStatus()->get();

        $this->assertTrue($users[0]->name == 'second');
        $this->assertTrue($users[1]->name == 'fourth');
        $this->assertTrue($users[2]->name == 'third');
        $this->assertTrue($users[3]->name == 'first');

        $users = $this->user->sortByStateAndStatus(byPriority: false)->get();

        $this->assertTrue($users[0]->name == 'second');
        $this->assertTrue($users[1]->name == 'third');
        $this->assertTrue($users[2]->name == 'fourth');
        $this->assertTrue($users[3]->name == 'first');

    }
}
