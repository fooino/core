<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;
use PDO;

class FullTextSearchUnitTest extends TestCase
{
    public Model|null $model = null;

    public function test_the_trait_can_not_search_in_names_since_database_not_support_full_text_search_index()
    {
        $this->setModelAndDb();
        $this->assertEquals($this->model->fullTextSearch('سبز')->get()->first()->name, "گام به گام چهارم خیلی سبز");
        $this->assertEquals($this->model->orFullTextSearch('سبز')->get()->first()->name, "گام به گام چهارم خیلی سبز");
    }

    public function test_the_trait_can_not_search_since_the_searchable_array_is_empty()
    {
        if ($this->mysqlConnection()) {
            $this->model->searchable = [];
            $this->assertEquals($this->model->fullTextSearch('سبز')->get()->first()->name, "گام به گام چهارم خیلی سبز");
            $this->assertEquals($this->model->orFullTextSearch('سبز')->get()->first()->name, "گام به گام چهارم خیلی سبز");
        }
    }

    public function test_full_text_search()
    {
        if ($this->mysqlConnection()) {
            $users = $this->model->fullTextSearch('گام به گام چهارم سبز')->get()->pluck('name')->toArray();
            $this->assertEquals(count($users), 3);
            $this->assertEquals($users[0], "گام به گام چهارم خیلی سبز");
            $this->assertEquals($users[1], "گام به گام پنجم خیلی سبز");
            $this->assertEquals($users[2], "خیلی سبز");
        }
    }

    public function test_or_full_text_search()
    {
        if ($this->mysqlConnection()) {
            $users = $this->model->orFullTextSearch('گام به گام پنجم')->get()->pluck('name')->toArray();
            $this->assertEquals(count($users), 2);
            $this->assertEquals($users[0], "گام به گام پنجم خیلی سبز");
            $this->assertEquals($users[1], "گام به گام چهارم خیلی سبز");
        }
    }

    public function setModelAndDb(): void
    {
        Schema::dropIfExists('users_table');
        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $this->model = new class extends Model
        {
            use FullTextSearch;

            protected $guarded = ['id'];

            protected $table = 'users_table';

            public $searchable = ['name'];
        };

        $this->model->insert([
            [
                'name'      => "گام به گام چهارم خیلی سبز",
            ],
            [
                'name'      => "گام به گام پنجم خیلی سبز",
            ],
            [
                'name'      => "خیلی سبز",
            ],
            [
                'name'      => "خیلی",
            ],
        ]);
    }

    public function mysqlConnection(): bool
    {
        try {
            $this->configMysqlDb();
            $dbconnect = DB::connection()->getPDO();
            $dbname = DB::connection()->getDatabaseName();

            $this->setModelAndDb();
            $this->setNameFullTextSearchIndex();

            return true;
        } catch (Exception $e) {
            echo "\n\n MySQL connection is not established for this test.\n\n";
            return false;
        }
    }

    public function setNameFullTextSearchIndex(): void
    {
        DB::statement('ALTER TABLE `users_table` ADD FULLTEXT INDEX user_name_index (`name`)');
    }

    public function configMysqlDb(): void
    {
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql' => [
            'driver'            => 'mysql',
            'url'               => null,
            'host'              => '127.0.0.1',
            'port'              => '3306',
            'database'          => env('TESTING_DB_DATABASE', 'fooino-testing'),
            'username'          => env('TESTING_DB_USERNAME', 'root'),
            'password'          => env('TESTING_DB_PASSWORD', 'Fooino!123'),
            'unix_socket'       => '',
            'charset'           => 'utf8mb4',
            'collation'         => 'utf8mb4_unicode_ci',
            'prefix'            => '',
            'prefix_indexes'    => true,
            'strict'            => false,
            'engine'            => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]]);
    }
}
