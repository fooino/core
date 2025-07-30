<?php


namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Actions\Admin\ChangePriorityAction;
use Fooino\Core\Events\ModelPriorityChangedEvent;
use Fooino\Core\Http\Requests\Admin\Priority\ChangePriorityRequest;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Infoable;
use Fooino\Core\Traits\Prioritiable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ChangePriorityActionUnitTest extends TestCase
{

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        Schema::create('users_table', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->integer('priority')->default(0);

            $table->timestamps();
        });


        $this->user = new class extends User
        {
            use
                Infoable,
                Prioritiable;

            protected $guarded = ['id'];
            protected $table = 'users_table';
        };


        $this->user->insert([
            [
                'name'      => 'first',
                'priority'  => 0,
            ],
            [
                'name'      => 'second',
                'priority'  => 10,
            ],
        ]);
    }


    public function test_the_validation()
    {

        $withoutInfoable = new class extends User
        {
            use
                Prioritiable;

            protected $guarded = ['*'];
            protected $table = 'users_table';
        };

        $withoutPrioritiable = new class extends User
        {
            use
                Infoable;

            protected $guarded = ['*'];
            protected $table = 'users_table';
        };

        $withoutPermission = new class extends User
        {
            use
                Infoable,
                Prioritiable;

            protected $guarded = ['*'];
            protected $table = 'users_table';
        };


        $this->assertThrows(
            fn() => resolveRequest(ChangePriorityRequest::class),
            ValidationException::class,
            'The model field is required'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: ChangePriorityRequest::class,
                data: [
                    'model' => 'foobar'
                ]
            ),
            ValidationException::class,
            'msg.modelIsInvalid'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: ChangePriorityRequest::class,
                data: [
                    'model' => get_class($withoutInfoable)
                ]
            ),
            ValidationException::class,
            'msg.modelIsInvalid'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: ChangePriorityRequest::class,
                data: [
                    'model' => get_class($withoutPrioritiable)
                ]
            ),
            ValidationException::class,
            'msg.modelIsInvalid'
        );

        $this->assertThrows(
            fn() => resolveRequest(
                request: ChangePriorityRequest::class,
                data: [
                    'model'     => get_class($withoutPermission),
                    'model_id'  => 1,
                    'priority'  => 100
                ]
            ),
            AuthorizationException::class,
            'msg.unauthorizedToChangePriority'
        );

        $user = $this->user->first();
        request()->setUserResolver(fn() => $user);

        Gate::define((lcfirst(class_basename($withoutPermission))) . '-update', function ($user) {
            return true;
        });

        $this->assertTrue(resolveRequest(
            request: ChangePriorityRequest::class,
            data: [
                'model'     => get_class($withoutPermission),
                'model_id'  => 1,
                'priority'  => 100
            ],
            user: $user
        ) instanceof ChangePriorityRequest);


        Gate::define((lcfirst(class_basename($withoutPermission))) . '-update', function ($user) {
            return false;
        });

        $this->assertThrows(
            fn() => resolveRequest(
                request: ChangePriorityRequest::class,
                data: [
                    'model'     => get_class($withoutPermission),
                    'model_id'  => 1,
                    'priority'  => 100
                ]
            ),
            AuthorizationException::class,
            'msg.unauthorizedToChangePriority'
        );
    }



    public function test_the_change_priority_action()
    {
        $user = $this->user->first();
        request()->setUserResolver(fn() => $user);

        Gate::define((lcfirst(class_basename($this->user))) . '-update', function ($user) {
            return true;
        });

        $request = resolveRequest(
            request: ChangePriorityRequest::class,
            data: [
                'model'     => get_class($this->user),
                'model_id'  => 1,
                'priority'  => 100
            ],
            user: $user
        );


        Event::fake();

        app(ChangePriorityAction::class)->run(request: $request);

        Event::assertDispatched(ModelPriorityChangedEvent::class);

        $this->assertDatabaseHas('users_table', [
            'id'    => 1,
            'priority'  => 100
        ]);

        Event::fake();

        app(ChangePriorityAction::class)->run(request: $request);

        Event::assertNotDispatched(ModelPriorityChangedEvent::class);
    }
}
