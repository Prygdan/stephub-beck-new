<?php

namespace App\Http\Requests\UploadProductsImage;

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
            'image'         => ['string', 'regex:/^data:image\/(jpeg|png|webp);base64,/'],
            'product_id'    => ['required', 'integer', 'exists:products,id'], 
        ];
    }

    public function attributes()
    {
        return [
            'image'         =>  'Зображення',
            'product_id'    =>  'Товар'
        ];
    }
}
