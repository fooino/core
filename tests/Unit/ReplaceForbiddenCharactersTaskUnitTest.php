<?php

namespace Fooino\Core\Tests\Unit;

use Fooino\Core\Tasks\Tools\ReplaceForbiddenCharactersTask;
use Fooino\Core\Tests\TestCase;

class ReplaceForbiddenCharactersTaskUnitTest extends TestCase
{
    public function test_the_task()
    {
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
        $result = app(ReplaceForbiddenCharactersTask::class)->run(value: implode('a', $forbidden));
        $this->assertEquals($result, '_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a_a___a_');

        $result = app(ReplaceForbiddenCharactersTask::class)->run(
            value: implode('a', $forbidden),
            excludes: ['-', '!'],
            replacementChar: 'X'
        );
        $this->assertEquals($result, '-aXa!aXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXaXXXaX');
    }
}
