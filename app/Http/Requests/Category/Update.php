<?php

namespace App\Http\Requests\Category;

class Update extends Store
{
    protected function nameUniqueRule() 
    {
        return parent::nameUniqueRule()->ignore($this->category->id);
    }
}
