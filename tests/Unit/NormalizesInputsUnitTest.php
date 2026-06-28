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
            'title'         => 'required',
            'bio'           => 'nullable'
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'title'     => 'عليك سلام',
                'bio'       => ''
            ],
        );

        expect($request->validated())->toBe([
            'title'         => 'علیک سلام',
            'bio'           => null,
        ]);

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'title'     => '۰۱۲۳٤٥٦۷۸۹',
                'bio'       => 'null '
            ],
        );

        expect($request->validated())->toBe([
            'title'         => '0123456789',
            'bio'           => null,
        ]);
    });

    it('skips normalizeInput when skipNormalize is true', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'raw' => 'required'
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

    it('keeps blank values when keepBlank is true', function () {

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

    it('converts zero to null when nullOnZero is true', function () {

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

    it('uses default fallback when input is blank', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'title' => 'nullable'
        ];

        NormalizesInputsTestFormRequest::$testConfigs = [
            'title' => ['default' => 'Untitled']
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'title' => ''
            ],
        );

        expect($request->validated()['title'])->toBe('Untitled');
    });

    it('does not override non-blank value when default is set', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'title' => 'required'
        ];

        NormalizesInputsTestFormRequest::$testConfigs = [
            'title' => ['default' => 'Untitled']
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'title' => 'My Title'
            ],
        );

        expect($request->validated()['title'])->toBe('My Title');
    });

    it('applies a single pipe after nullIfBlank', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'slug' => 'nullable'
        ];
        NormalizesInputsTestFormRequest::$testConfigs = [
            'slug' => ['pipe' => fn($value) => sanitizeSlug($value)]
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'slug' => ' Hello__World'
            ],
        );

        expect($request->validated()['slug'])->toBe('hello-world');
    });

    it('chains multiple pipes in sequence', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'phone' => 'nullable'
        ];

        NormalizesInputsTestFormRequest::$testConfigs = [
            'phone' => ['pipe' => [
                fn($v) => str_replace(',', '', $v),
                fn($v) => preg_replace('/\s+/', '', $v),
            ]]
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'phone' => ' 0912 , 123 , 4567 '
            ],
        );

        expect($request->validated()['phone'])->toBe('09121234567');
    });

    it('runs pipes after nullIfBlank so pipes can opt to keep null', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'slug' => 'nullable'
        ];

        NormalizesInputsTestFormRequest::$testConfigs = [
            'slug' => [
                'pipe' => [
                    fn($v) => $v === null ? null : strtolower($v),
                ]
            ]
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'slug' => ''
            ],
        );

        expect($request->validated()['slug'])->toBeNull();
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

    it('uses nullOnZero with default fallback', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'count' => 'nullable'
        ];

        NormalizesInputsTestFormRequest::$testConfigs = [
            'count' => [
                'nullOnZero' => true,
                'default' => 0
            ]
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'count' => '0'
            ],
        );

        expect($request->validated()['count'])->toBe(0);
    });

    it('handles multiple inputs with mixed configs', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'title' => 'required',
            'slug'  => 'nullable',
            'count' => 'nullable',
            'email' => 'required',
        ];

        NormalizesInputsTestFormRequest::$testConfigs = [
            'slug'  => [
                'pipe' => fn($v) => $v === null ? null : str_replace(' ', '-', strtolower($v))
            ],
            'count' => [
                'nullOnZero' => true
            ],
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
        expect($request->validated()['count'])->toBeNull();
        expect($request->validated()['email'])->toBe('USER@EXAMPLE.COM');
    });

    it('normalizes Arabic and Persian digits', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'phone' => 'required'
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'phone' => '۰۱۲۳۴۵۶۷۸۹'
            ],
        );

        expect($request->validated()['phone'])->toBe('0123456789');
    });

    it('replaces Arabic letters ي and ك with Persian ی and ک', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'name' => 'required'
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'name' => 'عليك'
            ],
        );

        expect($request->validated()['name'])->toBe('علیک');
    });

    it('strips non-allowed HTML tags', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'body' => 'required'
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'body' => '<b>safe</b><script>alert(1)</script>'
            ],
        );

        expect($request->validated()['body'])->toBe('<b>safe</b>alert(1)');
    });

    it('passes the request instance to pipes', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'slug' => 'nullable',
            'title' => 'required'
        ];
        NormalizesInputsTestFormRequest::$testConfigs = [
            'slug' => [
                'pipe' => fn($v, $req) => str_replace(' ', '-', strtolower($req->input('title'))),
            ]
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

    it('preserves pipe results when value is not blank', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'phone' => 'nullable'
        ];

        NormalizesInputsTestFormRequest::$testConfigs = [
            'phone' => [
                'pipe' => fn($v) => str_replace('-', '', $v)
            ]
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'phone' => '0912-123-4567'
            ],
        );

        expect($request->validated()['phone'])->toBe('09121234567');
    });

    it('does not modify inputs that are not in rules', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'title' => 'required'
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'title' => 'Hello',
                'extra' => ' untouched '
            ],
        );

        expect($request->validated())->toHaveKey('title');

        expect($request->validated())->not->toHaveKey('extra');
        expect($request->all()['extra'])->toBe(' untouched ');
    });

    it('trims whitespace by default', function () {

        NormalizesInputsTestFormRequest::$testRules = [
            'name' => 'required'
        ];

        $request = resolveRequest(
            request: NormalizesInputsTestFormRequest::class,
            data: [
                'name' => '  John  '
            ],
        );

        expect($request->validated()['name'])->toBe('John');
    });
});
