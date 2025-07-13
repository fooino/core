<?php

namespace Fooino\Core\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Exception;

trait Trashable
{
    abstract public function modelKeyName(): string;

    abstract public function permission(): bool;

    abstract public function restore();

    public function trashedList(): Collection|Exception
    {
        $this->checkPermission();

        return $this
            ->onlyTrashed()
            ->select($this->makeSelectArray())
            ->get();
    }

    public function getTrashById(int|float $id): self|Exception
    {
        $this->checkPermission();

        return $this
            ->onlyTrashed()
            ->select($this->makeSelectArray())
            ->findOrFail($id);
    }

    public function restoreFromTrash(): bool|null|Exception
    {
        $this->checkPermission();
        return $this->restore();
    }

    public function trashedCount(): int|float|Exception
    {
        $this->checkPermission();
        return $this->onlyTrashed()->count('id');
    }

    public function checkPermission(): bool|AuthorizationException
    {
        throw_if(
            !$this->permission(),
            AuthorizationException::class,
            __(key: 'msg.notAuthorizedToPerformTrashActions')
        );

        return true;
    }

    public function checkPermissionByKey(string|null $key): bool
    {
        if (
            filled($key) &&
            filled(request()->user()) &&
            request()->user()->can(str_replace('can:', '', $key))
        ) {
            return true;
        }

        return false;
    }

    public function makeSelectArray(): array
    {
        return ['id', $this->modelKeyName(), 'deleted_at'];
    }
}
