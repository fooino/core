<?php

namespace Fooino\Core\Actions\Admin;

use Illuminate\Http\Request;

class MoveToTrashAction
{
    public function run(Request $request)
    {
        dbTransaction(function () use ($request) {

            $model = app($request->safe()->model)->findOrFail(id: $request->safe()->model_id);

            $model->checkPermission(key: 'moveToTrashPermission');

            $model->moveToTrash();
        });
    }
}
