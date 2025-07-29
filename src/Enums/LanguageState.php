<?php

namespace Fooino\Core\Enums;

use Fooino\Core\Traits\Enumable;

enum LanguageState: string
{
    use Enumable;

    case DEFAULT    = 'DEFAULT';
    case UNDEFAULT  = 'UNDEFAULT';

    public static function default(): array
    {
        return self::maker(
            key: self::DEFAULT->value,
            query: 'state=' . self::DEFAULT->value,
            icon: 'check_circle',
            color: FOOINO_TEXT_SUCCESS
        );
    }

    public static function undefault(): array
    {
        return self::maker(
            key: self::UNDEFAULT->value,
            query: "state=" . self::UNDEFAULT->value,
            icon: 'cancel',
            color: FOOINO_TEXT_DANGER
        );
    }
}
