<?php

namespace Fooino\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trash extends Model
{

    use
        SoftDeletes;

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


    /**
     * custom functions section
     */
}
