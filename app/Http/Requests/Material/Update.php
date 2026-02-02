<?php

namespace App\Http\Requests\Material;

class Update extends Store
{
    protected function nameUniqueRule() 
    {
        return parent::nameUniqueRule()->ignore($this->material->id);
    }
}
