<?php


namespace Fooino\Core\Http\Requests\Admin\Priority;

use Illuminate\Foundation\Http\FormRequest;

class ChangePriorityRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;


    public function rules(): array
    {
        return [
            'model'             => [
                'required',
                'max:255',
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
}
