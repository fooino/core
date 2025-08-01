<?php

namespace Fooino\Core\Enums;

use Fooino\Core\Traits\Enumable;

enum LanguageStatus: string
{
    use Enumable;

    case ACTIVE     = 'ACTIVE';
    case INACTIVE   = 'INACTIVE';

    public static function statuses(int $id): array
    {
        return [
            self::active(id: $id),
            self::inactive(id: $id),
        ];
    }

    public static function active(int|null $id = null): array
    {
        return self::maker(
            key: self::ACTIVE->value,
            query: 'status=' . self::ACTIVE->value,
            endpoint: filled($id) ? "languages/{$id}/activate" : '',
            icon: 'check_circle',
            color: FOOINO_TEXT_SUCCESS
        );
    }

    public static function inactive(int|null $id = null): array
    {
        return self::maker(
            key: self::INACTIVE->value,
            query: "status=" . self::INACTIVE->value,
            endpoint: filled($id) ? "languages/{$id}/deactivate" : '',
            icon: 'cancel',
            color: FOOINO_TEXT_DANGER
        );
    }
}
