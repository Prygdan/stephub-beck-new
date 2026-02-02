<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Product extends Model
{
    use Sluggable;

    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function calculateDiscountedPrice($discount)
    {
        return !empty($discount) ? round($this->price * (1 - $discount / 100), 2) : null;
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }    
}
