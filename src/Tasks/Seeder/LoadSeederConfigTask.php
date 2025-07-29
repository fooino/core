<?php

namespace Fooino\Core\Tasks\Seeder;


class LoadSeederConfigTask
{
    public function run(string $key): void
    {
        $config = app('config');

        if ($config->has($key)) {
            return;
        }

        $config->set($key, require __DIR__ . "/../../config/{$key}.php");
    }
}
