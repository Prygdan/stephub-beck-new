<?php

namespace App\Models\Order;

use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {
    protected $fillable = ['cart_id', 'product_id', 'size_id', 'quantity', 'price'];

    public function cart() 
    {
        return $this->belongsTo(Cart::class);
    }

    public function product() 
    {
        return $this->belongsTo(Product::class);
    }

    public function size() 
    {
        return $this->belongsTo(Size::class);
    }
}
