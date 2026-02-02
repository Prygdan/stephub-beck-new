<?php

namespace App\Http\Requests\Order;

use Illuminate\Validation\Rule;

class Update extends Store
{
  public function rules(): array
  {
    $rules = parent::rules();
    $rules['status'] = ['required', 'numeric', Rule::in(array_keys(config('order_statuses')))];

    return $rules;
  }
}
