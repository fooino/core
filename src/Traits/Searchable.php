<?php

namespace Fooino\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeSortByStateAndStatus(Builder $query, bool $byPriority = true)
    {
        $query
            ->orderByRaw(
                "CASE 
                    WHEN `state` = 'DEFAULT' THEN 1
                    WHEN `state` = 'UNDEFAULT' THEN 0
                END 
                DESC"
            )
            ->orderByRaw(
                "CASE 
                    WHEN `status` = 'ACTIVE' THEN 1
                    WHEN `status` = 'INACTIVE' THEN 0
                END 
                DESC"
            );

        if (
            $byPriority
        ) {
            $table = $this->getTable();
            $query
                ->orderBy(
                    $table . '.priority',
                    (method_exists($this, 'getPrioritySort') ? $this->getPrioritySort() : 'ASC')
                )
                ->orderBy(
                    $table . '.id',
                    (method_exists($this, 'getIdSort') ? $this->getIdSort() : 'DESC')
                );
        }
    }
}
