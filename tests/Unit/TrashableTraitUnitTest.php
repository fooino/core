<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Trashable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class TrashableTraitUnitTest extends TestCase
{
    use DatabaseMigrations;

    public Model|null $model = null;

    public function setUp(): void
    {
        parent::setUp();

        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        $this->model = new class extends User
        {
            use
                SoftDeletes,
                Trashable;

            protected $guarded = ['id'];

            protected $table = 'users_table';

            public function modelKeyName(): string
            {
                return 'name';
            }

            public function permission(): bool
            {
                return true;
            }
        };

        $this->model->insert([
            [
                'id'        => 1,
                'name'      => 'foo',
            ],
            [
                'id'        => 2,
                'name'      => 'bar',
            ],
        ]);
    }

    public function test_get_trahsed_list_method()
    {
        $this->model->find(1)->delete();

        $this->assertCount(1, $this->model->trashedList());
        $this->assertEquals('foo', $this->model->trashedList()->first()->name);
        $this->assertEquals(1, $this->model->trashedCount());
        $this->assertSoftDeleted('users_table', ['id' => 1]);
    }

    public function test_restore_method()
    {
        $model = $this->model->find(1);
        $model->delete();

        $deletedModel = $this->model->getTrashById(1);
        $this->assertEquals($deletedModel->id, $model->id);

        $this->assertSoftDeleted('users_table', ['id' => 1]);

        $deletedModel->restoreFromTrash();
        $this->assertNotSoftDeleted('users_table', ['id' => 1]);
    }

    public function test_permission_method()
    {
        $model = new class extends Model
        {
            use
                SoftDeletes,
                Trashable;

            protected $guarded = ['id'];

            protected $table = 'users_table';

            public function modelKeyName(): string
            {
                return 'name';
            }

            public function permission(): bool
            {
                return false;
            }
        };

        $this->assertThrows(fn() => $model->trashedList(), AuthorizationException::class);
        $this->assertThrows(fn() =>  $model->trashedCount(), AuthorizationException::class);

        $model->delete();
        $this->assertThrows(fn() => $model->restoreFromTrash(), AuthorizationException::class);
    }

    public function test_check_permission_by_key_method()
    {
        $this->assertFalse($this->model->checkPermissionByKey(null));
        $this->assertFalse($this->model->checkPermissionByKey('test'));

        request()->setUserResolver(fn() => $this->model->first());
        $this->assertFalse($this->model->checkPermissionByKey('test'));

        Gate::define('test', function ($user) {
            return false;
        });
        $this->assertFalse($this->model->checkPermissionByKey('test'));

        Gate::define('test', function ($user) {
            return true;
        });
        $this->assertTrue($this->model->checkPermissionByKey('test'));
        $this->assertTrue($this->model->checkPermissionByKey('can:test'));
    }
}
