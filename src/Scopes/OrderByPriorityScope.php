<?php

namespace Fooino\Core\Scopes;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Scope
};

class OrderByPriorityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();

        $builder
            ->orderBy(
                $table . '.priority',
                $model->getPrioritySort()
            )
            ->orderBy(
                $table . '.id',
                $model->getIdSort()
            );
    }
}
