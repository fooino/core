<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Exceptions\InfiniteLoopException;
use Fooino\Core\Support\Sanitizer;

describe('Sanitizer utilities', function () {

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
    });

    describe('handle exceptions', function () {

        test('recursion limit throws before exceeding max depth', function () {

            $nested = ['trigger'];
            for ($i = 0; $i < 25; $i++) {
                $nested = [$nested];
            }

            expect(fn() => (new Sanitizer(value: $nested))->lowercase()->value())->toThrow(InfiniteLoopException::class);

            try {

                (new Sanitizer(value: $nested))->lowercase()->value();

                //
            } catch (InfiniteLoopException $e) {

                expect($e->getMessage())->toBe('msg.infiniteLoopException');
                expect($e->getCode())->toBe(10201);
                expect($e->getLevel())->toBe('critical');
                expect($e->reportable())->toBeTrue();
                expect($e->getWith())->toBe([
                    'method'    => 'toLowercase',
                    'attempted' => 26,
                ]);
            }
        });
    });
});
