<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Models\Trash;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Dateable;
use Fooino\Core\Traits\Infoable;
use Fooino\Core\Traits\Trashable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;

class TrashableTraitUnitTest extends TestCase
{
    use DatabaseMigrations;


    public $user;
    public $product;

    public function setUp(): void
    {
        parent::setUp();


        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('products_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });


        $this->user = new class extends User
        {
            use
                SoftDeletes,
                Infoable;

            protected $guarded = ['id'];

            protected $table = 'users_table';
        };

        $this->product = new class extends Model
        {

            use
                SoftDeletes,
                Trashable,
                Infoable,
                Dateable;

            protected $guarded = ['id'];

            protected $table = 'products_table';
        };

        $this->user->create([
            'name'  => 'John'
        ]);

        $this->product->insert([
            [
                'name'  => 'CellPhone'
            ],
            [
                'name'  => 'TV'
            ]
        ]);

        // 
    }

    public function test_add_to_trash_method()
    {
        $this->product->find(1)->delete();

        $this->assertDatabaseHas('trashes', [
            'trashable_type'    => get_class($this->product),
            'trashable_id'      => 1,
            'removerable_type'  => null,
            'removerable_id'    => null,
        ]);

        $this->product->withTrashed()->find(1)->update([
            'deleted_at'    => null
        ]);

        // moved to trash once
        $this->product->find(1)->delete();

        $this->assertTrue(Trash::where('trashable_id', 1)->where('trashable_type', get_class($this->product))->count('id') == 1); // the move to trash delete once the product since we used firstOrCreate


        request()->setUserResolver(fn() => $this->user->find(1));
        $this->product->find(2)->delete();

        $this->assertDatabaseHas('trashes', [
            'trashable_type'    => get_class($this->product),
            'trashable_id'      => 2,
            'removerable_type'  => get_class($this->user),
            'removerable_id'    => 1,
        ]);
    }

    public function test_remove_from_trash_method()
    {
        $this->product->find(1)->delete();

        $this->assertDatabaseHas('products_table', [
            'id'            => 1,
            'name'          => 'CellPhone',
            'deleted_at'    => currentDate(),
        ]);

        $this->assertDatabaseHas('trashes', [
            'trashable_type'    => get_class($this->product),
            'trashable_id'      => 1,
        ]);

        $this->product->withTrashed()->find(1)->restore();


        $this->assertDatabaseHas('products_table', [
            'id'            => 1,
            'name'          => 'CellPhone',
            'deleted_at'    => null,
        ]);

        $this->assertDatabaseMissing('trashes', [
            'trashable_type'    => get_class($this->product),
            'trashable_id'      => 1,
        ]);
    }


    public function test_trash_model_accessor_and_relations()
    {
        request()->setUserResolver(fn() => $this->user->find(1));

        $this->product->find(1)->delete();

        $trash = Trash::find(1);

        $this->assertEquals(
            $trash->trashed,
            [
                'id'            => 0,
                'name'          => 'msg.unknown',
                'type'          => '',
                'deleted_at'    => '',
                'deleted_at_tz' => '',
                'media'         => [],
            ]
        );

        $trash = Trash::with('trashable', 'removerable')->find(1);


        $this->assertEquals(
            $trash->trashed,
            [
                'id'                => 1,
                'name'              => 'CellPhone',
                'type'              => __('msg.' . str(class_basename($this->product))->camel()->value()),
                'deleted_at'        => $trash->trashable->deleted_at,
                'deleted_at_tz'     => $trash->trashable->deleted_at_tz,
                'media'             => [],
            ]
        );

        $this->assertEquals($trash->remover, userInfo(model: $trash, key: 'removerable'));


        $this->assertTrue(Trash::removedByAdmin()->count('id') == 0);
        Trash::find(1)->update([
            'removerable_type'  => 'Fooino\Admin\Models\Admin'
        ]);
        $this->assertTrue(Trash::removedByAdmin()->count('id') == 1);


        $user = new class extends User
        {
            use
                SoftDeletes,
                Trashable,
                Infoable;

            protected $guarded = ['id'];

            protected $table = 'users_table';
        };
        $user->find(1)->delete();

        $this->assertTrue(Trash::inTrashableType(null)->count('id') == 2);
        $this->assertTrue(Trash::inTrashableType([])->count('id') == 0);
        $this->assertTrue(Trash::inTrashableType('foobar')->count('id') == 0);
        $this->assertTrue(Trash::inTrashableType([get_class($user), get_class($this->product)])->count('id') == 2);
        $this->assertTrue(Trash::inTrashableType(get_class($user))->count('id') == 1);
    }
}
