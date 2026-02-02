<?php

namespace App\Http\Requests\Size;

class Update extends Store
{
    protected function sizeUniqueRule()
    {
        return parent::sizeUniqueRule()->ignore($this->size->id);
    }
}
