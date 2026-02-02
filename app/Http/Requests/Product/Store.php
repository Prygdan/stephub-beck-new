<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

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
            'category_id'        => ['required', 'exists:categories,id'],
            'subcategory_id'     => ['required', 'exists:subcategories,id'],
            'brand_id'           => ['nullable', 'exists:brands,id'],
            'season_id'          => ['nullable', 'exists:seasons,id'],
            'material_id'        => ['nullable', 'exists:materials,id'],

            'name'               => ['required', 'max:256', $this->nameUniqueRule()],
            'article'            => ['required', 'max:256', $this->articleUniqueRule()],
            'description'        => ['required', 'string'],

            'price'              => ['required', 'numeric', 'min:1'],
            'discount'           => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active'          => ['nullable', 'boolean'],

            'sizes'              => ['nullable', 'array'],
            'sizes.*'            => ['exists:sizes,id'],

            'meta_title'         => ['nullable', 'string'],
            'meta_description'   => ['nullable', 'string'],
            'meta_keywords'      => ['nullable', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'category_id'        => 'Категорія',
            'subcategory_id'     => 'Підкатегорія',
            'brand_id'           => 'Бренд',
            'season_id'          => 'Сезон',
            'material_id'        => 'Матеріал',

            'name'               => 'Назва',
            'article'            => 'Артикул',
            'description'        => 'Опис',

            'price'              =>  'Ціна',
            'discount'           =>  'Знижка',
            'is_active'          =>  'Статус',

            'sizes'              =>  'Розміри',

            'meta_title'         =>  'Title',
            'meta_description'   =>  'Description',
            'meta_keywords'      =>  'Keywords',
        ];
    }

    protected function nameUniqueRule() 
    {
        return Rule::unique(Product::class, 'name');
    }

    protected function articleUniqueRule() 
    {
        return Rule::unique(Product::class, 'article');
    }
}
