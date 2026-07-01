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

        expect(app(TokenGenerator::class)->getField())->toBe('');
        expect(app(TokenGenerator::class)->field('code')->getField())->toBe('code');

        expect(app(TokenGenerator::class)->getWhere())->toBe([]);
        expect(app(TokenGenerator::class)->where([])->getWhere())->toBe([]);
        expect(app(TokenGenerator::class)->where([['foo', '<>', 'bar']])->getWhere())->toBe([['foo', '<>', 'bar']]);
        expect(app(TokenGenerator::class)->where(['foo', '<>', 'bar'])->getWhere())->toBe([['foo', '<>', 'bar']]);
        expect(app(TokenGenerator::class)->where([['a', '=', 1], ['b', '>', 2]])->getWhere())->toBe([['a', '=', 1], ['b', '>', 2]]);

        expect(app(TokenGenerator::class)->getFormat())->toBe('numeric');
        expect(app(TokenGenerator::class)->alphaNumeric()->getFormat())->toBe('alphaNumeric');
        expect(app(TokenGenerator::class)->alphabet()->getFormat())->toBe('alphabet');
        expect(app(TokenGenerator::class)->weakPassword()->getFormat())->toBe('weakPassword');
        expect(app(TokenGenerator::class)->password()->getFormat())->toBe('password');
        expect(app(TokenGenerator::class)->strongPassword()->getFormat())->toBe('strongPassword');
        expect(app(TokenGenerator::class)->uuid4()->getFormat())->toBe('uuid4');
        expect(app(TokenGenerator::class)->uuid7()->getFormat())->toBe('uuid7');
        expect(app(TokenGenerator::class)->memorableOtp()->getFormat())->toBe('memorableOtp');

        expect(app(TokenGenerator::class)->getPipeline())->toBe([]);
        expect(app(TokenGenerator::class)->uppercase()->getPipeline())->toBe(['strtoupper']);
        expect(app(TokenGenerator::class)->lowercase()->getPipeline())->toBe(['strtolower']);
        expect(app(TokenGenerator::class)->lowercase()->uppercase()->getPipeline())->toBe(['strtolower', 'strtoupper']);
    });

    test('numeric format', function () {

        expect(strlen(app(TokenGenerator::class)->value()))->toBe(5);

        expect(strlen(app(TokenGenerator::class)->length(10)->numeric()->value()))->toBe(10);

        expect(app(TokenGenerator::class)->length(10)->numeric()->value()[0])->not->toBe('0');

        expect(ctype_digit(app(TokenGenerator::class)->length(10)->numeric()->value()))->toBeTrue();

        expect(ctype_digit(app(TokenGenerator::class)->length(10)->lowercase()->numeric()->value()))->toBeTrue();
        expect(ctype_digit(app(TokenGenerator::class)->length(10)->uppercase()->numeric()->value()))->toBeTrue();

        expect(strlen(app(TokenGenerator::class)->length(255)->numeric()->value()))->toBe(255);
    });

    test('alphaNumeric format', function () {

        expect(strlen(app(TokenGenerator::class)->alphaNumeric()->length(16)->value()))->toBe(16);

        expect(ctype_alnum(app(TokenGenerator::class)->alphaNumeric()->length(16)->value()))->toBeTrue();

        expect(strpbrk(app(TokenGenerator::class)->alphaNumeric()->length(16)->lowercase()->value(), implode('', range('A', 'Z'))))->toBeFalse();
        expect(strpbrk(app(TokenGenerator::class)->alphaNumeric()->length(16)->uppercase()->value(), implode('', range('a', 'z'))))->toBeFalse();
    });

    test('alphabet format', function () {

        expect(strlen(app(TokenGenerator::class)->alphabet()->length(16)->value()))->toBe(16);

        expect(ctype_alpha(app(TokenGenerator::class)->alphabet()->length(16)->value()))->toBeTrue();

        expect(ctype_lower(app(TokenGenerator::class)->alphabet()->length(16)->lowercase()->value()))->toBeTrue();
        expect(ctype_upper(app(TokenGenerator::class)->alphabet()->length(16)->uppercase()->value()))->toBeTrue();

        expect(ctype_upper(app(TokenGenerator::class)->alphabet()->length(16)->lowercase()->uppercase()->value()))->toBeTrue();
    });

    test('password format methods', function () {

        $symbols = ['~', '!', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '.', ',', '<', '>', '?', '/', '\\', '{', '}', '[', ']', '|', ':', ';'];

        expect(strlen(app(TokenGenerator::class)->weakPassword()->length(12)->value()))->toBe(12);

        expect(ctype_digit(app(TokenGenerator::class)->weakPassword()->length(12)->value()))->toBeTrue();
        expect(ctype_digit(app(TokenGenerator::class)->weakPassword()->length(12)->lowercase()->value()))->toBeTrue();
        expect(ctype_digit(app(TokenGenerator::class)->weakPassword()->length(12)->uppercase()->value()))->toBeTrue();

        expect(strpbrk(app(TokenGenerator::class)->weakPassword()->length(12)->value(), implode('', $symbols)))->toBeFalse();

        expect(app(TokenGenerator::class)->weakPassword()->length(12)->value()[0])->not->toBe('0');



        expect(strlen(app(TokenGenerator::class)->password()->length(12)->value()))->toBe(12);
        expect(ctype_alnum(app(TokenGenerator::class)->password()->length(12)->value()))->toBeTrue();

        expect(strpbrk(app(TokenGenerator::class)->password()->length(12)->lowercase()->value(), implode('', range('A', 'Z'))))->toBeFalse();
        expect(strpbrk(app(TokenGenerator::class)->password()->length(12)->uppercase()->value(), implode('', range('a', 'z'))))->toBeFalse();

        expect(strpbrk(app(TokenGenerator::class)->password()->length(12)->value(), implode('', $symbols)))->toBeFalse();




        expect(strlen(app(TokenGenerator::class)->strongPassword()->length(12)->value()))->toBe(12);

        expect(strpbrk(app(TokenGenerator::class)->strongPassword()->length(12)->lowercase()->value(), implode('', range('A', 'Z'))))->toBeFalse();
        expect(strpbrk(app(TokenGenerator::class)->strongPassword()->length(12)->uppercase()->value(), implode('', range('a', 'z'))))->toBeFalse();

        expect(strpbrk(app(TokenGenerator::class)->strongPassword()->length(12)->value(), implode('', $symbols)))->not->toBeFalse();
        //
    });

    test('uuid4 format', function () {

        expect(strlen(app(TokenGenerator::class)->uuid4()->value()))->toBe(36);

        expect(preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', app(TokenGenerator::class)->uuid4()->value()))->toBe(1);

        expect(ctype_xdigit(str_replace('-', '', app(TokenGenerator::class)->uuid4()->value())))->toBeTrue();

        expect(strlen(app(TokenGenerator::class)->uuid4()->lowercase()->value()))->toBe(36);

        expect(strlen(app(TokenGenerator::class)->uuid4()->uppercase()->value()))->toBe(36);

        expect(strlen(app(TokenGenerator::class)->uuid4()->length(10)->value()))->toBe(36);
    });

    test('uuid7 format', function () {

        expect(strlen(app(TokenGenerator::class)->uuid7()->value()))->toBe(36);

        expect(preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', app(TokenGenerator::class)->uuid7()->value()))->toBe(1);

        expect(ctype_xdigit(str_replace('-', '', app(TokenGenerator::class)->uuid7()->value())))->toBeTrue();

        expect(strlen(app(TokenGenerator::class)->uuid7()->lowercase()->value()))->toBe(36);

        expect(strlen(app(TokenGenerator::class)->uuid7()->uppercase()->value()))->toBe(36);

        expect(strlen(app(TokenGenerator::class)->uuid7()->length(10)->value()))->toBe(36);
    });

    test('memorableOtp format', function () {

        expect(strlen(app(TokenGenerator::class)->memorableOtp()->length(6)->value()))->toBe(6);

        expect(ctype_digit(app(TokenGenerator::class)->memorableOtp()->length(10)->value()))->toBeTrue();

        expect(app(TokenGenerator::class)->memorableOtp()->length(10)->value()[0])->not->toBe('0');

        $token = app(TokenGenerator::class)->memorableOtp()->length(6)->value();

        $hasAdjacentPair = false;

        for ($j = 0; $j < strlen($token) - 1; $j++) {

            if ($token[$j] === $token[$j + 1]) {

                $hasAdjacentPair = true;

                break;
            }
        }

        expect($hasAdjacentPair)->toBeTrue();

        expect(ctype_digit(app(TokenGenerator::class)->memorableOtp()->length(10)->lowercase()->value()))->toBeTrue();
        expect(ctype_digit(app(TokenGenerator::class)->memorableOtp()->length(10)->uppercase()->value()))->toBeTrue();

        $token = app(TokenGenerator::class)->memorableOtp()->length(2)->value();

        expect(strlen($token))->toBe(2);
        expect($token[0])->not->toBe('0');
        expect($token[0] === $token[1])->toBeTrue();
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


        $token = app(TokenGenerator::class)->model($model)->field('code')->numeric()->length(1)->value();

        expect($token)->toBe('9'); // 0 - 8 is already inserted and the 9 is the last number with length 1
    });

    test('where conditions are applied in uniqueness check', function () {

        Schema::create('codes_table', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('status');
        });

        $model = new class extends Model
        {
            protected $table = 'codes_table';
        };

        $insert = [];
        // Codes 1-8 are taken with ACTIVE status
        foreach (range(1, 8) as $i) {
            $insert[] = ['code' => $i, 'status' => 'ACTIVE'];
        }
        // Code 9 exists but with INACTIVE status — should not be blocked when filtering by ACTIVE
        $insert[] = ['code' => 9, 'status' => 'INACTIVE'];

        $model->insert($insert);

        $token = app(TokenGenerator::class)
            ->model($model)
            ->field('code')
            ->where(['status', 'ACTIVE'])
            ->numeric()
            ->length(1)
            ->value();

        // With ACTIVE filter, only 1-8 are taken; 9 is INACTIVE so it is available
        expect($token)->toBe('9');
    });

    test('instance reuse resets attempt counter', function () {

        $gen = app(TokenGenerator::class)->numeric()->length(10);

        $gen->value();

        $property = (new \ReflectionClass($gen))->getProperty('attempted');

        expect($property->getValue($gen))->toBe(0);
    });

    test('weakPassword never starts with zero', function () {

        $verified = false;

        for ($i = 0; $i < 200; $i++) {

            $token = app(TokenGenerator::class)->weakPassword()->length(3)->value();

            expect(strlen($token))->toBe(3);
            expect(ctype_digit($token))->toBeTrue();
            expect($token[0])->not->toBe('0');

            $verified = true;
        }

        expect($verified)->toBeTrue();
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


            expect(fn() => app(TokenGenerator::class)->model($model)->field('code')->numeric()->length(1)->value())->toThrow(InfiniteLoopException::class, 'msg.infiniteLoopExceptionInTokenGenerator');

            try {

                app(TokenGenerator::class)->model($model)->field('code')->numeric()->length(1)->value();

                //
            } catch (InfiniteLoopException $e) {


                expect($e->getMessage())->toBe('msg.infiniteLoopExceptionInTokenGenerator');
                expect($e->getCode())->toBe(253);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 101,
                    'length'    => 1,
                    'format'    => 'numeric'
                ]);
            }
        });

        test('length must be positive', function () {

            expect(fn() => app(TokenGenerator::class)->length(0)->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionLengthMustBePositive');
            expect(fn() => app(TokenGenerator::class)->length(-1)->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionLengthMustBePositive');

            try {

                app(TokenGenerator::class)->length(0)->value();

                //
            } catch (TokenGeneratorException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionLengthMustBePositive');
                expect($e->getCode())->toBe(1201);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 0,
                    'format'    => 'numeric'
                ]);
            }

            try {

                app(TokenGenerator::class)->length(-1)->value();

                //
            } catch (TokenGeneratorException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionLengthMustBePositive');
                expect($e->getCode())->toBe(1201);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => -1,
                    'format'    => 'numeric'
                ]);
            }
        });

        test('big length number', function () {

            expect(fn() => app(TokenGenerator::class)->length(256)->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionBigLengthNumber');
            expect(fn() => app(TokenGenerator::class)->length(500)->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionBigLengthNumber');

            try {

                app(TokenGenerator::class)->length(256)->value();

                //
            } catch (TokenGeneratorException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionBigLengthNumber');
                expect($e->getCode())->toBe(1202);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 256,
                    'format'    => 'numeric'
                ]);
            }
        });

        test('password length number', function () {

            expect(fn() => app(TokenGenerator::class)->strongPassword()->length(5)->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionSmallLengthNumberForStrongPassword');
            expect(fn() => app(TokenGenerator::class)->password()->length(5)->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionSmallLengthNumberForPassword');

            try {

                app(TokenGenerator::class)->strongPassword()->length(5)->value();

                //
            } catch (TokenGeneratorException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionSmallLengthNumberForStrongPassword');
                expect($e->getCode())->toBe(1203);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 5,
                    'format'    => 'strongPassword'
                ]);
            }

            try {

                app(TokenGenerator::class)->password()->length(5)->value();

                //
            } catch (TokenGeneratorException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionSmallLengthNumberForPassword');
                expect($e->getCode())->toBe(1204);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 5,
                    'format'    => 'password'
                ]);
            }
        });

        test('field is required', function () {

            expect(fn() => app(TokenGenerator::class)->model('foobar')->field('')->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionFieldIsRequired');

            try {

                app(TokenGenerator::class)->model('foobar')->field('')->value();

                //
            } catch (TokenGeneratorException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionFieldIsRequired');
                expect($e->getCode())->toBe(1205);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 5,
                    'format'    => 'numeric',
                    'field'     => '',
                ]);
            }
        });

        test('memorableOtp length number', function () {

            expect(fn() => app(TokenGenerator::class)->memorableOtp()->length(1)->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionSmallLengthNumberForMemorable');
            expect(fn() => app(TokenGenerator::class)->memorableOtp()->length(0)->value())->toThrow(TokenGeneratorException::class, 'msg.tokenGeneratorExceptionLengthMustBePositive');

            try {

                app(TokenGenerator::class)->memorableOtp()->length(1)->value();

                //
            } catch (TokenGeneratorException $e) {

                expect($e->getMessage())->toBe('msg.tokenGeneratorExceptionSmallLengthNumberForMemorable');
                expect($e->getCode())->toBe(1206);
                expect($e->getLevel())->toBe('error');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'attempted' => 0,
                    'length'    => 1,
                    'format'    => 'memorableOtp'
                ]);
            }
        });
    });
});
