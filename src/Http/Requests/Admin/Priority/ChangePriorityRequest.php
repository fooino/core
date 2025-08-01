<?php


namespace Fooino\Core\Http\Requests\Admin\Priority;

use Fooino\Core\Rules\CheckModelForChangePriorityRule;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class ChangePriorityRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

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
                new CheckModelForChangePriorityRule()
            ],
            'model_id'          => [
                'required',
                'numeric',
            ],
            'priority'          => [
                'required',
                'numeric'
            ]
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
            !app($this->model)->changePriorityPermission()
        ) {
            $this->unauthorizedMessage = __(key: 'msg.unauthorizedToChangePriority');
            $this->failedAuthorization();
        }
    }
}
