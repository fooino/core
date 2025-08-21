<?php


namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Actions\Admin\GetPaginatedTrashListAction;
use Fooino\Core\Models\Trash;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Dateable;
use Fooino\Core\Traits\Modelable;
use Fooino\Core\Traits\Trashable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;

class GetPaginatedTrashListActionUnitTest extends TestCase
{

    use DatabaseMigrations;

    public $product;

    public function setUp(): void
    {
        parent::setUp();

        Schema::create('products_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        $this->product = new class extends Model
        {
            use
                SoftDeletes,
                Dateable,
                Trashable,
                Modelable;

            protected $guarded = ['id'];
            protected $table = 'products_table';
        };

        $this->product->create([
            'name' => 'product 1'
        ]);

        $this->product->create([
            'name' => 'product 2'
        ]);
    }


    public function test_get_trash_list_action()
    {
        $this->product->first()->delete();

        $this->assertTrue(app(GetPaginatedTrashListAction::class)->run()->count() == 0);

        Trash::find(1)->update([
            'removerable_type'    => 'Fooino\Admin\Models\Admin'
        ]);
        $this->assertTrue(app(GetPaginatedTrashListAction::class)->run()->items()[0]->trashed['name'] == 'product 1');
    }
}
