<?php

namespace Fooino\Core\Tests\Unit;

use Astrotomic\Translatable\Contracts\Translatable as ContractsTranslatable;
use Astrotomic\Translatable\Translatable;
use Fooino\Core\Traits\Trashable;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Infoable;
use Fooino\Core\Traits\Prioritiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InfoableTraitUnitTest extends TestCase
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
            $table->integer('infoable_product_id');
            $table->string('locale');
            $table->string('name');
            $table->timestamps();
        });

        InfoableUser::create([
            'name'  => 'John'
        ]);

        InfoableProduct::create();

        InfoableProductTranslation::insert([
            [
                'infoable_product_id'       => 1,
                'locale'                    => 'en',
                'name'                      => 'foo'
            ],
            [
                'infoable_product_id'       => 1,
                'locale'                    => 'fa',
                'name'                      => 'bar'
            ],
        ]);
    }


    public function test_methods()
    {
        $user = InfoableUser::find(1);
        $user2 = InfoableUser2::find(1);
        $product = InfoableProduct::find(1);
        $product2 = InfoableProduct2::find(1);

        $this->assertTrue($user->objectNamespace() == 'Fooino\Core\Tests\Unit\InfoableUser');
        $this->assertTrue($user->objectPackage() == 'Core');
        $this->assertTrue($user->objectClassName() == 'InfoableUser');

        $this->assertTrue($product->objectNamespace() == 'Fooino\Core\Tests\Unit\InfoableProduct');
        $this->assertTrue($product->objectClassName() == 'InfoableProduct');

        $this->assertEquals(
            array_values($product->objectUsedTraits()),
            [
                'Fooino\Core\Traits\Infoable',
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
                'type'  => 'msg.infoableUser'
            ]
        );

        $this->assertEquals(
            $product->objectName(),
            [
                'name'  => 'foo',
                'type'  => 'msg.infoableProduct'
            ]
        );

        $this->assertEquals(
            $user2->objectName(),
            [
                'name'  => 'msg.unknown',
                'type'  => 'msg.infoableUser2'
            ]
        );

        $this->assertEquals(
            $product2->objectName(),
            [
                'name'  => 'msg.unknown',
                'type'  => 'msg.infoableProduct2'
            ]
        );
    }
}


class InfoableUser extends Model
{
    use Infoable;

    protected $guarded = ['id'];

    protected $table = 'users_table';
};

class InfoableUser2 extends InfoableUser
{
    public function objectKeyName(): string
    {
        return 'title';
    }
};


class InfoableProduct extends Model implements ContractsTranslatable
{
    use
        Infoable,
        Translatable,
        SoftDeletes,
        Trashable,
        Prioritiable;

    protected $guarded = ['id'];

    protected $table = 'products_table';

    public $translatedAttributes = ['name'];
};

class InfoableProduct2 extends InfoableProduct
{
    public function objectKeyName(): string
    {
        return 'title';
    }
};

class InfoableProductTranslation extends Model
{
    use Infoable;

    protected $guarded = ['id'];

    protected $table = 'product_translations_table';
};

class InfoableProduct2Translation extends InfoableProductTranslation {};
