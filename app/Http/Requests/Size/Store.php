<?php

namespace App\Http\Requests\Size;

use App\Models\Size;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Store extends FormRequest
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
            'value_eu'  =>  ['required', 'string', 'max:5', $this->sizeUniqueRule()],
            'value_cm'  =>  ['nullable', 'string', 'max:5'],
        ];
    }

    public function attributes()
    {
        return [
            'type'          =>  'Тип',
            'valueEURO'     =>  'Розмір',
            'valueCM'       =>  'Європейський розмір'
        ];
    }

    protected function sizeUniqueRule() 
    {
        return Rule::unique(Size::class, 'value_eu');
    }
}
