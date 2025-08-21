<?php

namespace Fooino\Core\Tests\Unit;

use Astrotomic\Translatable\Contracts\Translatable as ContractsTranslatable;
use Astrotomic\Translatable\Translatable;
use Fooino\Core\Traits\Trashable;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Modelable;
use Fooino\Core\Traits\Prioritiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModelableTraitUnitTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();


        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('products_table', function (Blueprint $table) {
            $table->id();
            $table->integer('priority')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('product_translations_table', function (Blueprint $table) {
            $table->id();
            $table->integer('modelable_product_id');
            $table->string('locale');
            $table->string('name');
            $table->timestamps();
        });

        ModelableUser::create([
            'name'  => 'John'
        ]);

        ModelableProduct::create();

        ModelableProductTranslation::create([
            'modelable_product_id'       => 1,
            'locale'                     => 'EN',
            'name'                       => 'foo'
        ]);

        ModelableProductTranslation::insert([
            [
                'modelable_product_id'      => 1,
                'locale'                    => 'FA',
                'name'                      => 'bar'
            ],
        ]);
    }


    public function test_methods()
    {
        $user = ModelableUser::find(1);
        $user2 = ModelableUser2::find(1);
        $product = ModelableProduct::find(1);
        $product2 = ModelableProduct2::find(1);

        $this->assertDatabaseHas(
            'product_translations_table',
            [
                'modelable_product_id'       => 1,
                'locale'                     => 'en',
                'name'                       => 'foo'
            ]
        );

        $this->assertDatabaseHas(
            'product_translations_table',
            [
                'modelable_product_id'       => 1,
                'locale'                     => 'FA',
                'name'                       => 'bar'
            ]
        );

        $this->assertTrue(ModelableProductTranslation::find(1)->locale == 'en');
        $this->assertTrue(ModelableProductTranslation::find(2)->locale == 'fa');

        $this->assertTrue($user->objectNamespace() == 'Fooino\Core\Tests\Unit\ModelableUser');
        $this->assertTrue($user->objectPackage() == 'Core');
        $this->assertTrue($user->objectClassName() == 'ModelableUser');

        $this->assertTrue($product->objectNamespace() == 'Fooino\Core\Tests\Unit\ModelableProduct');
        $this->assertTrue($product->objectClassName() == 'ModelableProduct');

        $this->assertEquals(
            array_values($product->objectUsedTraits()),
            [
                'Fooino\Core\Traits\Modelable',
                'Astrotomic\Translatable\Translatable',
                'Illuminate\Database\Eloquent\SoftDeletes',
                'Fooino\Core\Traits\Trashable',
                'Fooino\Core\Traits\Prioritiable'
            ]
        );

        $this->assertFalse($user->objectUsedSoftDeletes());
        $this->assertFalse($user->objectUsedTranslatable());
        $this->assertFalse($user->objectUsedTrashable());
        $this->assertFalse($user->objectUsedPrioritiable());
        $this->assertFalse($user->objectUsedMediable());

        $this->assertTrue($product->objectUsedSoftDeletes());
        $this->assertTrue($product->objectUsedTranslatable());
        $this->assertTrue($product->objectUsedTrashable());
        $this->assertTrue($product->objectUsedPrioritiable());


        $this->assertEquals(
            $user->objectName(),
            [
                'name'  => 'John',
                'type'  => 'msg.modelableUser'
            ]
        );

        $this->assertEquals(
            $product->objectName(),
            [
                'name'  => 'foo',
                'type'  => 'msg.modelableProduct'
            ]
        );

        $this->assertEquals(
            $user2->objectName(),
            [
                'name'  => 'msg.unknown',
                'type'  => 'msg.modelableUser2'
            ]
        );

        $this->assertEquals(
            $product2->objectName(),
            [
                'name'  => 'msg.unknown',
                'type'  => 'msg.modelableProduct2'
            ]
        );
    }
}


class ModelableUser extends Model
{
    use Modelable;

    protected $guarded = ['id'];

    protected $table = 'users_table';
};

class ModelableUser2 extends ModelableUser
{
    public function objectKeyName(): string
    {
        return 'title';
    }
};


class ModelableProduct extends Model implements ContractsTranslatable
{
    use
        Modelable,
        Translatable,
        SoftDeletes,
        Trashable,
        Prioritiable;

    protected $guarded = ['id'];

    protected $table = 'products_table';

    public $translatedAttributes = ['name'];
};

class ModelableProduct2 extends ModelableProduct
{
    public function objectKeyName(): string
    {
        return 'title';
    }
};

class ModelableProductTranslation extends Model
{
    use Modelable;

    protected $guarded = ['id'];

    protected $table = 'product_translations_table';
};

class ModelableProduct2Translation extends ModelableProductTranslation {};
