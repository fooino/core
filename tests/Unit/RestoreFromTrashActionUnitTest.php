<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Actions\Admin\RestoreFromTrashAction;
use Fooino\Core\Models\Trash;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Infoable;
use Fooino\Core\Traits\Trashable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class RestoreFromTrashActionUnitTest extends TestCase
{
    use DatabaseMigrations;

    public $product;
    public $user;

    public function setUp(): void
    {
        parent::setUp();

        Schema::create('products_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        $this->product = new class extends Model {

            use SoftDeletes,
                Infoable,
                Trashable;

            protected $table = 'products_table';

            protected $guarded = ['id'];
        };

        $this->user = new class extends User {

            use SoftDeletes,
                Infoable,
                Trashable;

            protected $table = 'users_table';

            protected $guarded = ['id'];
        };


        $this->product->create([
            'name' => 'Product 1',
        ]);

        $this->user->create([
            'name' => 'John Wick',
        ]);
    }


    public function test_the_move_to_trash_action()
    {
        $user = $this->user->first();
        request()->setUserResolver(fn() => $user);

        Gate::define((lcfirst(class_basename($this->product))) . '-restore', function ($user) {
            return false;
        });

        $this->product->find(1)->delete();

        $this->assertDatabaseHas('trashes', [
            'id'                    => 1,
            'trashable_type'        => get_class($this->product),
            'trashable_id'          => 1,
            'removerable_type'      => get_class($this->user),
            'removerable_id'        => 1,
        ]);

        $this->assertDatabaseHas('products_table', [
            'id'                    => 1,
            'deleted_at'            => currentDate()
        ]);

        $trash = Trash::find(1);

        $this->assertThrows(
            fn() => app(RestoreFromTrashAction::class)->run(trash: $trash),
            AuthorizationException::class,
            'msg.unauthorizedToRestoreFromTrash'
        );

        Gate::define((lcfirst(class_basename($this->product))) . '-restore', function ($user) {
            return true;
        });

        $this->assertTrue(app(RestoreFromTrashAction::class)->run(trash: $trash));


        $this->assertDatabaseHas('products_table', [
            'id'                    => 1,
            'deleted_at'            => null
        ]);

        $this->assertDatabaseMissing('trashes', [
            'id'                    => 1,
            'trashable_type'        => get_class($this->product),
            'trashable_id'          => 1,
            'removerable_type'      => get_class($this->user),
            'removerable_id'        => 1,
        ]);
    }
}
