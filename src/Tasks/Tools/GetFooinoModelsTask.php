<?php

namespace Fooino\Core\Tasks\Tools;

class GetFooinoModelsTask
{
    public function run(): array
    {
        $models = [];
        $path = $this->prettifyPath(path: base_path('vendor/fooino/*/src/Models/*.php'));
        $files = glob($path);

        foreach ($files as $file) {

            if (
                preg_match('#/vendor/fooino/([^/]+)/src/Models/([^/]+)\.php#', $file, $matches)
            ) {
                $package = ucfirst($matches[1] ?? '');
                $className = $matches[2] ?? '';

                if (
                    blank($package) ||
                    blank($className)
                ) {
                    continue;
                }

                $model = "Fooino\\$package\\Models\\$className";

                if (
                    class_exists($model) &&
                    is_subclass_of($model, 'Illuminate\Database\Eloquent\Model')
                ) {
                    $key = lcfirst($className);
                    $models[$key] = $model;
                }
            }
        }

        return $models;
    }


    private function prettifyPath(string $path): string
    {
        return str_replace('/vendor/orchestra/testbench-core/laravel', '', $path);
    }
}
