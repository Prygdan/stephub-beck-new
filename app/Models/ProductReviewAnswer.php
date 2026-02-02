<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReviewAnswer extends Model
{
    protected $table = 'reviews_answers'; 
    protected $guarded = [];

    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'product_review_id');
    }
}
