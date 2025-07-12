<?php

use Fooino\Core\Tasks\Tools\ReplaceForbiddenCharactersTask;

if (!function_exists('replaceForbiddenCharacters')) {

    function replaceForbiddenCharacters(
        string|int|float|null $value,
        array $excludes = [],
        string $replacementChar = '_'
    ) {
        return filled($value) ? app(ReplaceForbiddenCharactersTask::class)->run(value: $value, excludes: $excludes, replacementChar: $replacementChar) : $value;
    }
}