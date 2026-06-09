<?php

namespace Fooino\Core\Tests\Data\Traits;

trait Datasetable
{
    public static function merge(mixed ...$sets): array
    {
        $items = count($sets) === 1 && is_array($sets[0]) ? $sets[0] : $sets;
        $data = [];

        foreach ($items as $item) {

            $merge = match (true) {

                is_array($item)                                             => $item,

                is_string($item) && method_exists(static::class, $item)     => self::{$item}(),

                default                                                     => [$item]
            };

            $data = array_merge($data, $merge);
        }

        return $data;
    }
}
