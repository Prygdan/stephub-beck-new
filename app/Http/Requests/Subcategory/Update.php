<?php

namespace App\Http\Requests\Subcategory;

class Update extends Store
{
    protected function nameUniqueRule() 
    {
        return parent::nameUniqueRule()->ignore($this->subcategory->id);
    }
}