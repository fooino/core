<?php

namespace Fooino\Core\Http\Requests\Admin\Language;

use Fooino\Core\Enums\{
    Direction,
    LanguageStatus
};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search'                => [
                'nullable',
                'max:255'
            ],
            'status'                => [
                'nullable',
                Rule::in(LanguageStatus::values()),
            ],
            'direction'             => [
                'nullable',
                Rule::in(Direction::values()),
            ]
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $data = [
            'search'                => emptyToNullOrValue($this->search),
            'status'                => emptyToNullOrValue($this->status),
            'direction'             => emptyToNullOrValue($this->direction),
        ];
        $this->merge($data);
        request()->merge($data);
    }
}
