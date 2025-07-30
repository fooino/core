<?php

namespace Fooino\Core\Actions\Admin;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Fooino\Core\Events\ModelPriorityChangedEvent;

class ChangePriorityAction
{
    public function run(Request $request): Model
    {

        return dbTransaction(function () use ($request) {

            $model = app($request->safe()->model)->findOrFail($request->safe()->model_id);

            $oldPriority = $model->priority;

            $model->update([
                'priority' => $request->safe()->priority
            ]);

            if (
                $oldPriority != $model->priority
            ) {

                event(new ModelPriorityChangedEvent(
                    model: $model,
                    oldPriority: $oldPriority,
                    newPriority: $model->priority,
                ));

                // 
            }

            return $model;
        });
    }
}
