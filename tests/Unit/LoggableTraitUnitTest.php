<?php


namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Traits\Trashable;
use Fooino\Core\Tests\TestCase;
use Fooino\Core\Traits\Infoable;
use Fooino\Core\Traits\Loggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class LoggableTraitUnitTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();


        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });

        Schema::create('users_table', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('nickname')->nullable();
            $table->string('email_verified_at')->nullable();
            $table->string('password')->nullable();

            $table->json('info')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        activity()->enableLogging();
    }



    public function test_loggable_can_log_data_at_create_update_delete_restore()
    {
        $user = new class extends User
        {
            use
                Infoable,
                SoftDeletes,
                Trashable,
                Loggable;


            protected $guarded = ['id'];

            protected $table = 'users_table';

            protected $hidden = [
                'password'
            ];


            public function info(): Attribute
            {
                return jsonAttribute();
            }

            public function getLogExceptions(): array
            {
                return [
                    'email_verified_at'
                ];
            }


            public function getJsonLogExceptions(): array
            {
                return [
                    'info->url->0',
                    'info->url->1->foo',
                    'info->url->style',
                    'info->url->position->top'
                ];
            }
        };

        $user->create([
            'name'  => 'foobar',
            'email' => 'foobar@gmail.com'
        ]);

        $log = Activity::latest('id')->first();

        $this->assertEquals($log->log_name, $user->getLogName());

        $this->assertEquals($log->event, 'created');

        $this->assertEquals($log->subject_type, get_class($user));
        $this->assertEquals($log->subject_id, 1);

        $this->assertEquals($log->causer_type, null);
        $this->assertEquals($log->causer_id, null);

        // it removes info since it is empty
        $this->assertEquals(
            $log->properties->toArray(),
            [
                'attributes'    => [
                    'id'    => 1,
                    'name'  => 'foobar',
                    'email' => 'foobar@gmail.com',
                ]
            ]
        );


        $user->find(1)->update([
            'info'  => ['foobar']
        ]);

        $log = Activity::latest('id')->first();

        $this->assertEquals(
            $log->properties->toArray(),
            [
                'attributes'    => [
                    'info'      => ['foobar']
                ],
                'old'           => [
                    'info'      => []
                ]
            ]
        );


        $user->find(1)->update([
            'name'  => 'foobar2',
            'info'  => ['foobar']
        ]);

        $log = Activity::latest('id')->first();

        // we used logOnDirty so the info not saved
        $this->assertEquals(
            $log->properties->toArray(),
            [
                'attributes'    => [
                    'name'      => 'foobar2'
                ],
                'old'           => [
                    'name'      => 'foobar'
                ]
            ]
        );

        $user->find(1)->update([
            'info'  => null
        ]);

        $log = Activity::latest('id')->first();

        $this->assertEquals(
            $log->properties->toArray(),
            [
                'attributes'    => [
                    'info'      => []
                ],
                'old'           => [
                    'info'      => ['foobar']
                ]
            ]
        );



        Auth::login($user->first());

        $user->create([
            'name'              => 'foobar2',
            'email'             => 'foobar2@gmail.com',
            'email_verified_at' => '2025-01-01 00:00:00',
            'password'          => '123456',
            'info'              => [
                'url'   => [
                    'href'      => 'https://google.com',
                    'style'     => 'color: red',
                    'position'  => [
                        'top'   => 100,
                        'left'  => 200
                    ],
                    [
                        'additional' => 'foobar'
                    ],
                    [
                        'foo'       => 'bar',
                        'john'      => 'wick'
                    ]
                ],
                'status' => 'active'
            ]
        ]);

        $log = Activity::latest('id')->first();

        $this->assertEquals($log->causer_type, get_class($user));
        $this->assertEquals($log->causer_id, 1);

        // it removes email_verified_at since added to getLogExceptions()
        // it removes password since it is hidden
        // it removes the json data that is added to getJsonLogExceptions()
        $this->assertTrue(
            $log->properties->toArray() ==
                [
                    'attributes'    => [
                        'id'                => 2,
                        'name'              => 'foobar2',
                        'email'             => 'foobar2@gmail.com',
                        // 'email_verified_at' => now(),
                        // 'password'          => '123456',
                        'info'              => [
                            'url' => [
                                'href' => 'https://google.com',
                                // 'style' => 'color: red',
                                'position' => [
                                    // 'top' => 100,
                                    'left' => 200
                                ],
                                // [
                                //     'additional' => 'foobar'
                                // ],
                                1 => [
                                    // 'foo'   => 'bar',
                                    'john'  => 'wick'
                                ]

                            ],
                            'status' => 'active'
                        ]
                    ]
                ]
        );


        $user->find(2)->update([
            'name'              => 'foobar3',
            'email'             => 'foobar2@gmail.com',
            'email_verified_at' => '1999-01-01 00:00:00',
            'password'          => '123456',
            'info'              => [
                'url'   => [
                    'href'      => 'https://youtube.com',
                    'style'     => 'color: red',
                    'position'  => [
                        'top'   => 100,
                        'left'  => 150
                    ],
                    [
                        'additional' => 'foobar'
                    ],
                    [
                        'foo'       => 'bar',
                        'john'      => 'cena'
                    ]
                ],
                'status' => 'active'
            ]
        ]);


        $log = Activity::latest('id')->first();

        $lastLogId = $log->id;

        $this->assertEquals($log->event, 'updated');

        // it removes email_verified_at since added to getLogExceptions()
        // it removes password since it is hidden
        // it removes the json data that is added to getJsonLogExceptions()
        // does not log email since it is not changed logOnlyDirty
        $this->assertTrue(
            $log->properties->toArray() ==
                [
                    'old'    => [
                        'name'              => 'foobar2',
                        // 'email'             => 'foobar2@gmail.com',
                        // 'email_verified_at' => '1999-01-01 00:00:00',
                        // 'password'          => '123456',
                        'info'              => [
                            'url' => [
                                'href' => 'https://google.com',
                                // 'style' => 'color: red',
                                'position' => [
                                    // 'top' => 100,
                                    'left' => 200
                                ],
                                // [
                                //     'additional' => 'foobar'
                                // ],
                                1 => [
                                    'john'      => 'wick'
                                ]

                            ],
                            'status' => 'active'
                        ]
                    ],
                    'attributes'    => [
                        'name'              => 'foobar3',
                        // 'email'             => 'foobar2@gmail.com',
                        // 'email_verified_at' => '1999-01-01 00:00:00',
                        // 'password'          => '123456',
                        'info'              => [
                            'url' => [
                                'href' => 'https://youtube.com',
                                // 'style' => 'color: red',
                                'position' => [
                                    // 'top' => 100,
                                    'left' => 150
                                ],
                                // [
                                //     'additional' => 'foobar'
                                // ],
                                1 => [
                                    'john'      => 'cena'
                                ]

                            ],
                            'status' => 'active'
                        ]
                    ],
                ]
        );



        // it does log since we used dontSubmitEmptyLogs
        $user->find(2)->update([
            'email_verified_at' => '2000-01-01 00:00:00',
        ]);

        $log = Activity::latest('id')->first();

        $this->assertEquals($log->id, $lastLogId);



        $user->find(2)->delete();

        $log = Activity::latest('id')->first();

        $this->assertEquals($log->event, 'deleted');

        $this->assertTrue(
            $log->properties->toArray() == [
                'old'   => [
                    'id'    => 2,
                    'name'  => 'foobar3',
                    'email' => 'foobar2@gmail.com',
                    'info'  => [
                        'url'  => [
                            'href'  => 'https://youtube.com',
                            'position'  => [
                                'left'  => 150
                            ],
                            1           => [
                                'john'  => 'cena'
                            ],
                        ],
                        'status'    => 'active'
                    ],
                ]
            ]
        );


        $user->withTrashed()->find(2)->restore();

        $log = Activity::latest('id')->first();

        $this->assertEquals($log->event, 'restored');


        $this->assertTrue(
            $log->properties->toArray() == [
                'attributes'   => [
                    'id'    => 2,
                    'name'  => 'foobar3',
                    'email' => 'foobar2@gmail.com',
                    'info'  => [
                        'url'  => [
                            'href'  => 'https://youtube.com',
                            'position'  => [
                                'left'  => 150
                            ],
                            1           => [
                                'john'  => 'cena'
                            ],
                        ],
                        'status'    => 'active'
                    ],
                ]
            ]
        );
    }
}
