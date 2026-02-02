<?php

namespace App\Http\Requests\ProductReviewAnswer;

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
      'product_review_id'     =>  ['required', 'exists:product_reviews,id'],
      'content'               =>  ['required', 'string', 'min:3', 'max:1000']
    ];
  }

  /**
   * Get custom attribute names.
   */
  public function attributes(): array
  {
    return [
      'product_review_id'     =>  'Коментар',
      'content'               =>  'Відповідь'
    ];
  }
}
