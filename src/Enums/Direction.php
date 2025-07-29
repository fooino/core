<?php

namespace Fooino\Core\Enums;

use Fooino\Core\Traits\Enumable;

enum Direction: string
{
    use Enumable;

    case RTL = 'RTL';
    case LTR = 'LTR';

    public static function rtl(): array
    {
        return self::maker(
            key: self::RTL->value,
            query: 'direction=' . self::RTL->value,
            icon: 'format_textdirection_r_to_l',
            color: FOOINO_TEXT_SUCCESS
        );
    }

    public static function ltr(): array
    {
        return self::maker(
            key: self::LTR->value,
            query: 'direction=' . self::LTR->value,
            icon: 'format_textdirection_l_to_r',
            color: FOOINO_TEXT_PRIMARY
        );
    }
}
