<?php

namespace Fooino\Core\Models;

use Fooino\Core\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use
        FullTextSearch;

    protected $guarded = ['id'];

    public $searchable = ['name'];


    /**
     * relationships section
     */

    /**
     * attributes section
     */

    /**
     * scopes section
     */
    public function scopeSearch(Builder $query, string|int|float|bool|null $search = null): void
    {
        if (
            filled($search)
        ) {
            $query->where(
                fn($q) =>
                $q
                    ->orFullTextSearch($search)
                    ->orWhere('name', 'LIKE', "%{$search}%")
            );
        }
    }


    /**
     * custom functions section
     */
}
