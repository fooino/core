<?php

namespace Fooino\Core\Actions\Admin;

use Fooino\Core\Models\Trash;

class RestoreFromTrashAction
{
    public function run(Trash $trash)
    {
        dbTransaction(function () use ($trash) {

            $trash->trashable->checkPermission(key: 'restoreFromTrashPermission');

            $trash->trashable->restoreFromTrash();
        });
    }
}
