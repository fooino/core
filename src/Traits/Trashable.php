<?php

namespace Fooino\Core\Traits;

use Fooino\Core\Models\Trash;

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
        $this->deleteOrFail();
    }

    public function restoreFromTrash(): void
    {
        $this->restore();
    }

    public function restoreFromTrashPermission(): bool
    {
        $can = lcfirst($this->objectClassName()) . '-restore';

        if (
            filled(request()->user()) &&
            request()->user()->can($can)
        ) {
            return true;
        }

        return false;
    }

    public function moveToTrashPermission(): bool
    {
        $can = lcfirst($this->objectClassName()) . '-delete';

        if (
            filled(request()->user()) &&
            request()->user()->can($can)
        ) {
            return true;
        }

        return false;
    }

    public function getHasMoveToTrashPermissionAttribute()
    {
        return once(fn() => $this->moveToTrashPermission());
    }

    public function getHasRestoreFromTrashPermissionAttribute()
    {
        return once(fn() => $this->restoreFromTrashPermission());
    }
}
