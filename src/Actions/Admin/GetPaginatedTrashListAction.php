<?php


namespace Fooino\Core\Actions\Admin;

use Fooino\Core\Models\Trash;

class GetPaginatedTrashListAction
{
    public function run()
    {
        return Trash::latest('id')
            ->removedByAdmin()
            ->with($this->getWith())
            ->paginate(pg());
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
