<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tasks\Tools\GenerateCodeTask;
use Fooino\Core\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Exception;

class GenerateCodeTaskUnitTest extends TestCase
{
    public function test_the_task_can_generate_numeric_OTP_code()
    {
        $code = app(GenerateCodeTask::class)->length(4)->isNumeric(true)->run();

        $this->assertEquals(strlen($code), 4);
        $this->assertTrue(is_numeric($code));
    }

    public function test_the_task_can_generate_random_token()
    {
        $code = app(GenerateCodeTask::class)->length(20)->isNumeric(false)->run();

        $this->assertEquals(strlen($code), 20);
        $this->assertTrue(is_string($code));
    }

    public function test_the_task_can_generate_token_in_timestamp_style()
    {
        $token = app(GenerateCodeTask::class)->length(6)->timestampStyle(true)->run();
        $this->assertTrue((bool)preg_match('/[\w]{6}[\d]{1,}[\w\d]{6}/', $token));
    }

    public function test_the_task_can_generate_random_token_in_lower_case()
    {
        $code = app(GenerateCodeTask::class)->length(6)->isNumeric(false)->lowerCase()->run();

        $this->assertEquals(strlen($code), 6);
        $this->assertTrue(is_string($code));

        $upperCase = range('A', 'Z');

        foreach (str_split($code) as $letter) {
            $this->assertFalse(in_array($letter, $upperCase));
        }
    }
    public function test_the_task_can_generate_random_token_in_upper_case()
    {
        $code = app(GenerateCodeTask::class)->length(6)->isNumeric(false)->upperCase()->run();

        $this->assertEquals(strlen($code), 6);
        $this->assertTrue(is_string($code));

        $lowerCase = range('a', 'z');
        
        foreach (str_split($code) as $letter) {
            $this->assertFalse(in_array($letter, $lowerCase));
        }
    }

    public function test_the_task_generate_unique_random_token()
    {
        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('token');
        });

        $model = new class extends Model
        {
            protected $table = 'users_table';
        };

        $code = app(GenerateCodeTask::class)->model($model)->field('token')->length(20)->isNumeric(false)->run();

        $this->assertEquals(strlen($code), 20);
        $this->assertTrue(filled($code));
    }

    public function test_the_task_throw_exception_to_generate_unique_random_token_after_many_attempts()
    {
        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->timestamps();
        });

        $model = new class extends Model
        {
            protected $guarded = ['id'];

            protected $table = 'users_table';
        };

        $inserts = [];
        
        $tokens = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));

        foreach ($tokens as $token) {
            $inserts[]['token'] = $token;
        }

        $model->insert($inserts);

        $this->assertThrows(
            function () use ($model) {
                // set length to 1 to have the exception 
                $code = app(GenerateCodeTask::class)
                    ->model(get_class($model))
                    ->field('token')
                    ->length(1)
                    ->isNumeric(false)
                    ->run();
            },
            Exception::class,
            "The task attempts more than 100 times and can not generate code anymore"
        );
    }

    public function test_length_method()
    {
        $this->assertThrows(
            fn() =>
            app(GenerateCodeTask::class)
                ->length(0)
                ->run(),
            Exception::class,
            'The length must greater than zero'
        );

        $this->assertThrows(
            fn() =>
            app(GenerateCodeTask::class)
                ->isNumeric(false)
                ->length(101)
                ->isNumeric(true)
                ->run(),
            Exception::class,
            'No more than 100 digits can be produced'
        );

        $this->assertThrows(
            fn() =>
            app(GenerateCodeTask::class)
                ->length(1001)
                ->isNumeric(false)
                ->run(),
            Exception::class,
            'No more than 1000 characters can be produced'
        );


        $this->assertTrue(strlen(app(GenerateCodeTask::class)
            ->length(100)
            ->isNumeric(true)
            ->run()) == 100);

        $this->assertTrue(strlen(app(GenerateCodeTask::class)
            ->length(1000)
            ->isNumeric(false)
            ->run()) == 1000);
    }
}
