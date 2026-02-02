<?php

namespace App\Http\Requests\Page;

use App\Models\Page;
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
            'slug'      =>  ['required', 'min:2', 'max:256', $this->slugUniqueRule()],
            'title'     =>  ['required', 'string', 'min:2', 'max:256'],
            'content'   =>  ['nullable', 'string']
        ];
    }

    public function attributes()
    {
        return [
            'slug'      =>  'URL',
            'title'     =>  'Заголовок',
            'content'   =>  'Контент'
        ];
    }

    protected function slugUniqueRule() 
    {
        return Rule::unique(Page::class, 'slug');
    }
}
