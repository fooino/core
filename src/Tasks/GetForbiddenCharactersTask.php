<?php

namespace Fooino\Core\Tasks;

use Fooino\Core\Support\SingletonableTask;

class GetForbiddenCharactersTask extends SingletonableTask
{
    /**
     * Build the forbidden character list, ordered by length so multi-character entries like '../' are replaced before their single-character parts
     */
    protected function getData(): mixed
    {
        $chars = [
            ' ',
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
            '[',
            ']',
            '~',
            '°',
            '../',
            '_'
        ];

        usort($chars, fn(mixed $a, mixed $b) => strlen($b) <=> strlen($a));

        return $chars;
    }
}
