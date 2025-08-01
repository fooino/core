<?php

namespace Fooino\Core\Enums;

use Fooino\Core\Traits\Enumable;

enum LanguageState: string
{
    use Enumable;

    case DEFAULT        = 'DEFAULT';
    case NON_DEFAULT    = 'NON_DEFAULT';

    public static function default(): array
    {
        return self::maker(
            key: self::DEFAULT->value,
            query: 'state=' . self::DEFAULT->value,
            icon: 'check_circle',
            color: FOOINO_TEXT_SUCCESS
        );
    }

    public static function nonDefault(): array
    {
        return self::maker(
            key: self::NON_DEFAULT->value,
            query: "state=" . self::NON_DEFAULT->value,
            icon: 'cancel',
            color: FOOINO_TEXT_DANGER
        );
    }
}
