<?php

namespace Fooino\Core\Models;

use Fooino\Core\Traits\Dateable;
use Fooino\Core\Traits\Infoable;
use Fooino\Core\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trash extends Model
{

    use
        SoftDeletes,
        Dateable,
        Infoable,
        Searchable;

    protected $guarded = ['id'];

    /**
     * relationships section
     */

    public function trashable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function removerable(): MorphTo
    {
        return $this->morphTo()->withoutTrashed();
    }

    /**
     * attributes section
     */

    public function getTrashedAttribute()
    {

        if (
            !$this->relationLoaded(key: 'trashable')
        ) {
            return [
                'id'            => 0,
                'name'          => __(key: 'msg.unknown'),
                'type'          => '',
                'deleted_at'    => '',
                'deleted_at_tz' => '',
                'media'         => [],
            ];
        }

        return [
            'id'                => $this->trashable->id,

            ...$this->trashable->objectName(),

            'deleted_at'        => $this->trashable->deleted_at,
            'deleted_at_tz'     => $this->trashable->deleted_at_tz,

            'media'             => $this->trashable->relationLoaded(key: 'media') ? $this->trashable->media : [],
        ];
    }

    public function getRemoverAttribute()
    {
        return userInfo(model: $this, key: 'removerable');
    }

    /**
     * scopes section
     */

    public function scopeRemovedByAdmin(Builder $query): void
    {
        $query->where('removerable_type', 'Fooino\Admin\Models\Admin');
    }

    public function scopeInTrashableType(Builder $query, array|string|null $types = null): void
    {
        if (
            !is_null($types)
        ) {
            $types = filled($types) ? array_unique((array) $types) : [0];
            $query->whereIn('trashable_type', (array) $types);
        }
    }


    /**
     * custom functions section
     */
}
