<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Concerns\NormalizesInputs;
use Illuminate\Foundation\Http\FormRequest;

class NormalizesInputsTestFormRequest extends FormRequest
{
    use NormalizesInputs;

    public static array $testRules = [];
    public static array $testConfigs = [];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return static::$testRules;
    }

    protected function inputConfigs(): array
    {
        return static::$testConfigs;
    }
}

describe('NormalizesInputs trait', function () {

    beforeEach(function () {

        NormalizesInputsTestFormRequest::$testRules = [];

        NormalizesInputsTestFormRequest::$testConfigs = [];
    });

    it('normalizes and converts blank to null by default', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'title'                 => 'required',
            'bio'                   => 'nullable',
            'slogan'                => 'nullable',
            'content'               => 'required',
            'age'                   => 'required|numeric',
            'amount'                => 'required|numeric',
            'phone_number'          => 'required',
            'remember_me'           => 'required|boolean:strict',
            'user'                  => 'nullable',
            'attributes'            => 'required|array'
        ];

        $collection = collect(['id' => 123, 'name' => 'عليك سلام']);

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'title'         => 'عليك سلام',
                'bio'           => 'null ',
                // I did not pass the slogan
                'content'       => '<script>alert(1)</script>',
                'age'           => 29,
                'amount'        => 123.0102,
                'phone_number'  => '۰۱۲۳٤٥٦۷۸۹',
                'remember_me'   => true,
                'user'          => $collection,
                'attributes'    => [
                    'id'        => 1,
                    'name'      => 'عليك سلام',
                    'priority'  => '۷۸۹'
                ]
            ],
        );

        expect($request->validated())->toBe([
            'title'         => 'علیک سلام',
            'bio'           => null,
            'slogan'        => null, // the null value is merged into the request
            'content'       => 'alert(1)',
            'age'           => 29,
            'amount'        => 123.0102,
            'phone_number'  => '0123456789',
            'remember_me'   => true,
            'user'          => $collection,
            'attributes'    => [
                'id'        => 1,
                'name'      => 'علیک سلام',
                'priority'  => '789'
            ]
        ]);


        expect($request->validated()['user']->toArray()['name'])->toBe('عليك سلام'); // it does not affect the object
    });

    it('does not merge when there are no rules', function () {

        NormalizesInputsTestFormRequest::$testRules = [];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'unused' => 'value'
            ],
        );

        expect($request->validated())->toBe([]);
    });

    it('does not modify inputs that are not in rules', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'title' => 'required'
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'title' => 'Hello',
                'extra' => ' untouched۱۲۳ '
            ],
        );

        expect($request->validated())->toHaveKey('title');

        expect($request->validated())->not->toHaveKey('extra');
        expect($request->all()['extra'])->toBe(' untouched۱۲۳ ');
    });

    describe('config options', function () {

        it('skipNormalize', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'raw' => 'required',
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'raw' => ['skipNormalize' => true]
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'raw' => '<script>alert(1)</script>'
                ],
            );

            expect($request->validated()['raw'])->toBe('<script>alert(1)</script>');


            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'raw' => '۰۱۲۳٤٥٦۷۸۹'
                ],
            );

            expect($request->validated()['raw'])->toBe('۰۱۲۳٤٥٦۷۸۹');
        });

        it('skipNormalize disabled re-enables normalize', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'raw' => 'required'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'raw' => ['skipNormalize' => false]
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'raw' => '۰۱۲۳٤٥٦۷۸۹'
                ],
            );

            expect($request->validated()['raw'])->toBe('0123456789');
        });

        it('keepBlank', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'bio' => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'bio' => ['keepBlank' => true]
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'bio' => ''
                ],
            );

            expect($request->validated()['bio'])->toBe('');


            NormalizesInputsTestFormRequest::$testConfigs = [
                'bio' => ['keepBlank' => false]
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'bio' => 'undefined'
                ],
            );

            expect($request->validated()['bio'])->toBe(null);
        });

        it('nullOnZero', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'count' => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'count' => ['nullOnZero' => true]
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'count' => '0'
                ],
            );

            expect($request->validated()['count'])->toBe(null);


            NormalizesInputsTestFormRequest::$testConfigs = [
                'count' => ['nullOnZero' => false]
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'count' => 0
                ],
            );

            expect($request->validated()['count'])->toBe(0);
        });

        it('default fallback when input is blank', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'title'     => 'nullable',
                'slogan'    => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'title'     => ['default' => 'Untitled'],
                'slogan'    => ['default' => 'fooino will rock you']
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'title' => "NULL\n",
                    // I did not pass the slogan but it gets the default value
                ],
            );

            expect($request->validated()['title'])->toBe('Untitled');
            expect($request->validated()['slogan'])->toBe('fooino will rock you');
        });

        it('does not override non-blank value when default is set', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'title' => 'required'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = ['title' => ['default' => 'Untitled']];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'title' => 'My Title'
                ],
            );

            expect($request->validated()['title'])->toBe('My Title');
        });

        it('nullOnZero with default fallback', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'count' => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'count' => ['nullOnZero' => true, 'default' => 0],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'count' => '0'
                ],
            );

            expect($request->validated()['count'])->toBe(0);
        });

        it('single pipe', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'slug' => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'slug' => ['pipe' => fn($value, $request) => sanitizeSlug($value)],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'slug' => ' Hello__World'
                ],
            );

            expect($request->validated()['slug'])->toBe('hello-world');
        });

        it('multiple pipes in sequence', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'phone' => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'phone' => ['pipe' => [
                    fn($v) => str_replace(',', '', $v),
                    fn($v) => preg_replace('/\s+/', '', $v),
                ]],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'phone' => ' 0912 , 123 , 4567 '
                ],
            );

            expect($request->validated()['phone'])->toBe('09121234567');
        });

        it('pipe receives null after nullIfBlank', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'slug' => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'slug' => ['pipe' => [fn($v) => $v === null ? null : strtolower($v)]],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'slug' => ''
                ],
            );

            expect($request->validated()['slug'])->toBe(null);


            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'slug' => 'FOOBAR'
                ],
            );

            expect($request->validated()['slug'])->toBe('foobar');
        });

        it('pipe receives the request instance', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'slug'  => 'nullable',
                'title' => 'required',
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'slug' => ['pipe' => fn($v, $req) => str_replace(' ', '-', strtolower($req->input('title')))],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'slug' => '',
                    'title' => 'My Article'
                ],
            );

            expect($request->validated()['slug'])->toBe('my-article');
        });

        it('mixed configs across multiple inputs', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'title' => 'required',
                'slug'  => 'nullable',
                'count' => 'nullable',
                'email' => 'required',
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'slug'  => ['pipe' => fn($v) => $v === null ? null : str_replace(' ', '-', strtolower($v))],
                'count' => ['nullOnZero' => true, 'default' => 'siuuuu'],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'title' => '  Hello World  ',
                    'slug'  => '',
                    'count' => '0',
                    'email' => ' USER@EXAMPLE.COM ',
                ],
            );

            expect($request->validated()['title'])->toBe('Hello World');
            expect($request->validated()['slug'])->toBeNull();
            expect($request->validated()['count'])->toBe('siuuuu');
            expect($request->validated()['email'])->toBe('USER@EXAMPLE.COM');
        });
    });

    describe('dot notation', function () {

        it('normalizes dot-notation inputs', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'user.name'                  => 'required',
                'user.bio'                   => 'nullable',
                'user.content'               => 'required',
                'user.age'                   => 'required|numeric',
                'user.phone_number'          => 'required',
                'user.remember_me'           => 'required|boolean:strict',
                'user.attributes.id'         => 'nullable',
                'user.attributes.email'      => 'required|email'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'user.attributes.id'        => ['nullOnZero' => true],
                'user.attributes.email'     => ['default'    => 'FOO@INO.COM', 'pipe' => fn($value, $request) => strtolower($value)]
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'user' => [
                        'name'          => 'عليك سلام',
                        'bio'           => 'null ',
                        'content'       => '<script>alert(1)</script>',
                        'age'           => 29,
                        'phone_number'  => '۰۱۲۳٤٥٦۷۸۹',
                        'remember_me'   => true,
                        'attibutes'     => [
                            'id'        => 0,
                        ]
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'user' => [
                    'name'          => 'علیک سلام',
                    'bio'           => null,
                    'content'       => 'alert(1)',
                    'age'           => 29,
                    'phone_number'  => '0123456789',
                    'remember_me'   => true,
                    'attributes'    => [
                        'id'    => null,
                        'email' => 'foo@ino.com'
                    ]
                ],
            ]);
        });

        it('applies config to dot-notation inputs', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'user.name' => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'user.name' => ['default' => 'Guest']
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'user' => [
                        'name' => ''
                    ]
                ],
            );

            expect($request->validated())->toBe([
                'user' => [
                    'name' => 'Guest'
                ],
            ]);
        });

        it('normalizes indexed dot-notation', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'user.0.name' => 'required',
                'user.1.name' => 'required',
                'user.2.name' => 'nullable',
                'user.3.name' => 'required',
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'user.3.name'   => ['default' => 'FIFA world cup 2026 - Germany vs Paraguay']
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'user' => [
                        ['name' => 'عليك سلام'],
                        ['name' => '۰۱۲۳'],
                        // I did not pass the third and fourth name
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'user' => [
                    ['name' => 'علیک سلام'],
                    ['name' => '0123'],
                    ['name' => null],
                    ['name' => 'FIFA world cup 2026 - Germany vs Paraguay']
                ],
            ]);
        });

        it('applies config to indexed dot-notation', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'user.0.name' => 'nullable',
                'user.1.name' => 'nullable',
            ];

            NormalizesInputsTestFormRequest::$testConfigs = ['user.0.name' => ['default' => 'First']];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'user' => [
                        ['name' => ''],
                        ['name' => ''],
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'user' => [
                    ['name' => 'First'],
                    ['name' => null],
                ],
            ]);
        });
    });

    describe('wildcards', function () {

        it('normalizes wildcard inputs', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.*.name' => 'required'
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => [
                        [
                            'name'  => 'عليك سلام',
                            'email' => '',
                        ],
                        [
                            'name'  => '۰۱۲۳',
                            'email' => '۰۱۲۳'
                        ],
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'users' => [
                    [
                        'name'  => 'علیک سلام'
                    ],
                    [
                        'name'  => '0123'
                    ],
                ],
            ]);

            expect($request->all())->toBe([
                'users' => [
                    [
                        'name'  => 'علیک سلام',
                        'email' => ''
                    ],
                    [
                        'name'  => '0123',
                        'email' => '۰۱۲۳',
                    ],
                ],
            ]);
        });

        it('applies config to wildcard inputs', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.1.name'  => 'required',
                'users.*.name'  => 'nullable',
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'users.*.name'  => ['default'   => 'Guest'],
                'users.1.name'  => ['default'   => 'fooino magic', 'pipe' => fn($value) => strtoupper($value)]
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => [
                        ['name' => 'عليك '],
                        ['name' => null],
                        ['name' => null]
                    ],
                ],
            );

            expect($request->all())->toBe([
                'users' => [
                    ['name' => 'علیک'],
                    ['name' => 'FOOINO MAGIC'],
                    ['name' => 'Guest'],
                ],
            ]);
        });

        it('applies pipe to wildcard inputs', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.*.name' => 'nullable'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'users.*.name' => ['pipe' => fn($v) => $v === null ? null : strtoupper($v)],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => [
                        ['name' => 'Ali'],
                        ['name' => ''],
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'users' => [
                    ['name' => 'ALI'],
                    ['name' => null],
                ],
            ]);
        });

        it('skips when parent is not an array', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.*.name' => 'nullable'
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => 'not-an-array'
                ],
            );

            expect($request->validated())->toBe([]);
        });

        it('skips when wildcard has no remaining segments', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.*' => 'required'
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => [
                        ['name' => 'Ali'],
                        ['name' => '']
                    ]
                ],
            );

            expect($request->validated())->toBe(['users' => [['name' => 'Ali'], ['name' => '']]]);
        });

        it('deeply nested wildcards: 3 levels', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.*.attributes.*.name' => 'required'
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'users.*.attributes.*.name' => [
                    'default'   => 'n/a',
                    'pipe'      => fn($v) => $v === null ? null : strtoupper($v)
                ],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => [
                        [
                            'attributes' => [
                                ['name' => 'عليك', 'type' => 'a'],
                                ['name' => '۰۱۲۳', 'type' => 'b'],
                            ],
                        ],
                        [
                            'attributes' => [
                                ['name' => 'مصطفي', 'type' => 'c'],
                                ['name' => 'undefined'],
                                ['name' => 'fooino']
                            ],
                        ],
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'users' => [
                    [
                        'attributes' => [
                            ['name' => 'علیک'],
                            ['name' => '0123']
                        ]
                    ],
                    [
                        'attributes' => [
                            ['name' => 'مصطفی'],
                            ['name' => 'N/A'],
                            ['name' => 'FOOINO']
                        ]
                    ],
                ],
            ]);
        });

        it('applies config to deeply nested wildcards', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.*.attributes.*.name' => 'nullable',
            ];

            NormalizesInputsTestFormRequest::$testConfigs = [
                'users.*.attributes.*.name' => ['default' => 'Unnamed'],
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => [
                        ['attributes' => [
                            ['name' => 'عليك'],
                            ['name' => '']
                        ]],
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'users' => [
                    ['attributes' => [
                        ['name' => 'علیک'],
                        ['name' => 'Unnamed']
                    ]],
                ],
            ]);
        });

        it('4 levels with Persian digits and nullish at leaf', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'orgs.*.users.*.metadata.*.value' => 'nullable',
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'orgs' => [
                        [
                            'users' => [
                                [
                                    'metadata' => [
                                        ['value' => '۰۱۲۳'],
                                        ['value' => ''],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'orgs' => [
                    [
                        'users' => [
                            [
                                'metadata' => [
                                    ['value' => '0123'],
                                    ['value' => null],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });

        it('wildcard with non-wildcard intermediate key', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.*.metadata.name' => 'required'
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => [
                        ['metadata' => ['name' => 'عليك سلام']],
                        ['metadata' => ['name' => '۰۱۲۳']],
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'users' => [
                    ['metadata' => ['name' => 'علیک سلام']],
                    ['metadata' => ['name' => '0123']],
                ],
            ]);
        });

        it('wildcard with specific numeric index', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'users.*.attributes.1.name' => 'required'
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'users' => [
                        [
                            'attributes' => [
                                ['name' => 'عليك'],
                                ['name' => 'عليك'],
                            ],
                        ],
                        [
                            'attributes' => [
                                ['name' => '۰۱۲۳'],
                                ['name' => '۰۱۲۳'],
                            ],
                        ],
                    ],
                ],
            );

            expect($request->validated())->toBe([
                'users' => [
                    ['attributes' => [1 => ['name' => 'علیک']]],
                    ['attributes' => [1 => ['name' => '0123']]],
                ],
            ]);

            expect($request->all())->toBe([
                'users' => [
                    ['attributes' => [['name' => 'عليك'], ['name' => 'علیک']]],
                    ['attributes' => [['name' => '۰۱۲۳'], ['name' => '0123']]],
                ],
            ]);
        });

        it('normalizeInput does not corrupt array or JSON structure', function () {

            NormalizesInputsTestFormRequest::$testRules = [
                'metadata.json'                 => 'required',
                'metadata.json_persian'         => 'required',
                'metadata.nested_json'          => 'required',
                'metadata.nested'               => 'required',
            ];

            $request = resolveRequest(
                request: NormalizesInputsTestFormRequest::class,
                data: [
                    'metadata' => [
                        'json'          => '{"status":"ok"}',
                        'json_persian'  => '{"title":"۰۱۲۳"}',
                        'nested_json'   => '{"deep":{"deeper":"\u0639\u0644\u064a\u0643 \u0633\u0644\u0627\u0645","0":{"deepest":{"number":"\u06f0\u06f1\u06f2\u06f3","name":"undefined"}}}}',
                        'nested'        => ['deep' => ['deeper' => 'عليك سلام']],
                    ],
                ],
            );

            $validated = $request->validated();

            expect($validated['metadata']['json'])->toBe('{"status":"ok"}');

            expect($validated['metadata']['json_persian'])->toBe('{"title":"0123"}');

            expect(jsonDecodeToArray($validated['metadata']['nested_json']))->toBe(['deep' => ['deeper' => 'علیک سلام', ['deepest' => ['number' => '0123', 'name' => 'undefined']]]]);

            expect($validated['metadata']['nested'])->toBe(['deep' => ['deeper' => 'علیک سلام']]);
        });
    });
});
