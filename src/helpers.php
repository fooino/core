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

if (
    !function_exists('trimEmptyString')
) {

    function trimEmptyString(mixed $value): mixed
    {
        return (\is_string($value) && filled($value)) ? trim($value) : $value;
    }
}
