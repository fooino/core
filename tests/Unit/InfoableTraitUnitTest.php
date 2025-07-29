<?php

namespace Fooino\Core\Tests\Unit;

use Astrotomic\Translatable\Contracts\Translatable as ContractsTranslatable;
use Astrotomic\Translatable\Translatable;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Infoable;
use Illuminate\Database\Eloquent\Model;
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
            $table->timestamps();
        });

        Schema::create('product_translations_table', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->string('locale');
            $table->string('name');
            $table->timestamps();
        });

        User::create([
            'name'  => 'John'
        ]);

        Product::create();

        ProductTranslation::insert([
            [
                'product_id'    => 1,
                'locale'        => 'en',
                'name'          => 'foo'
            ],
            [
                'product_id'    => 1,
                'locale'        => 'fa',
                'name'          => 'bar'
            ],
        ]);
    }


    public function test_methods()
    {
        $user = User::find(1);
        $user2 = User2::find(1);
        $product = Product::find(1);
        $product2 = Product2::find(1);

        $this->assertTrue($user->objectNamespace() == 'Fooino\Core\Tests\Unit\User');
        $this->assertTrue($user->objectPackage() == 'Core');
        $this->assertTrue($user->objectClassName() == 'User');

        $this->assertTrue($product->objectNamespace() == 'Fooino\Core\Tests\Unit\Product');
        $this->assertTrue($product->objectClassName() == 'Product');

        $this->assertEquals(
            array_values($product->objectUsedTraits()),
            [
                'Fooino\Core\Traits\Infoable',
                'Astrotomic\Translatable\Translatable'
            ]
        );

        $this->assertFalse($user->objectUsedTranslatable());
        $this->assertFalse($user->objectUsedMediable());
        $this->assertTrue($product->objectUsedTranslatable());

        $this->assertEquals(
            $user->objectName(),
            [
                'name'  => 'John',
                'type'  => 'msg.user'
            ]
        );

        $this->assertEquals(
            $product->objectName(),
            [
                'name'  => 'foo',
                'type'  => 'msg.product'
            ]
        );

        $this->assertEquals(
            $user2->objectName(),
            [
                'name'  => 'msg.unknown',
                'type'  => 'msg.user2'
            ]
        );

        $this->assertEquals(
            $product2->objectName(),
            [
                'name'  => 'msg.unknown',
                'type'  => 'msg.product2'
            ]
        );
    }
}


class User extends Model
{
    use Infoable;

    protected $guarded = ['id'];

    protected $table = 'users_table';
};

class User2 extends User
{
    public function objectKeyName(): string
    {
        return 'title';
    }
};


class Product extends Model implements ContractsTranslatable
{
    use
        Infoable,
        Translatable;

    protected $guarded = ['id'];

    protected $table = 'products_table';

    public $translatedAttributes = ['name'];
};

class Product2 extends Product
{
    public function objectKeyName(): string
    {
        return 'title';
    }
};

class ProductTranslation extends Model
{
    use Infoable;

    protected $guarded = ['id'];

    protected $table = 'product_translations_table';
};

class Product2Translation extends ProductTranslation {};
