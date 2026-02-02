<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselItem extends Model
{
    protected $guarded = [];
    
    public function carousel()
    {
        return $this->belongsTo(Carousel::class);
    }
}
