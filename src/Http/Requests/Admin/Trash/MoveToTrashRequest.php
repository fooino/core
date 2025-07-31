<?php


namespace Fooino\Core\Http\Requests\Admin\Trash;

use Fooino\Core\Rules\CheckModelForMoveToTrashRule;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class MoveToTrashRequest extends FormRequest
{

    public string $unauthorizedMessage = '';

    protected function failedAuthorization()
    {
        throw new AuthorizationException($this->unauthorizedMessage);
    }


    public function rules(): array
    {
        return [
            'model'             => [
                'required',
                'max:255',
                new CheckModelForMoveToTrashRule()
            ],
            'model_id'          => [
                'required',
                'numeric',
            ],
        ];
    }


    public function prepareForValidation(): void
    {
        $merge = [
            'model' => emptyToNullOrValue(value: getFooinoModelByName(name: $this?->model ?? '') ?? $this?->model ?? null)
        ];

        $this->merge($merge);
        request()->merge($merge);
    }


    public function passedValidation(): void
    {
        if (
            !app($this->model)->moveToTrashPermission()
        ) {
            $this->unauthorizedMessage = __(key: 'msg.unauthorizedToMoveToTrash');
            $this->failedAuthorization();
        }
    }
}
