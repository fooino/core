<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Actions\Admin\MoveToTrashAction;
use Fooino\Core\Exceptions\ResolveRequestValidationException;
use Fooino\Core\Http\Requests\Admin\Trash\MoveToTrashRequest;
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

class MoveToTrashActionUnitTest extends TestCase
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


    public function test_the_move_to_trash_request_validation()
    {

        $withoutInfoable = new class extends User
        {
            use
                SoftDeletes,
                Trashable;

            protected $guarded = ['*'];
            protected $table = 'users_table';
        };

        $withoutTrashable = new class extends User
        {
            use
                SoftDeletes,
                Infoable;

            protected $guarded = ['*'];
            protected $table = 'users_table';
        };

        $withoutPermission = new class extends User
        {
            use
                SoftDeletes,
                Infoable,
                Trashable;

            protected $guarded = ['*'];
            protected $table = 'users_table';
        };


        $this->assertThrows(
            fn() => resolveRequest(MoveToTrashRequest::class),
            ResolveRequestValidationException::class,
            'The model field is required'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: MoveToTrashRequest::class,
                data: [
                    'model' => '   '
                ]
            ),
            ResolveRequestValidationException::class,
            'The model field is required'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: MoveToTrashRequest::class,
                data: [
                    'model' => 'foobar'
                ]
            ),
            ResolveRequestValidationException::class,
            'msg.modelIsInvalid'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: MoveToTrashRequest::class,
                data: [
                    'model' => get_class($withoutInfoable)
                ]
            ),
            ResolveRequestValidationException::class,
            'msg.modelIsInvalid'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: MoveToTrashRequest::class,
                data: [
                    'model' => get_class($withoutTrashable)
                ]
            ),
            ResolveRequestValidationException::class,
            'msg.modelIsInvalid'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: MoveToTrashRequest::class,
                data: [
                    'model'     => get_class($withoutPermission),
                    'model_id'  => 1,
                ]
            ),
            AuthorizationException::class,
            'msg.unauthorizedToMoveToTrash'
        );

        $user = $this->user->first();
        request()->setUserResolver(fn() => $user);

        Gate::define((lcfirst(class_basename($withoutPermission))) . '-delete', function ($user) {
            return true;
        });

        $this->assertTrue($withoutPermission->hasMoveToTrashPermission);

        $this->assertTrue(resolveRequest(
            request: MoveToTrashRequest::class,
            data: [
                'model'     => get_class($withoutPermission),
                'model_id'  => 1,
            ],
            user: $user
        ) instanceof MoveToTrashRequest);


        Gate::define((lcfirst(class_basename($withoutPermission))) . '-delete', function ($user) {
            return false;
        });


        $this->assertTrue($withoutPermission->hasMoveToTrashPermission); // !! since we used once the changing gate in runtime does not change the accessor

        $this->assertThrows(
            fn() => resolveRequest(
                request: MoveToTrashRequest::class,
                data: [
                    'model'     => get_class($withoutPermission),
                    'model_id'  => 1,
                ]
            ),
            AuthorizationException::class,
            'msg.unauthorizedToMoveToTrash'
        );
    }


    public function test_the_move_to_trash_action()
    {
        $user = $this->user->first();
        request()->setUserResolver(fn() => $user);

        Gate::define((lcfirst(class_basename($this->product))) . '-delete', function ($user) {
            return true;
        });

        $request = resolveRequest(
            request: MoveToTrashRequest::class,
            data: [
                'model'     => get_class($this->product),
                'model_id'  => 1,
            ],
            user: $user
        );


        $this->assertTrue(app(MoveToTrashAction::class)->run(request: $request));


        $this->assertDatabaseHas('products_table', [
            'id'                    => 1,
            'deleted_at'            => currentDate()
        ]);

        $this->assertDatabaseHas('trashes', [
            'id'                    => 1,
            'trashable_type'        => get_class($this->product),
            'trashable_id'          => 1,
            'removerable_type'      => get_class($this->user),
            'removerable_id'        => 1,
        ]);
    }
}
