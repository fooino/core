<?php


namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Actions\Admin\GetTrashListAction;
use Fooino\Core\Models\Trash;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Infoable;
use Fooino\Core\Traits\Trashable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class GetTrashListActionUnitTest extends TestCase
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


        $this->product = new class extends Model
        {
            use
                SoftDeletes,
                Trashable,
                Infoable;

            protected $guarded = ['id'];
            protected $table = 'products_table';
        };

        $this->user = new class extends User
        {
            protected $guarded = ['id'];
            protected $table = 'users_table';
        };

        $this->product->create([
            'name' => 'product 1'
        ]);

        $this->product->create([
            'name' => 'product 2'
        ]);

        $this->user->create([
            'name'  => 'John'
        ]);
    }


    public function test_get_trash_list_action()
    {
        $this->assertTrue(app(GetTrashListAction::class)->run()->count() == 0); // empty

        $this->product->first()->delete();

        $user = $this->user->first();
        request()->setUserResolver(fn() => $user);

        $can = ucfirst(class_basename($this->product)) . '-delete';

        Gate::define($can, function ($user) {
            return false;
        });

        $this->assertTrue(app(GetTrashListAction::class)->run()->count() == 0); // has permission but not deleted by admin


        Trash::find(1)->update([
            'removerable_type'    => 'Fooino\Admin\Models\Admin'
        ]);

        $this->assertTrue(app(GetTrashListAction::class)->run()->count() == 0); // has not permission


        Gate::define($can, function ($user) {
            return true;
        });

        $this->assertTrue(app(GetTrashListAction::class)->run()->count() == 1); // everything is fine
    }
}
