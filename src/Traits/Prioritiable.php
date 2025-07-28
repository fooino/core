<?php

namespace Fooino\Core\Traits;

use Fooino\Core\Scopes\OrderByPriorityScope;

trait Prioritiable
{
    protected static function booted(): void
    {
        static::addGlobalScope(new OrderByPriorityScope());
    }

    public function getPrioritySort(): string
    {
        return 'ASC';
    }

    public function getIdSort(): string
    {
        return 'DESC';
    }


    public function changePriorityPermission()
    {
        $can = ucfirst(class_basename($this)) . '-update';

        if (
            filled(request()->user()) &&
            request()->user()->can($can)
        ) {
            return true;
        }

        return false;
    }
}
