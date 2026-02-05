<?php

namespace App\Http\Requests\Subcategory;

use App\Models\Subcategory;
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
            'name'              =>  ['required', 'string', $this->nameUniqueRule()],
            'category_id'       =>  ['required', 'integer', 'exists:categories,id'],
            'description'       =>  ['nullable', 'string'],

            'allowed_filters'   =>  ['nullable', 'array'],
            'allowed_filters.*' =>  ['string'],

            'meta_title'        =>  ['nullable', 'string'],
            'meta_description'  =>  ['nullable', 'string'],
            'meta_keywords'     =>  ['nullable', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'name'              =>  'Назва',
            'category_id'       =>  'Категорія',
            'description'       =>  'Текст',

            'allowed_filters'   =>  'Пункти фільтрації',

            'meta_title'        =>  'Title',
            'meta_description'  =>  'Description',
            'meta_keywords'     =>  'Keywords',
        ];
    }
    
    protected function nameUniqueRule() 
    {
        return Rule::unique('subcategories', 'name')->where(function ($query) {
            return $query->where('category_id', $this->category_id);
        });
    }
}
