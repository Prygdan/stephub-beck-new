<?php

namespace App\Http\Requests\ProductReview;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
            'name'           => ['nullable', 'string', 'max:255'],
            'rating'         => ['required', 'integer', 'min:1', 'max:5'],
            'advantages'     => ['nullable', 'string', 'max:1000'],
            'disadvantages'  => ['nullable', 'string', 'max:1000'],
            'comment'        => ['required', 'string', 'min:10', 'max:1000'],
            'is_name_hidden' => ['boolean'],
        ];
    }
}
