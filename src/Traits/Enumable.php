<?php

namespace Fooino\Core\Traits;

trait Enumable
{
    public static function values(): array
    {
        return \array_column(self::cases(), 'value');
    }

    public static function randomValue(): string
    {
        $values = self::values();
        return $values[rand(0, (count($values) - 1))] ?? '';
    }

    public function detail(): array
    {
        $value = lcfirst(str(strtolower($this->value))->camel()->value());
        return self::$value();
    }

    public static function info(): array
    {
        $result = [];
        foreach (self::values() as $value) {

            $value = lcfirst(str(strtolower($value))->camel()->value());

            $result[] = self::$value();
        }

        return $result;
    }

    public static function maker(
        string $key         = 'defaultKey',
        string $name        = '',
        string $query       = '',
        string $endpoint    = '',
        string $iconClass   = 'msr',
        string $icon        = '',
        string $color       = '',
        string $bgColor     = '',
        array $additional   = [],
    ): array {

        $made = array_merge(
            [
                'key'           => $key,
                'name'          => filled($name) ? $name : ($key == 'defaultKey' ? 'unknown' :  __(key: "msg." . lcfirst(str(strtolower($key))->camel()->value()))),
                'icon_class'    => $iconClass,
                'icon'          => $icon,
                'color'         => $color,
                'bg_color'      => filled($bgColor) ? $bgColor : str_replace('text-', 'bg-', $color)
            ],
            $additional
        );

        if (
            filled($query)
        ) {
            $made['query'] = $query;
        }
        if (
            filled($endpoint)
        ) {
            $made['endpoint'] = $endpoint;
        }


        return $made;

        // 
    }
}
