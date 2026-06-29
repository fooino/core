<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\InfiniteLoopException;
use Fooino\Core\Support\Sanitizer;
use Illuminate\Database\Eloquent\Model;
use stdClass;

describe('Sanitizer utilities', function () {

    test('normalizeInput method', function () {


        expect(sanitizer(0)->normalizeInput()->value())->toBe(0);
        expect(sanitizer(-0)->normalizeInput()->value())->toBe(-0);
        expect(sanitizer(+0)->normalizeInput()->value())->toBe(+0);

        expect(sanitizer(123)->normalizeInput()->value())->toBe(123);
        expect(sanitizer(-123)->normalizeInput()->value())->toBe(-123);
        expect(sanitizer(+123)->normalizeInput()->value())->toBe(+123);

        expect(sanitizer(0.0)->normalizeInput()->value())->toBe(0.0);
        expect(sanitizer(-0.0)->normalizeInput()->value())->toBe(-0.0);
        expect(sanitizer(+0.0)->normalizeInput()->value())->toBe(+0.0);

        expect(sanitizer(123.123)->normalizeInput()->value())->toBe(123.123);
        expect(sanitizer(-123.123)->normalizeInput()->value())->toBe(-123.123);
        expect(sanitizer(+123.123)->normalizeInput()->value())->toBe(+123.123);

        expect(sanitizer(1_123_111_234_333_111_222)->normalizeInput()->value())->toBe(1_123_111_234_333_111_222);
        expect(sanitizer(0.0001000301102001102)->normalizeInput()->value())->toBe(0.0001000301102001102);

        expect(sanitizer(1.1E+20)->normalizeInput()->value())->toBe(1.1E+20);
        expect(sanitizer(-1.1E+20)->normalizeInput()->value())->toBe(-1.1E+20);

        expect(sanitizer(1.1E-20)->normalizeInput()->value())->toBe(1.1E-20);
        expect(sanitizer(-1.1E-20)->normalizeInput()->value())->toBe(-1.1E-20);

        expect(sanitizer(INF)->normalizeInput()->value())->toBe(INF);
        expect(sanitizer(-INF)->normalizeInput()->value())->toBe(-INF);

        expect(gettype(sanitizer(123)->normalizeInput()->value()))->toBe('integer');
        expect(gettype(sanitizer(123.123)->normalizeInput()->value()))->toBe('double');




        expect(sanitizer(null)->normalizeInput()->value())->toBe(null);
        expect(gettype(sanitizer(null)->normalizeInput()->value()))->toBe('NULL');




        expect(sanitizer(true)->normalizeInput()->value())->toBe(true);
        expect(sanitizer(false)->normalizeInput()->value())->toBe(false);

        expect(gettype(sanitizer(true)->normalizeInput()->value()))->toBe('boolean');
        expect(gettype(sanitizer(false)->normalizeInput()->value()))->toBe('boolean');




        expect(sanitizer('')->normalizeInput()->value())->toBe('');
        expect(sanitizer("   \t\r\nfoo  bar \n")->normalizeInput()->value())->toBe("foo  bar");
        expect(sanitizer("   ")->normalizeInput()->value())->toBe("");

        expect(gettype(sanitizer('foobar')->normalizeInput()->value()))->toBe('string');
        expect(sanitizer('foobar')->normalizeInput()->value())->toBe('foobar');
        expect(sanitizer('foobar123')->normalizeInput()->value())->toBe('foobar123');
        expect(sanitizer('foobar ')->normalizeInput()->value())->toBe('foobar');
        expect(sanitizer(' foobar')->normalizeInput()->value())->toBe('foobar');
        expect(sanitizer(' foobar ')->normalizeInput()->value())->toBe('foobar');

        expect(sanitizer('علیك سلام')->normalizeInput()->value())->toBe('علیک سلام');
        expect(sanitizer('عليك سلام')->normalizeInput()->value())->toBe('علیک سلام');
        expect(sanitizer('باي بای علیك')->normalizeInput()->value())->toBe('بای بای علیک');
        expect(sanitizer('باي بای علي')->normalizeInput()->value())->toBe('بای بای علی');
        expect(sanitizer('مصطفي')->normalizeInput()->value())->toBe('مصطفی');
        expect(sanitizer('عيسي')->normalizeInput()->value())->toBe('عیسی');
        expect(sanitizer('زکريا')->normalizeInput()->value())->toBe('زکریا');

        expect(sanitizer('۰۱۲۳۴۵۶۷۸۹')->normalizeInput()->value())->toBe('0123456789');
        expect(sanitizer('۰۱۲۳٤٥٦۷۸۹')->normalizeInput()->value())->toBe('0123456789');
        expect(sanitizer('foobar۰۱۲۳٤٥٦۷۸۹')->normalizeInput()->value())->toBe('foobar0123456789');


        expect(sanitizer('Hello <input name="password" value="123">')->normalizeInput()->value())->toBe('Hello');
        expect(sanitizer('Hello <script>alert("XSS");</script> World')->normalizeInput()->value())->toBe('Hello alert("XSS"); World');
        expect(normalizeInput('<script>alert("hi");</script> <h1>hi</h1>'))->toBe('alert("hi"); <h1>hi</h1>');
        expect(sanitizer('<script>alert("XSS");</script>')->normalizeInput()->value())->toBe('alert("XSS");');
        expect(sanitizer('<a href="javascript:alert(1)">click</a>')->normalizeInput()->value())->toBe('<a href="javascript:alert(1)">click</a>');
        expect(sanitizer('<img src="x" onerror="alert(1)">')->normalizeInput()->value())->toBe('<img src="x" onerror="alert(1)">');
        expect(sanitizer('<div class="foo" onclick="evil()">content</div>')->normalizeInput()->value())->toBe('<div class="foo" onclick="evil()">content</div>');
        expect(sanitizer('<b>bold</b> <i>italic</i>')->normalizeInput()->value())->toBe('<b>bold</b> <i>italic</i>');
        expect(sanitizer('<script>alert(1)</script>')->normalizeInput()->value())->toBe('alert(1)');
        expect(sanitizer('<unknown>tag</unknown>')->normalizeInput()->value())->toBe('tag');
        expect(sanitizer('Hello <img src="x" onclick="evil()"> World')->normalizeInput()->value())->toBe('Hello <img src="x" onclick="evil()"> World');

        expect(sanitizer('Hello 👋🏼')->normalizeInput()->value())->toBe('Hello 👋🏼');
        expect(sanitizer('😊😎👍')->normalizeInput()->value())->toBe('😊😎👍');
        expect(sanitizer('😊 <script>alert("XSS");</script> 😎')->normalizeInput()->value())->toBe('😊 alert("XSS"); 😎');

        expect(sanitizer('foo' . json_decode('"\u200C"') . 'bar')->normalizeInput()->value())->toBe('foobar');
        expect(sanitizer('سلام' . json_decode('"\u200C"') . 'علیک')->normalizeInput()->value())->toBe('سلامعلیک');
        expect(sanitizer('  ' . json_decode('"\u200C"') . '  ')->normalizeInput()->value())->toBe("");
        expect(sanitizer('سلام' . json_decode('"\u200D"') . 'علیک')->normalizeInput()->value())->toBe('سلامعلیک');
        expect(sanitizer('سلام' . json_decode('"\uFEFF"') . 'علیک')->normalizeInput()->value())->toBe('سلامعلیک');




        expect(sanitizer('0')->normalizeInput()->value())->toBe('0');
        expect(sanitizer('-0')->normalizeInput()->value())->toBe('-0');
        expect(sanitizer('+0')->normalizeInput()->value())->toBe('+0');
        expect(sanitizer('0 ')->normalizeInput()->value())->toBe('0');
        expect(sanitizer(' -0')->normalizeInput()->value())->toBe('-0');
        expect(sanitizer(' +0 ')->normalizeInput()->value())->toBe('+0');

        expect(sanitizer('0.0')->normalizeInput()->value())->toBe('0.0');
        expect(sanitizer('-0.0')->normalizeInput()->value())->toBe('-0.0');
        expect(sanitizer('+0.0')->normalizeInput()->value())->toBe('+0.0');
        expect(sanitizer('0.0 ')->normalizeInput()->value())->toBe('0.0');
        expect(sanitizer(' -0.0')->normalizeInput()->value())->toBe('-0.0');
        expect(sanitizer(' +0.0 ')->normalizeInput()->value())->toBe('+0.0');

        expect(sanitizer('0.123')->normalizeInput()->value())->toBe('0.123');
        expect(sanitizer('123.123')->normalizeInput()->value())->toBe('123.123');

        expect(sanitizer('1.1e+20')->normalizeInput()->value())->toBe('1.1e+20');
        expect(sanitizer('-1.1e+20')->normalizeInput()->value())->toBe('-1.1e+20');

        expect(sanitizer('1.1e-20')->normalizeInput()->value())->toBe('1.1e-20');
        expect(sanitizer('-1.1e-20')->normalizeInput()->value())->toBe('-1.1e-20');

        expect(sanitizer('true')->normalizeInput()->value())->toBe('true');
        expect(sanitizer('false')->normalizeInput()->value())->toBe('false');

        expect(sanitizer('true ')->normalizeInput()->value())->toBe('true');
        expect(sanitizer(' false ')->normalizeInput()->value())->toBe('false');

        expect(sanitizer('null')->normalizeInput()->value())->toBe('null');
        expect(sanitizer(' null')->normalizeInput()->value())->toBe('null');

        expect(sanitizer(' 1,123.123')->normalizeInput()->value())->toBe('1,123.123');

        expect(sanitizer('{}')->normalizeInput()->value())->toBe('{}');
        expect(sanitizer('[]')->normalizeInput()->value())->toBe('[]');



        expect(sanitizer('{"foo":null,"bar":{"baz":"۰۱۲۳"}}')->normalizeInput()->value())->toBe('{"foo":null,"bar":{"baz":"0123"}}');

        expect(sanitizer(jsonEncode([null, true, false, 0]))->normalizeInput()->value())->toBe(jsonEncode([null, true, false, 0]));

        expect(sanitizer(jsonEncode([['deep' => 'foobar۰۱۲۳']]))->normalizeInput()->value())->toBe(jsonEncode([['deep' => 'foobar0123']]));

        expect(sanitizer('{"name":"foo <b>bar<\/b>","num":"۰۱۲۳"}')->normalizeInput()->value())->toBe('{"name":"foo <b>bar<\/b>","num":"0123"}');

        expect(sanitizer(jsonEncode("علیك ۰۱۲۳"))->normalizeInput()->value())->toBe(jsonEncode("علیک 0123"));

        expect(sanitizer('۰۱۲۳ ')->normalizeInput()->value())->toBe('0123');

        $object = new stdClass;
        $object->number = '۰۱۲۳';
        expect(sanitizer($object)->normalizeInput()->value())->toBe($object);

        expect(sanitizer(jsonEncode(new stdClass))->normalizeInput()->value())->toBe('{}');

        expect(sanitizer(jsonEncode(new class extends Model {}))->normalizeInput()->value())->toBe('[]');


        expect(sanitizer([])->normalizeInput()->value())->toBe([]);

        expect(sanitizer(['۰۱۲۳'])->normalizeInput()->value())->toBe(['0123']);
        expect(sanitizer(jsonEncode(['۰۱۲۳']))->normalizeInput()->value())->toBe(jsonEncode(['0123']));
        expect(sanitizer(jsonEncode(['۰۱۲۳', 'foobar4567۸']))->normalizeInput()->value())->toBe(jsonEncode(['0123', 'foobar45678']));

        expect(
            sanitizer([
                0       => 'bar۰۱۲۳',
                1       => '۱.۰',
                2       => true,
                3       => false,
                4       => null,
                5       => '۱٤۰۱/۱۰/۱٤',
                'foo'   => '۰۱۲۳',
                '2d'    => [
                    '123',
                    '۰۱۲۳',
                    'علیك سلام'
                ],
                'withKey'    => [
                    'foo' => '123',
                    'bar' => '۰۱۲۳',
                    'third' => [
                        'foo'   => '123',
                        'bar'   => '۰۱۲۳',
                        'john'  => null,
                        'doe'   => true
                    ]
                ]
            ])->normalizeInput()->value()
        )
            ->toBe(
                [
                    0       => 'bar0123',
                    1       => '1.0',
                    2       => true,
                    3       => false,
                    4       => null,
                    5       => '1401/10/14',
                    'foo'   => '0123',
                    '2d'    => [
                        '123',
                        '0123',
                        'علیک سلام'
                    ],
                    'withKey'    => [
                        'foo' => '123',
                        'bar' => '0123',
                        'third' => [
                            'foo'   => '123',
                            'bar'   => '0123',
                            'john'  => null,
                            'doe'   => true
                        ]
                    ]
                ]
            );



        $deep = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'level4' => [
                            'level5' => 'foo۰۱۲۳bar'
                        ]
                    ]
                ]
            ]
        ];
        expect(sanitizer($deep)->normalizeInput()->value())->toBe(['level1' => ['level2' => ['level3' => ['level4' => ['level5' => 'foo0123bar']]]]]);
    });

    test('replaceForbiddenCharacters method', function () {

        expect((new Sanitizer(value: 1))->replaceForbiddenCharacters()->value())->toBe(1);
        expect((new Sanitizer(value: 1.1))->replaceForbiddenCharacters()->value())->toBe(1.1);
        expect((new Sanitizer(value: null))->replaceForbiddenCharacters()->value())->toBe(null);
        expect((new Sanitizer(value: true))->replaceForbiddenCharacters()->value())->toBe(true);
        expect((new Sanitizer(value: false))->replaceForbiddenCharacters()->value())->toBe(false);
        expect((new Sanitizer(value: ''))->replaceForbiddenCharacters()->value())->toBe('');
        expect((new Sanitizer(value: ' '))->replaceForbiddenCharacters()->value())->toBe('');
        expect((new Sanitizer(value: []))->replaceForbiddenCharacters()->value())->toBe([]);

        expect((new Sanitizer(value: ' foobar'))->replaceForbiddenCharacters()->value())->toBe('foobar');
        expect((new Sanitizer(value: [123]))->replaceForbiddenCharacters()->value())->toBe([123]);

        $forbidden = [
            '-',
            '.',
            '!',
            '@',
            '#',
            '$',
            '%',
            '^',
            '&',
            '*',
            '(',
            ')',
            '=',
            '+',
            '{',
            '}',
            ':',
            ';',
            '"',
            "'",
            '?',
            '؟',
            '<',
            '>',
            ',',
            '|',
            '`',
            '/',
            '\\',
            ' ',
            '[',
            ']',
            '~',
            '°',
            '../',
            '_'
        ];

        expect((new Sanitizer(value: implode('', $forbidden)))->replaceForbiddenCharacters()->value())->toBe('');

        expect((new Sanitizer(value: implode('a', $forbidden)))->replaceForbiddenCharacters()->value())->toBe('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');

        expect((new Sanitizer(value: implode('a', $forbidden)))->replaceForbiddenCharacters(replaceWith: '_')->value())->toBe('_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_');

        expect((new Sanitizer(value: implode('a', $forbidden)))->replaceForbiddenCharacters(excludes: ['-', '!'], replaceWith: 'X')->value())->toBe('-aXa!aXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaX');

        expect((new Sanitizer(value: [1, 1.1, null, true, false, 'foobar', 'f!o o-b.a$r"', [1, 1.1, null, true, false, 'foobar', '2,200,000']]))->replaceForbiddenCharacters(excludes: ['-'], replaceWith: '-')->value())->toBe([1, 1.1, null, true, false, 'foobar', 'f-o-o-b-a-r-', [1, 1.1, null, true, false, 'foobar', '2-200-000']]);
    });

    test('lowercase method', function () {

        expect((new Sanitizer(value: 1))->lowercase()->value())->toBe(1);
        expect((new Sanitizer(value: 1.1))->lowercase()->value())->toBe(1.1);
        expect((new Sanitizer(value: null))->lowercase()->value())->toBe(null);
        expect((new Sanitizer(value: true))->lowercase()->value())->toBe(true);
        expect((new Sanitizer(value: false))->lowercase()->value())->toBe(false);
        expect((new Sanitizer(value: ''))->lowercase()->value())->toBe('');
        expect((new Sanitizer(value: ' '))->lowercase()->value())->toBe(' ');
        expect((new Sanitizer(value: []))->lowercase()->value())->toBe([]);
        expect((new Sanitizer(value: [123]))->lowercase()->value())->toBe([123]);

        expect((new Sanitizer(value: ' fooBar'))->lowercase()->value())->toBe(' foobar');
        expect((new Sanitizer(value: [1, 1.1, null, true, false, ' fooBar', [1, 1.1, null, true, false, 'FOO_BAR']]))->lowercase()->value())->toBe([1, 1.1, null, true, false, ' foobar', [1, 1.1, null, true, false, 'foo_bar']]);

        expect((new Sanitizer(value: 'ÜBER'))->lowercase()->value())->toBe('über');
        expect((new Sanitizer(value: 'ÄÖÜ'))->lowercase()->value())->toBe('äöü');
        expect((new Sanitizer(value: 'ТЕКСТ'))->lowercase()->value())->toBe('текст');
        expect((new Sanitizer(value: 'Σ'))->lowercase()->value())->toBe('σ');
        expect((new Sanitizer(value: 'عَلِي'))->lowercase()->value())->toBe('عَلِي');
        expect((new Sanitizer(value: 'über straße'))->lowercase()->value())->toBe('über straße');
        expect((new Sanitizer(value: 'Hello 世界'))->lowercase()->value())->toBe('hello 世界');
        expect((new Sanitizer(value: 'STRASSE'))->lowercase()->value())->toBe('strasse');

        expect((new Sanitizer(value: ['ÜBER', 'foo', ['ТЕКСТ']]))->lowercase()->value())->toBe(['über', 'foo', ['текст']]);
    });

    test('uppercase method', function () {

        expect((new Sanitizer(value: 1))->uppercase()->value())->toBe(1);
        expect((new Sanitizer(value: 1.1))->uppercase()->value())->toBe(1.1);
        expect((new Sanitizer(value: null))->uppercase()->value())->toBe(null);
        expect((new Sanitizer(value: true))->uppercase()->value())->toBe(true);
        expect((new Sanitizer(value: false))->uppercase()->value())->toBe(false);
        expect((new Sanitizer(value: ''))->uppercase()->value())->toBe('');
        expect((new Sanitizer(value: ' '))->uppercase()->value())->toBe(' ');
        expect((new Sanitizer(value: []))->uppercase()->value())->toBe([]);
        expect((new Sanitizer(value: [123]))->uppercase()->value())->toBe([123]);

        expect((new Sanitizer(value: ' fooBar'))->uppercase()->value())->toBe(' FOOBAR');
        expect((new Sanitizer(value: [1, 1.1, null, true, false, ' fooBar', [1, 1.1, null, true, false, 'foo_bar']]))->uppercase()->value())->toBe([1, 1.1, null, true, false, ' FOOBAR', [1, 1.1, null, true, false, 'FOO_BAR']]);

        expect((new Sanitizer(value: 'über'))->uppercase()->value())->toBe('ÜBER');
        expect((new Sanitizer(value: 'äöü'))->uppercase()->value())->toBe('ÄÖÜ');
        expect((new Sanitizer(value: 'текст'))->uppercase()->value())->toBe('ТЕКСТ');
        expect((new Sanitizer(value: 'straße'))->uppercase()->value())->toBe('STRASSE');
        expect((new Sanitizer(value: 'عَلِي'))->uppercase()->value())->toBe('عَلِي');
        expect((new Sanitizer(value: 'hello 世界'))->uppercase()->value())->toBe('HELLO 世界');
        expect((new Sanitizer(value: 'über straße'))->uppercase()->value())->toBe('ÜBER STRASSE');

        expect((new Sanitizer(value: ['über', 'foo', ['текст']]))->uppercase()->value())->toBe(['ÜBER', 'FOO', ['ТЕКСТ']]);
    });

    test('collapse method', function () {

        expect((new Sanitizer(value: 1))->collapse(char: '-')->value())->toBe(1);
        expect((new Sanitizer(value: 1.1))->collapse(char: '-')->value())->toBe(1.1);
        expect((new Sanitizer(value: null))->collapse(char: '-')->value())->toBe(null);
        expect((new Sanitizer(value: true))->collapse(char: '-')->value())->toBe(true);
        expect((new Sanitizer(value: false))->collapse(char: '-')->value())->toBe(false);
        expect((new Sanitizer(value: ''))->collapse(char: '-')->value())->toBe('');
        expect((new Sanitizer(value: ' '))->collapse(char: '-')->value())->toBe(' ');
        expect((new Sanitizer(value: []))->collapse(char: '-')->value())->toBe([]);
        expect((new Sanitizer(value: [123]))->collapse(char: '-')->value())->toBe([123]);

        expect((new Sanitizer(value: 'foo-bar'))->collapse(char: '')->value())->toBe('foo-bar');
        expect((new Sanitizer(value: 'foo---bar'))->collapse(char: '')->value())->toBe('foo---bar');

        expect((new Sanitizer(value: 'foo-bar'))->collapse(char: '-')->value())->toBe('foo-bar');
        expect((new Sanitizer(value: 'foo---bar'))->collapse(char: '-')->value())->toBe('foo-bar');
        expect((new Sanitizer(value: '---foo---bar---'))->collapse(char: '-')->value())->toBe('-foo-bar-');
        expect((new Sanitizer(value: 'foo___bar'))->collapse(char: '_')->value())->toBe('foo_bar');
        expect((new Sanitizer(value: 'foo_______bar'))->collapse(char: '_')->value())->toBe('foo_bar');

        expect((new Sanitizer(value: [1, 1.1, null, true, false, 'foo---bar', [1, 1.1, null, true, false, 'baz___qux']]))->collapse(char: '-')->value())->toBe([1, 1.1, null, true, false, 'foo-bar', [1, 1.1, null, true, false, 'baz___qux']]);
        expect((new Sanitizer(value: [1, 1.1, null, true, false, 'foo___bar', [1, 1.1, null, true, false, 'baz---qux']]))->collapse(char: '_')->value())->toBe([1, 1.1, null, true, false, 'foo_bar', [1, 1.1, null, true, false, 'baz---qux']]);

        expect((new Sanitizer(value: '---'))->collapse(char: '-')->value())->toBe('-');
        expect((new Sanitizer(value: 'foo..bar'))->collapse(char: '.')->value())->toBe('foo.bar');
        expect((new Sanitizer(value: 'foo...bar'))->collapse(char: '.')->value())->toBe('foo.bar');
        expect((new Sanitizer(value: 'foo+++bar'))->collapse(char: '+')->value())->toBe('foo+bar');
        expect((new Sanitizer(value: 'foo***bar'))->collapse(char: '*')->value())->toBe('foo*bar');
        expect((new Sanitizer(value: 'foo\\\\\\bar'))->collapse(char: '\\')->value())->toBe('foo\\bar');
        expect((new Sanitizer(value: 'foo$$$bar'))->collapse(char: '$')->value())->toBe('foo$bar');
        expect((new Sanitizer(value: 'foo^^^bar'))->collapse(char: '^')->value())->toBe('foo^bar');
        expect((new Sanitizer(value: 'foo|||bar'))->collapse(char: '|')->value())->toBe('foo|bar');
        expect((new Sanitizer(value: 'foo???bar'))->collapse(char: '?')->value())->toBe('foo?bar');
        expect((new Sanitizer(value: 'foo(((bar'))->collapse(char: '(')->value())->toBe('foo(bar');
        expect((new Sanitizer(value: 'foo)))bar'))->collapse(char: ')')->value())->toBe('foo)bar');
        expect((new Sanitizer(value: 'foo[[[bar'))->collapse(char: '[')->value())->toBe('foo[bar');
        expect((new Sanitizer(value: 'foo]]]bar'))->collapse(char: ']')->value())->toBe('foo]bar');
        expect((new Sanitizer(value: 'foo{{{bar'))->collapse(char: '{')->value())->toBe('foo{bar');
        expect((new Sanitizer(value: 'foo}}}bar'))->collapse(char: '}')->value())->toBe('foo}bar');
        expect((new Sanitizer(value: 'foo ی ی bar'))->collapse(char: 'ی')->value())->toBe('foo ی ی bar');
        expect((new Sanitizer(value: 'foo ییی bar'))->collapse(char: 'ی')->value())->toBe('foo ی bar');
    });

    test('trim method', function () {

        expect((new Sanitizer(value: 1))->trim(char: '-')->value())->toBe(1);
        expect((new Sanitizer(value: 1.1))->trim(char: '-')->value())->toBe(1.1);
        expect((new Sanitizer(value: null))->trim(char: '-')->value())->toBe(null);
        expect((new Sanitizer(value: true))->trim(char: '-')->value())->toBe(true);
        expect((new Sanitizer(value: false))->trim(char: '-')->value())->toBe(false);
        expect((new Sanitizer(value: ''))->trim(char: '-')->value())->toBe('');
        expect((new Sanitizer(value: ' '))->trim(char: '-')->value())->toBe(' ');
        expect((new Sanitizer(value: []))->trim(char: '-')->value())->toBe([]);
        expect((new Sanitizer(value: [123]))->trim(char: '-')->value())->toBe([123]);

        expect((new Sanitizer(value: 'foo-bar'))->trim(char: '-')->value())->toBe('foo-bar');
        expect((new Sanitizer(value: '-foo-bar'))->trim(char: '-')->value())->toBe('foo-bar');
        expect((new Sanitizer(value: 'foo-bar-'))->trim(char: '-')->value())->toBe('foo-bar');
        expect((new Sanitizer(value: '---foo---bar---'))->trim(char: '-')->value())->toBe('foo---bar');
        expect((new Sanitizer(value: '___foo___bar___'))->trim(char: '_')->value())->toBe('foo___bar');

        expect((new Sanitizer(value: [1, 1.1, null, true, false, '-foo-bar', [1, 1.1, null, true, false, '-baz-qux-']]))->trim(char: '-')->value())->toBe([1, 1.1, null, true, false, 'foo-bar', [1, 1.1, null, true, false, 'baz-qux']]);
        expect((new Sanitizer(value: [1, 1.1, null, true, false, '_foo_bar_', [1, 1.1, null, true, false, '_baz_qux']]))->trim(char: '_')->value())->toBe([1, 1.1, null, true, false, 'foo_bar', [1, 1.1, null, true, false, 'baz_qux']]);

        expect((new Sanitizer(value: '-'))->trim(char: '-')->value())->toBe('');
        expect((new Sanitizer(value: '---'))->trim(char: '-')->value())->toBe('');
        expect((new Sanitizer(value: '.foo.bar.'))->trim(char: '.')->value())->toBe('foo.bar');
        expect((new Sanitizer(value: '+foo+bar+'))->trim(char: '+')->value())->toBe('foo+bar');
        expect((new Sanitizer(value: '*foo*bar*'))->trim(char: '*')->value())->toBe('foo*bar');
        expect((new Sanitizer(value: '\\foo\\bar\\'))->trim(char: '\\')->value())->toBe('foo\\bar');
        expect((new Sanitizer(value: '$foo$bar$'))->trim(char: '$')->value())->toBe('foo$bar');
        expect((new Sanitizer(value: 'یfooیbarی'))->trim(char: 'ی')->value())->toBe('fooیbar');

        expect((new Sanitizer(value: '  hello  '))->trim()->value())->toBe('hello');
        expect((new Sanitizer(value: "\nhello\n"))->trim()->value())->toBe('hello');
        expect((new Sanitizer(value: "\thello\t"))->trim()->value())->toBe('hello');
        expect((new Sanitizer(value: "\rhello\r"))->trim()->value())->toBe('hello');
        expect((new Sanitizer(value: " \n\t\r\0hello \n\t\r\0"))->trim()->value())->toBe('hello');

        expect((new Sanitizer(value: 'hello'))->trim(char: '')->value())->toBe('hello');
        expect((new Sanitizer(value: '---hello---'))->trim(char: '')->value())->toBe('---hello---');

        expect((new Sanitizer(value: ' über straße '))->trim()->value())->toBe('über straße');
        expect((new Sanitizer(value: 'üüfooü'))->trim(char: 'ü')->value())->toBe('foo');
        expect((new Sanitizer(value: 'üüfoo'))->trim(char: 'ü')->value())->toBe('foo');
        expect((new Sanitizer(value: 'fooüü'))->trim(char: 'ü')->value())->toBe('foo');
        expect((new Sanitizer(value: 'üüfooüü'))->trim(char: 'ü')->value())->toBe('foo');
    });

    test('replaceSensitiveFiles method', function () {

        expect((new Sanitizer(value: 1))->replaceSensitiveFiles()->value())->toBe(1);
        expect((new Sanitizer(value: 1.1))->replaceSensitiveFiles()->value())->toBe(1.1);
        expect((new Sanitizer(value: null))->replaceSensitiveFiles()->value())->toBe(null);
        expect((new Sanitizer(value: true))->replaceSensitiveFiles()->value())->toBe(true);
        expect((new Sanitizer(value: false))->replaceSensitiveFiles()->value())->toBe(false);
        expect((new Sanitizer(value: ''))->replaceSensitiveFiles()->value())->toBe('');
        expect((new Sanitizer(value: ' '))->replaceSensitiveFiles()->value())->toBe(' ');
        expect((new Sanitizer(value: []))->replaceSensitiveFiles()->value())->toBe([]);
        expect((new Sanitizer(value: [123]))->replaceSensitiveFiles()->value())->toBe([123]);

        expect((new Sanitizer(value: 'hello world'))->replaceSensitiveFiles()->value())->toBe('hello world');

        expect((new Sanitizer(value: 'config/database.php'))->replaceSensitiveFiles()->value())->toBe('config/database');
        expect((new Sanitizer(value: 'storage/logs/laravel.log'))->replaceSensitiveFiles()->value())->toBe('storage/logs/');
        expect((new Sanitizer(value: '.env'))->replaceSensitiveFiles()->value())->toBe('');
        expect((new Sanitizer(value: '/.git/config'))->replaceSensitiveFiles()->value())->toBe('//config');

        expect((new Sanitizer(value: '.env.backup'))->replaceSensitiveFiles()->value())->toBe('');
        expect((new Sanitizer(value: '/.env.backup'))->replaceSensitiveFiles()->value())->toBe('/');
        expect((new Sanitizer(value: '/path/.env.example'))->replaceSensitiveFiles()->value())->toBe('/path/');

        expect((new Sanitizer(value: '/path/composer.json'))->replaceSensitiveFiles()->value())->toBe('/path/');

        expect((new Sanitizer(value: 'config/database.php'))->replaceSensitiveFiles(excludes: ['.php'])->value())->toBe('config/database.php');
        expect((new Sanitizer(value: '/path/.env'))->replaceSensitiveFiles(excludes: ['.env'])->value())->toBe('/path/.env');

        expect((new Sanitizer(value: 'config/database.php'))->replaceSensitiveFiles(replaceWith: '[REMOVED]')->value())->toBe('config/database[REMOVED]');
        expect((new Sanitizer(value: '.env'))->replaceSensitiveFiles(replaceWith: '[HIDDEN]')->value())->toBe('[HIDDEN]');

        expect((new Sanitizer(value: [1, 1.1, null, true, false, 'config/database.php', [1, 1.1, null, true, false, '.env']]))->replaceSensitiveFiles()->value())->toBe([1, 1.1, null, true, false, 'config/database', [1, 1.1, null, true, false, '']]);
    });

    test('replaceEmoji method', function () {

        expect((new Sanitizer(value: 1))->replaceEmoji()->value())->toBe(1);
        expect((new Sanitizer(value: 1.1))->replaceEmoji()->value())->toBe(1.1);
        expect((new Sanitizer(value: null))->replaceEmoji()->value())->toBe(null);
        expect((new Sanitizer(value: true))->replaceEmoji()->value())->toBe(true);
        expect((new Sanitizer(value: false))->replaceEmoji()->value())->toBe(false);
        expect((new Sanitizer(value: ''))->replaceEmoji()->value())->toBe('');
        expect((new Sanitizer(value: ' '))->replaceEmoji()->value())->toBe(' ');
        expect((new Sanitizer(value: []))->replaceEmoji()->value())->toBe([]);
        expect((new Sanitizer(value: [123]))->replaceEmoji()->value())->toBe([123]);

        expect((new Sanitizer(value: 'hello world'))->replaceEmoji()->value())->toBe('hello world');
        expect((new Sanitizer(value: 'foo123bar'))->replaceEmoji()->value())->toBe('foo123bar');

        expect((new Sanitizer(value: '😀'))->replaceEmoji()->value())->toBe('');
        expect((new Sanitizer(value: 'Hello 😎 World'))->replaceEmoji()->value())->toBe('Hello  World');
        expect((new Sanitizer(value: '😊😎👍'))->replaceEmoji()->value())->toBe('');
        expect((new Sanitizer(value: 'a😊b😎c👍d'))->replaceEmoji()->value())->toBe('abcd');
        expect((new Sanitizer(value: 'Hello 😊😎👍 World'))->replaceEmoji()->value())->toBe('Hello  World');
        expect((new Sanitizer(value: '🚗💨'))->replaceEmoji()->value())->toBe('');
        expect((new Sanitizer(value: '🇩🇪'))->replaceEmoji()->value())->toBe('');
        expect((new Sanitizer(value: '👨‍👩‍👧‍👦'))->replaceEmoji()->value())->toBe('');
        expect((new Sanitizer(value: '👋🏽'))->replaceEmoji()->value())->toBe('');
        expect((new Sanitizer(value: '٩'))->replaceEmoji()->value())->toBe('٩');

        expect((new Sanitizer(value: '😀'))->replaceEmoji(replaceWith: '[emoji]')->value())->toBe('[emoji]');
        expect((new Sanitizer(value: 'Hello 😎 World'))->replaceEmoji(replaceWith: '-')->value())->toBe('Hello - World');

        expect((new Sanitizer(value: [1, 1.1, null, true, false, 'hello 😊 world', [1, 1.1, null, true, false, 'foo 😎 bar']]))->replaceEmoji()->value())->toBe([1, 1.1, null, true, false, 'hello  world', [1, 1.1, null, true, false, 'foo  bar']]);

        expect((new Sanitizer(value: ['nested' => ['deep' => 'test 😊']]))->replaceEmoji()->value())->toBe(['nested' => ['deep' => 'test ']]);

        $keycap = "#\u{FE0F}\u{20E3}";
        expect((new Sanitizer(value: $keycap))->replaceEmoji()->value())->toBe('#');

        $variation = "\u{A9}\u{FE0F}";
        expect((new Sanitizer(value: $variation))->replaceEmoji()->value())->toBe("\u{A9}");

        $england = "\u{1F3F4}\u{E0067}\u{E0062}\u{E0065}\u{E006E}\u{E0067}\u{E007F}";
        expect((new Sanitizer(value: $england))->replaceEmoji()->value())->toBe('');
    });

    describe('handle exceptions', function () {

        test('flat array with many items does not trigger false recursion limit', function () {

            $flat = array_map(fn($i) => "item-$i", range(1, 30));

            expect(fn() => (new Sanitizer(value: $flat))->lowercase()->value())->not->toThrow(InfiniteLoopException::class);
            expect(fn() => (new Sanitizer(value: $flat))->uppercase()->value())->not->toThrow(InfiniteLoopException::class);
            expect(fn() => (new Sanitizer(value: $flat))->replaceForbiddenCharacters()->value())->not->toThrow(InfiniteLoopException::class);
            expect(fn() => (new Sanitizer(value: $flat))->replaceSensitiveFiles()->value())->not->toThrow(InfiniteLoopException::class);
            expect(fn() => (new Sanitizer(value: $flat))->replaceEmoji()->value())->not->toThrow(InfiniteLoopException::class);
            expect(fn() => (new Sanitizer(value: $flat))->collapse(char: '-')->value())->not->toThrow(InfiniteLoopException::class);
            expect(fn() => (new Sanitizer(value: $flat))->trim(char: '-')->value())->not->toThrow(InfiniteLoopException::class);
        });

        test('recursion limit throws before exceeding max depth', function () {

            $nested = ['trigger'];

            for ($i = 0; $i < 25; $i++) {
                $nested = [$nested];
            }

            expect(fn() => (new Sanitizer(value: $nested))->lowercase()->value())->toThrow(InfiniteLoopException::class, 'msg.infiniteLoopExceptionSanitizerRecursionLimit');

            try {

                (new Sanitizer(value: $nested))->lowercase()->value();

                //
            } catch (InfiniteLoopException $e) {

                expect($e->getMessage())->toBe('msg.infiniteLoopExceptionSanitizerRecursionLimit');
                expect($e->getCode())->toBe(252);
                expect($e->getLevel())->toBe('critical');
                expect($e->getHttpStatusCode())->toBe(500);
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'    => 'toLowercase',
                    'attempted' => 26,
                    'value'     => $nested
                ]);
            }
        });
    });
});
