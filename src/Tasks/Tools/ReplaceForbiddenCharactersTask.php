<?php

namespace Fooino\Core\Tasks\Tools;

class ReplaceForbiddenCharactersTask
{

    public function run(
        string|int|float|null|array|bool $value,
        array $excludes = [],
        string $replacementChar = '_'
    ): string|int|float|null|array|bool {

        if (
            blank($value) ||
            !is_string($value)
        ) {
            return $value;
        }

        $forbiddens = $this->forbiddenCharacters();

        foreach ($excludes as $exclude) {

            foreach ($forbiddens as $key => $forbidden) {

                if (
                    $exclude == $forbidden
                ) {

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
