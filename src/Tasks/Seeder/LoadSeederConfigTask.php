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

        $path = str_replace('/vendor/orchestra/testbench-core/laravel', '', $path);

        if (
            file_exists($path)
        ) {

            return $path;

            // 
        } else {

            return preg_replace('#/vendor/fooino/[^/]+#', '', $path);
        }
    }
}
