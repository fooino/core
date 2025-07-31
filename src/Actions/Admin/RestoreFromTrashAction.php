<?php

namespace Fooino\Core\Actions\Admin;

use Fooino\Core\Models\Trash;
use Illuminate\Auth\Access\AuthorizationException;

class RestoreFromTrashAction
{
    public function run(Trash $trash): bool
    {
        return dbTransaction(function () use ($trash) {

            throw_if(
                !$trash->trashable->restoreFromTrashPermission(),
                new AuthorizationException(
                    message: __(key: 'msg.unauthorizedToRestoreFromTrash')
                )
            );

            $trash->trashable->restoreFromTrash();

            return true;
        });
    }
}
