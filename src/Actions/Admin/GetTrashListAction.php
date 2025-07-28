<?php


namespace Fooino\Core\Actions\Admin;

use Fooino\Core\Models\Trash;

class GetTrashListAction
{
    public function run()
    {
        $ids = $this->getIds();

        return Trash::latest()
            ->removedByAdmin()
            ->inIds($ids)
            ->with($this->getWith())
            ->paginate(pg());
    }

    private function getIds(): array
    {
        $ids = [];

        $models = Trash::removedByAdmin()->pluck('trashable_type')->toArray();


        $models = array_filter($models, function ($model) {
            return (new $model)->trashPermission();
        });


        $ids = Trash::removedByAdmin()->inTrashableType($models)->pluck('trashable_id')->toArray();

        return filled($ids) ? $ids : [0];
    }


    private function getWith(): array
    {
        $with = [
            'trashable',
        ];

        if (class_exists('Fooino\Admin\Models\Admin')) {
            $with[] = 'removerable';
        }

        return $with;
    }
}
