<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $table = 'product_reviews';
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'advantages',
        'disadvantages',
        'comment',
        'name',
        'is_name_hidden',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answer()
    {
        return $this->hasOne(ProductReviewAnswer::class, 'product_review_id');
    }
}
