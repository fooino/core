<?php

namespace Fooino\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

trait Searchable
{
    public function scopeSortByStateAndStatus(Builder $query, bool $byPriority = true)
    {
        $query
            ->orderByRaw(
                "CASE 
                    WHEN `state` = 'DEFAULT' THEN 1
                    WHEN `state` = 'NON_DEFAULT' THEN 0
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

    public function scopeSortByStatus(Builder $query, bool $byPriority = true)
    {
        $query
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


    public function scopeStatus(Builder $query, UnitEnum|string|null $status = null): void
    {
        if (
            filled($status)
        ) {
            $query->where('status', enumOrValue($status));
        }
    }

    public function scopeActive(Builder $query): void
    {
        $query->status('ACTIVE');
    }

    public function scopeInactive(Builder $query): void
    {
        $query->status('INACTIVE');
    }

    public function scopeState(Builder $query, UnitEnum|string|null $state = null): void
    {
        if (
            filled($state)
        ) {
            $query->where('state', enumOrValue($state));
        }
    }

    public function scopeDefault(Builder $query): void
    {
        $query->state('DEFAULT');
    }

    public function scopeNonDefault(Builder $query): void
    {
        $query->state('NON_DEFAULT');
    }

    public function getStatusDetailAttribute()
    {
        return ($this->modelStatusEnumClass())::from(value: $this->status)->detail();
    }

    public function getStatusesAttribute()
    {
        return ($this->modelStatusEnumClass())::statuses(id: $this->id);
    }


    public function getStateDetailAttribute()
    {
        return ($this->modelStateEnumClass())::from(value: $this->state)->detail();
    }




    public function scopeInIds(Builder $query, array|int|float|null $ids = null): void
    {
        if (
            !is_null($ids)
        ) {
            $ids = filled($ids) ? array_unique((array) $ids) : [0];

            $query->whereIn('id', $ids);
        }
    }
}
