<?php

namespace Fooino\Core\Tasks\Seeder;


class LoadSeederConfigTask
{
    public function run(string $path): void
    {
        $path = $this->prettifyPath(path: $path);

        $file = str_replace('.php', '', basename($path));

        $config = app('config');

        if ($config->has($file)) {
            return;
        }

        $config->set($file, require $path);
    }


    private function prettifyPath(string $path): string
    {
        if (
            str_contains($path, '/vendor/orchestra/testbench-core/laravel')
        ) {
            return str_replace(
                [
                    '/vendor/orchestra/testbench-core/laravel',
                    '/vendor/fooino/core',
                    '/vendor/fooino/world',
                ],
                '',
                $path
            );
        }

        return $path;
    }
}
