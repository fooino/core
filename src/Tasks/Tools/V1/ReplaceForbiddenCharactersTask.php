<?php

namespace Fooino\Core\Tasks\Tools\V1;

class ReplaceForbiddenCharactersTask
{

    public function run(
        string|int|float $value,
        array $excludes = [],
        string $replacementChar = '_'
    ): string {

        $forbiddens = $this->forbiddenCharacters();

        foreach ($excludes as $exclude) {
            foreach ($forbiddens as $key => $forbidden) {
                if ($exclude == $forbidden) {
                    unset($forbiddens[$key]);
                }
            }
        }

        return \str_replace(
            $forbiddens,
            $replacementChar,
            $value
        );
    }

    private function forbiddenCharacters(): array
    {
        return [
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
    }
}
