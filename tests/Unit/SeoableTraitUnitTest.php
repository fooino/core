<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Models\Tag;
use Fooino\Core\Facades\Json;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Seoable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;

class SeoableTraitUnitTest extends TestCase
{
    use DatabaseMigrations;

    public Model|null $post = null;

    public function setUp(): void
    {
        parent::setUp();

        Schema::create('posts_table', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('keywords')->nullable();
            $table->string('canonical')->nullable();
            $table->timestamps();
        });

        $this->post = new class extends Model
        {
            use Seoable;

            protected $guarded = ['id'];

            protected $table = 'posts_table';
        };
    }

    public function test_seoable_trait()
    {
        $this->post->create([
            'title'         => 'My first post',
            'keywords'      => ['laravel', 'php', 'javascript'],
        ]);

        $this->assertDatabaseHas('posts_table', [
            'title'     => 'My first post',
            'keywords'  => '["laravel","php","javascript"]',
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'laravel',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'php',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'javascript',
        ]);


        $this->assertTrue($this->post->find(1)->keywords_to_string == 'laravel,php,javascript');

        $this->post->create([
            'title'             => 'My second post',
            'slug'              => '',
            'meta_title'        => '',
            'meta_description'  => '',
            'keywords'          => '',
            'canonical'         => '',
        ]);

        $this->assertDatabaseHas('posts_table', [
            'title'             => 'My second post',
            'slug'              => null,
            'meta_title'        => null,
            'meta_description'  => null,
            'keywords'          => null,
            'canonical'         => null,
        ]);

        $this->assertTrue($this->post->find(2)->slug == '');
        $this->assertTrue($this->post->find(2)->meta_title == '');
        $this->assertTrue($this->post->find(2)->meta_description == '');
        $this->assertTrue($this->post->find(2)->keywords == []);
        $this->assertTrue($this->post->find(2)->canonical == '');


        $this->post->create([
            'title'             => 'My third post',
            'slug'              => 'the !is / a slug',
            'meta_title'        => 'foobar',
            'meta_description'  => 'fooobar',
            'keywords'          => ['foo', 'bar'],
            'canonical'         => 'https://example.com?q=foo bar&status=!foo',
        ]);

        $this->assertDatabaseHas('posts_table', [
            'title'             => 'My third post',
            'slug'              => 'the--is---a-slug',
            'meta_title'        => 'foobar',
            'meta_description'  => 'fooobar',
            'keywords'          => Json::encode(['foo', 'bar']),
            'canonical'         => 'https://example.com?q=foo_bar&status=_foo',
        ]);

        $this->assertEquals($this->post->latest('id')->first()->seo_response, [
            'slug'                          => 'the--is---a-slug',
            'meta_title'                    => 'foobar',
            'meta_description'              => 'fooobar',
            'keywords'                      => ['foo', 'bar'],
            'keywords_to_string'            => 'foo,bar',
            'canonical'                     => 'https://example.com?q=foo_bar&status=_foo',
        ]);
    }

    public function test_search_scope()
    {
        $this->post->create([
            'title'         => 'My first post',
            'keywords'      => ['laravel', 'php', 'javascript'],
        ]);
        $this->post->create([
            'title'         => 'My second post',
            'keywords'      => 'livewire',
        ]);

        $this->assertTrue($this->post->find(2)->keywords == ['livewire']);
        $this->assertDatabaseHas('posts_table', [
            'id'        => 2,
            'keywords'  => Json::encode(['livewire']),
        ]);

        $this->assertEquals(Tag::search('laravel')->count(), 1);
        $this->assertEquals(Tag::search(null)->count('id'), Tag::count('id'));
    }
}
