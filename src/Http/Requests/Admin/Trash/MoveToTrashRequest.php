<?php


namespace Fooino\Core\Http\Requests\Admin\Trash;

use Illuminate\Foundation\Http\FormRequest;

class MoveToTrashRequest extends FormRequest
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
        ];
    }
}
