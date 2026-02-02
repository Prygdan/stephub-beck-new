<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(CarouselItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
