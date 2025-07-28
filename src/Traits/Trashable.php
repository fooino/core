<?php

namespace Fooino\Core\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Fooino\Core\Models\Trash;
use Exception;

trait Trashable
{
    abstract public function restore(); // the model must use the SoftDeletes


    public static function bootTrashable()
    {
        static::deleted(function ($model) {
            $model->moveToTrash();
        });

        static::restored(function ($model) {
            $model->restoreFromTrash();
        });
    }

    public function moveToTrash(): void
    {

        Trash::withTrashed()->firstOrCreate(
            [
                'trashable_type'    => get_class($this),
                'trashable_id'      => $this->id,
            ],
            getUserable(able: 'removerable')
        );

        // 
    }

    public function restoreFromTrash()
    {
        Trash::withTrashed()->where('trashable_id', $this->id)->where('trashable_type', get_class($this))->forceDelete();
    }

    public function trashPermission()
    {
        $can = ucfirst(class_basename($this)) . '-delete';

        if (
            filled(request()->user()) &&
            request()->user()->can($can)
        ) {
            return true;
        }

        return false;
    }
}
