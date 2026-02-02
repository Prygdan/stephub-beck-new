<?php

namespace App\Http\Requests\Season;

class Update extends Store
{
    protected function nameUniqueRule() 
    {
        return parent::nameUniqueRule()->ignore($this->season->id);
    }
}
