<?php

namespace App\Http\Requests\Page;

use App\Models\Page;
use Illuminate\Validation\Rule;

class Update extends Store {
    protected function slugUniqueRule()
    {
        return Rule::unique(Page::class, 'slug')->ignore($this->slug, 'slug');
    }
}
