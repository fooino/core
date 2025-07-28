<?php

namespace Fooino\Core\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Fooino\Core\Models\Trash;
use Exception;

trait Trashable
{
    // abstract public function permission(): bool;

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

    public function modelKeyName(): string
    {
        return 'name';
    }



    // public function trashedList(): Collection|Exception
    // {
    //     $this->checkPermission();

    //     return $this
    //         ->onlyTrashed()
    //         ->select($this->makeSelectArray())
    //         ->with($this->makeWithArray())
    //         ->get();
    // }

    // public function getTrashById(int|float $id): self|Exception
    // {
    //     $this->checkPermission();

    //     return $this
    //         ->onlyTrashed()
    //         ->select($this->makeSelectArray())
    //         ->with($this->makeWithArray())
    //         ->findOrFail($id);
    // }

    // // public function restoreFromTrash(): bool|null|Exception
    // // {
    // //     $this->checkPermission();
    // //     return $this->restore();
    // // }

    // public function trashedCount(): int|float|Exception
    // {
    //     $this->checkPermission();
    //     return $this->onlyTrashed()->count('id');
    // }

    // public function checkPermission(): bool|AuthorizationException
    // {
    //     throw_if(
    //         !$this->permission(),
    //         AuthorizationException::class,
    //         __(key: 'core::messages.notAuthorizedToPerformTrashActions')
    //     );

    //     return true;
    // }

    // public function checkPermissionByKey(string|null $key): bool
    // {
    //     if (
    //         filled($key) &&
    //         filled(request()->user()) &&
    //         request()->user()->can(str_replace('can:', '', $key))
    //     ) {
    //         return true;
    //     }

    //     return false;
    // }

    // public function modelUseTranslatable(): bool
    // {
    //     return in_array(
    //         'Astrotomic\Translatable\Translatable',
    //         class_uses($this)
    //     );
    // }

    // public function modelUseMediable(): bool
    // {
    //     return in_array(
    //         'Fooino\Media\Traits\Mediable',
    //         class_uses($this)
    //     );
    // }

    // public function makeSelectArray(): array
    // {
    //     $select = ['id', 'deleted_at'];
    //     if (
    //         !$this->modelUseTranslatable()
    //     ) {
    //         $select[] = $this->modelKeyName();
    //     }

    //     return $select;
    // }

    // public function makeWithArray(): array
    // {
    //     $with = [];
    //     if (
    //         $this->modelUseTranslatable()
    //     ) {
    //         $with[] = 'translations:' . $this->getForeignKey() . ',locale,' . $this->modelKeyName();
    //     }

    //     if (
    //         $this->modelUseMediable()
    //     ) {
    //         $with[] = 'media';
    //     }

    //     return $with;
    // }
}
