<?php

namespace App\Http\Requests\Carousel;

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
            'category_id'           =>  ['nullable', 'exists:categories,id', 'unique:carousels'],
            'items'                 =>  ['required','array'],
            'items.*.image'         =>  ['string', 'regex:/^data:image\/(jpeg|png|webp);base64,/'],
            'items.*.image_mobile'  =>  ['nullable','string', 'regex:/^data:image\/(jpeg|png|webp);base64,/'],
        ];
    }
}
