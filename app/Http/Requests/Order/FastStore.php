<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class FastStore extends FormRequest
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
            'name'                      => ['required', 'string'],
            'surname'                   => ['required', 'string'],
            'phone'                     => ['required', 'string', 'regex:/^\+380\d{9}$/'],
            'products'                  => ['required', 'array'],
            'products.*.product_id'     => ['required', 'exists:products,id'],
            'products.*.size_id'        => ['required', 'exists:sizes,id'],
            'products.*.quantity'       => ['required', 'integer', 'min:1'],
            'products.*.price'          => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                      => 'Ім’я',
            'surname'                   => 'Прізвище',
            'phone'                     => 'Телефон',
            'products.*.size_id'        => 'Розмір',
            'products.*.quantity'       => 'Кількість',
            'products.*.price'          => 'Ціна',
        ];
    }
}
