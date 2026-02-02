<?php

namespace App\Http\Requests\Order;

use App\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'                      => ['required', 'string'],
            'surname'                   => ['required', 'string'],
            'phone'                     => ['required', 'string', 'regex:/^\+380\d{9}$/'],
            'area'                      => ['required', 'string'],
            'area_ref'                  => ['required', 'string'],
            'city'                      => ['required', 'string'],
            'city_ref'                  => ['required', 'string'],
            'middle_name'               => ['nullable', 'string'],
            'branch'                    => ['nullable', 'string'],
            'branch_ref'                => ['nullable', 'string'],
            'postomat'                  => ['nullable', 'string'],
            'postomat_ref'              => ['nullable', 'string'],
            'comment'                   => ['nullable', 'string'],
            'payment_method'            => ['required', Rule::in(PaymentMethod::values())],
            'products'                  => ['required', 'array'],
            'products.*.product_id'     => ['required', 'exists:products,id'],
            'products.*.size_id'        => ['nullable', 'exists:sizes,id'],
            'products.*.quantity'       => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Add custom validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $branch = $this->input('branch');
            $postomat = $this->input('postomat');

            if (!$branch && !$postomat) {
                $validator->errors()->add('branch', 'Ви повинні вибрати або відділення, або поштомат.');
                $validator->errors()->add('postomat', 'Ви повинні вибрати або відділення, або поштомат.');
            }
        });
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name'                  => 'Ім’я',
            'surname'               => 'Прізвище',
            'middle_name'           => 'По-батькові',
            'phone'                 => 'Телефон',
            'area'                  => 'Область',
            'area_ref'              => 'Код області',
            'city'                  => 'Місто',
            'city_ref'              => 'Код міста',
            'branch'                => 'Відділення',
            'branch_ref'            => 'Код відділення',
            'postomat'              => 'Поштомат',
            'postomat_ref'          => 'Код поштомату',
            'products'              => 'Товари',
            'products.*.product_id' => 'ID товару',
            'products.*.size_id'    => 'Розмір',
            'products.*.quantity'   => 'Кількість товару',
            'products.*.price'      => 'Ціна товару',
            'payment_method'        => 'Оплата'
        ];
    }
}
