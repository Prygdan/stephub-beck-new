<?php

namespace App\Http\Requests\Brand;

use App\Models\Brand;
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
            'name'              =>  ['required', 'string', $this->brandUniqueRule()],
            'image'             =>  ['nullable', 'regex:/^data:image\/(jpeg|png|jpg);base64,/'],
            'in_popular'        =>  ['nullable', 'boolean'],
            'allowed_filters'   =>  ['nullable', 'array'],
            'allowed_filters.*' =>  ['string'],
        ];
    }

    public function attributes()
    {
        return [
            'name'              => 'Назва',
            'image'             => 'Зображення',
            'in_popular'        => 'В списку популярні',
            'allowed_filters'   =>  'Пункти фільтрації',
        ];
    }
    
    protected function brandUniqueRule() 
    {
        return Rule::unique(Brand::class, 'name');
    }
}
