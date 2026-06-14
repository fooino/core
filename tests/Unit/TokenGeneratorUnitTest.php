<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\FooinoException;
use Fooino\Core\Exceptions\InfiniteLoopException;
use Fooino\Core\Exceptions\TokenGeneratorException;
use Fooino\Core\Support\TokenGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

describe('TokenGenerator utilities', function () {

    test('getter and setter methods', function () {

        $user = new class extends Model {};

        expect(app(TokenGenerator::class)->getLength())->toBe(5);
        expect(app(TokenGenerator::class)->length(10)->getLength())->toBe(10);

        expect(app(TokenGenerator::class)->getModel())->toBe('');
        expect(app(TokenGenerator::class)->model('foobar')->getModel())->toBe('foobar');
        expect(app(TokenGenerator::class)->model($user)->getModel())->toBe(get_class($user));

        expect(app(TokenGenerator::class)->getField())->toBe('token');
        expect(app(TokenGenerator::class)->field('code')->getField())->toBe('code');

        expect(app(TokenGenerator::class)->getWhere())->toBe([]);
        expect(app(TokenGenerator::class)->where([['foo', '<>', 'bar']])->getWhere())->toBe([['foo', '<>', 'bar']]);
        expect(app(TokenGenerator::class)->where(['foo', '<>', 'bar'])->getWhere())->toBe([['foo', '<>', 'bar']]);

        expect(app(TokenGenerator::class)->getFormat())->toBe('numeric');
        expect(app(TokenGenerator::class)->alphaNumeric()->getFormat())->toBe('alphaNumeric');
        expect(app(TokenGenerator::class)->alphabet()->getFormat())->toBe('alphabet');
        expect(app(TokenGenerator::class)->weakPassword()->getFormat())->toBe('weakPassword');
        expect(app(TokenGenerator::class)->password()->getFormat())->toBe('password');
        expect(app(TokenGenerator::class)->strongPassword()->getFormat())->toBe('strongPassword');
    });

    test('numeric format', function () {

        expect(strlen(app(TokenGenerator::class)->token()))->toBe(5);

        expect(strlen(app(TokenGenerator::class)->length(10)->numeric()->token()))->toBe(10);

        expect(app(TokenGenerator::class)->length(10)->numeric()->token()[0])->not->toBe('0');

        expect(ctype_digit(app(TokenGenerator::class)->length(10)->numeric()->token()))->toBeTrue();

        expect(ctype_digit(app(TokenGenerator::class)->length(10)->lowercase()->numeric()->token()))->toBeTrue();
        expect(ctype_digit(app(TokenGenerator::class)->length(10)->uppercase()->numeric()->token()))->toBeTrue();
    });

    test('alphaNumeric format', function () {

        expect(strlen(app(TokenGenerator::class)->alphaNumeric()->length(16)->token()))->toBe(16);

        expect(ctype_alnum(app(TokenGenerator::class)->alphaNumeric()->length(16)->token()))->toBeTrue();

        expect(strpbrk(app(TokenGenerator::class)->alphaNumeric()->length(16)->lowercase()->token(), implode('', range('A', 'Z'))))->toBeFalse();
        expect(strpbrk(app(TokenGenerator::class)->alphaNumeric()->length(16)->uppercase()->token(), implode('', range('a', 'z'))))->toBeFalse();
    });

    test('alphabet format', function () {

        expect(strlen(app(TokenGenerator::class)->alphabet()->length(16)->token()))->toBe(16);

        expect(ctype_alpha(app(TokenGenerator::class)->alphabet()->length(16)->token()))->toBeTrue();

        expect(ctype_lower(app(TokenGenerator::class)->alphabet()->length(16)->lowercase()->token()))->toBeTrue();
        expect(ctype_upper(app(TokenGenerator::class)->alphabet()->length(16)->uppercase()->token()))->toBeTrue();
    });

    test('password format methods', function () {

        $symbols = ['~', '!', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '.', ',', '<', '>', '?', '/', '\\', '{', '}', '[', ']', '|', ':', ';'];

        expect(strlen(app(TokenGenerator::class)->weakPassword()->length(12)->token()))->toBe(12);

        expect(ctype_digit(app(TokenGenerator::class)->weakPassword()->length(12)->token()))->toBeTrue();
        expect(ctype_digit(app(TokenGenerator::class)->weakPassword()->length(12)->lowercase()->token()))->toBeTrue();
        expect(ctype_digit(app(TokenGenerator::class)->weakPassword()->length(12)->uppercase()->token()))->toBeTrue();

        expect(strpbrk(app(TokenGenerator::class)->weakPassword()->length(12)->token(), implode('', $symbols)))->toBeFalse();



        expect(strlen(app(TokenGenerator::class)->password()->length(12)->token()))->toBe(12);
        expect(ctype_alnum(app(TokenGenerator::class)->password()->length(12)->token()))->toBeTrue();

        expect(strpbrk(app(TokenGenerator::class)->password()->length(12)->lowercase()->token(), implode('', range('A', 'Z'))))->toBeFalse();
        expect(strpbrk(app(TokenGenerator::class)->password()->length(12)->uppercase()->token(), implode('', range('a', 'a'))))->toBeFalse();

        expect(strpbrk(app(TokenGenerator::class)->password()->length(12)->token(), implode('', $symbols)))->toBeFalse();




        expect(strlen(app(TokenGenerator::class)->strongPassword()->length(12)->token()))->toBe(12);

        expect(strpbrk(app(TokenGenerator::class)->strongPassword()->length(12)->lowercase()->token(), implode('', range('A', 'Z'))))->toBeFalse();
        expect(strpbrk(app(TokenGenerator::class)->strongPassword()->length(12)->uppercase()->token(), implode('', range('a', 'a'))))->toBeFalse();

        expect(strpbrk(app(TokenGenerator::class)->strongPassword()->length(12)->token(), implode('', $symbols)))->not->toBeFalse();
        // 
    });

    test('check token uniqueness', function () {

        Schema::create('users_table', function (Blueprint $table) {
            $table->id();
            $table->string('code');
        });

        $model = new class extends Model
        {
            protected $table = 'users_table';
        };

        $insert = [];
        foreach (range(0, 8) as $i) {
            $insert[]['code'] = $i;
        }

        $model->insert($insert);


        $token = app(TokenGenerator::class)->model($model)->field('code')->numeric()->length(1)->token();

        expect($token)->toBe('9'); // 0 - 8 is already inserted and the 9 is the last number with length 1
    });

    describe('handle exceptions', function () {

        test('generate make infinite loop', function () {

            Schema::create('users_table', function (Blueprint $table) {
                $table->id();
                $table->string('code');
            });

            $model = new class extends Model
            {
                protected $table = 'users_table';
            };

            $insert = [];
            foreach (range(0, 9) as $i) {
                $insert[]['code'] = $i;
            }

            $model->insert($insert);


            expect(fn() => app(TokenGenerator::class)->model($model)->field('code')->numeric()->length(1)->token())->toThrow(InfiniteLoopException::class);

            try {

                app(TokenGenerator::class)->model($model)->field('code')->numeric()->length(1)->token();

                // 
            } catch (FooinoException $e) {


                expect($e->getMessage())->toBe('msg.infiniteLoopExceptionInTokenGenerator');
                expect($e->getCode())->toBe(10202);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 101,
                    'length'    => 1,
                    'format'    => 'numeric'
                ]);
            }
        });

        test('length must be positive', function () {

            expect(fn() => app(TokenGenerator::class)->length(0)->token())->toThrow(TokenGeneratorException::class);
            expect(fn() => app(TokenGenerator::class)->length(-1)->token())->toThrow(TokenGeneratorException::class);

            try {

                app(TokenGenerator::class)->length(0)->token();

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionLengthMustBePositive');
                expect($e->getCode())->toBe(10401);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 0,
                    'format'    => 'numeric'
                ]);
            }

            try {

                app(TokenGenerator::class)->length(-1)->token();

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionLengthMustBePositive');
                expect($e->getCode())->toBe(10401);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => -1,
                    'format'    => 'numeric'
                ]);
            }
        });

        test('big length number', function () {

            expect(fn() => app(TokenGenerator::class)->length(256)->token())->toThrow(TokenGeneratorException::class);
            expect(fn() => app(TokenGenerator::class)->length(500)->token())->toThrow(TokenGeneratorException::class);

            try {

                app(TokenGenerator::class)->length(256)->token();

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionBigLengthNumber');
                expect($e->getCode())->toBe(10402);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 256,
                    'format'    => 'numeric'
                ]);
            }
        });

        test('password length number', function () {

            expect(fn() => app(TokenGenerator::class)->strongPassword()->length(5)->token())->toThrow(TokenGeneratorException::class);
            expect(fn() => app(TokenGenerator::class)->password()->length(5)->token())->toThrow(TokenGeneratorException::class);

            try {

                app(TokenGenerator::class)->strongPassword()->length(5)->token();

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionSmallLengthNumberForStrongPassword');
                expect($e->getCode())->toBe(10403);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 5,
                    'format'    => 'strongPassword'
                ]);
            }

            try {

                app(TokenGenerator::class)->password()->length(5)->token();

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionSmallLengthNumberForPassword');
                expect($e->getCode())->toBe(10404);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 5,
                    'format'    => 'password'
                ]);
            }
        });

        test('field is required', function () {

            expect(fn() => app(TokenGenerator::class)->model('foobar')->field('')->token())->toThrow(TokenGeneratorException::class);

            try {

                app(TokenGenerator::class)->model('foobar')->field('')->token();

                // 
            } catch (FooinoException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionFieldIsRequired');
                expect($e->getCode())->toBe(10405);
                expect($e->getLevel())->toBe('error');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 5,
                    'format'    => 'numeric'
                ]);
            }
        });
    });
});
