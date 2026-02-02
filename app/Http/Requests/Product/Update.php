<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Validation\Rule;

class Update extends Store {
    protected function nameUniqueRule()
    {
        return Rule::unique(Product::class, 'name')->ignore($this->name, 'name');
    }

    protected function articleUniqueRule() 
    {
        return Rule::unique(Product::class, 'article')->ignore($this->article, 'article');
    }
}
