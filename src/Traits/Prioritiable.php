<?php

namespace Fooino\Core\Traits;

use Fooino\Core\Scopes\OrderByPriorityScope;
use Illuminate\Database\Eloquent\Builder;

trait Prioritiable
{
    protected static function bootPrioritiable(): void
    {
        static::addGlobalScope(OrderByPriorityScope::class);
    }

    public function scopeDisablePrioritiable(Builder $query): void
    {
        $query->withoutGlobalScope(OrderByPriorityScope::class);
    }

    public function getPrioritySort(): string
    {
        return 'ASC';
    }

    public function getIdSort(): string
    {
        return 'DESC';
    }


    public function changePriorityPermission(): bool
    {
        $can = lcfirst(class_basename($this)) . '-update';

        if (
            filled(request()->user()) &&
            request()->user()->can($can)
        ) {
            return true;
        }

        return false;
    }
}
