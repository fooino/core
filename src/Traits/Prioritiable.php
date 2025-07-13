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
}
