<?php

namespace Fooino\Core\Traits;

use Fooino\Core\Models\Trash;
use Illuminate\Auth\Access\AuthorizationException;

trait Trashable
{
    abstract public function restore(); // the model must use the SoftDeletes


    public static function bootTrashable()
    {
        static::deleted(function ($model) {
            $model->addToTrash();
        });

        static::restored(function ($model) {
            $model->removeFromTrash();
        });
    }

    public function addToTrash(): void
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

    public function removeFromTrash()
    {
        Trash::withTrashed()->where('trashable_id', $this->id)->where('trashable_type', get_class($this))->forceDelete();
    }

    public function moveToTrash(): void
    {
        $this->delete();
    }

    public function restoreFromTrash(): void
    {
        $this->restore();
    }

    public function restoreFromTrashPermission()
    {
        $can = lcfirst(class_basename($this)) . '-restore';

        if (
            filled(request()->user()) &&
            request()->user()->can($can)
        ) {
            return true;
        }

        return false;
    }

    public function moveToTrashPermission()
    {
        $can = lcfirst(class_basename($this)) . '-delete';

        if (
            filled(request()->user()) &&
            request()->user()->can($can)
        ) {
            return true;
        }

        return false;
    }
}
