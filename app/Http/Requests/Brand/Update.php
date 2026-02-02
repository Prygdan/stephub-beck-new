<?php

namespace App\Http\Requests\Brand;

class Update extends Store
{
    protected function brandUniqueRule() 
    {
        return parent::brandUniqueRule()->ignore($this->brand->id);
    }
}
