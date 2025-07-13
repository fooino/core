<?php

namespace Fooino\Core\Scopes;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Scope
};

class OrderByPriorityScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
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
